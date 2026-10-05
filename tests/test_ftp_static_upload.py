import tempfile
import unittest
from pathlib import Path
import sys
sys.path.insert(0, str(Path(__file__).resolve().parents[1] / 'platform/scripts'))
from ftp_static_upload import upload_static_file
from ftplib import error_perm
from unittest.mock import patch
import io
from ftp_static_upload import published_matches


class FakeFTP:
    def __init__(self, error=None):
        self.error = error
        self.bytes = b''
        self.directory = None
        self.closed = False

    def cwd(self, directory):
        self.directory = directory

    def storbinary(self, command, source):
        self.command = command
        self.bytes = source.read(3 if self.error else -1)
        if self.error:
            raise self.error

    def close(self):
        self.closed = True

    def rename(self, source, destination):
        self.renamed = (source, destination)


class RecoveryTest(unittest.TestCase):
    def test_reconnect_replaces_partial_file_from_byte_zero(self):
        with tempfile.TemporaryDirectory() as folder:
            path = Path(folder) / 'asset.bin'; path.write_bytes(b'complete asset')
            failed = FakeFTP(TimeoutError('timeout')); fresh = FakeFTP()
            result = upload_static_file(failed, lambda: fresh, '/public_html/t/29/assets', 'asset.bin', path)
            self.assertIs(result, fresh); self.assertTrue(failed.closed)
            self.assertEqual(fresh.bytes, b'complete asset')
            self.assertEqual(fresh.directory, '/public_html/t/29/assets')
            self.assertEqual(failed.command, 'STOR .asset.bin.armaghan-upload-1')
            self.assertEqual(fresh.command, 'STOR .asset.bin.armaghan-upload-2')
            self.assertFalse(hasattr(failed, 'renamed'))
            self.assertEqual(fresh.renamed, ('.asset.bin.armaghan-upload-2', 'asset.bin'))

    def test_skips_only_exact_published_bytes(self):
        with tempfile.TemporaryDirectory() as folder:
            path = Path(folder) / 'asset.bin'; path.write_bytes(b'complete')
            for remote, expected in [(b'complete', True), (b'com', False), (b'complete extra', False)]:
                with patch('ftp_static_upload.urlopen', return_value=io.BytesIO(remote)):
                    self.assertEqual(published_matches('https://example.test/t/29/asset.bin', path), expected)
            with patch('ftp_static_upload.urlopen', side_effect=TimeoutError('timeout')):
                self.assertFalse(published_matches('https://example.test/t/29/asset.bin', path))

    def test_recovery_is_bounded(self):
        with tempfile.TemporaryDirectory() as folder:
            path = Path(folder) / 'asset.bin'; path.write_bytes(b'asset')
            connections = []
            def connect():
                row = FakeFTP(TimeoutError('timeout')); connections.append(row); return row
            with self.assertRaises(TimeoutError):
                upload_static_file(connect(), connect, '/public_html/t/29', 'asset.bin', path)
            self.assertEqual(len(connections), 3)
            self.assertTrue(all(row.closed for row in connections))

    def test_permission_failure_is_not_retried(self):
        with tempfile.TemporaryDirectory() as folder:
            path = Path(folder) / 'asset.bin'; path.write_bytes(b'asset')
            def forbidden_connect():
                self.fail('permission failures must not reconnect')
            with self.assertRaises(error_perm):
                upload_static_file(FakeFTP(error_perm('permission')), forbidden_connect, '/public_html/t/29', 'asset.bin', path)


if __name__ == '__main__':
    unittest.main()
