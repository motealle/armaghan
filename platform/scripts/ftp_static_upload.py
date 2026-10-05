"""Bounded recovery for idempotent static-file replacement, never application POSTs."""
from ftplib import error_temp
from pathlib import Path
from urllib.request import Request, urlopen
from urllib.error import URLError
import hashlib


def published_matches(url, local_path):
    expected = Path(local_path).read_bytes()
    digest = hashlib.sha256(expected).hexdigest()
    try:
        with urlopen(Request(url + '?static_sha=' + digest, headers={'Cache-Control': 'no-cache'}), timeout=15) as response:
            # Bounded read; an oversized response is never treated as equal.
            return response.read(len(expected) + 1) == expected
    except (OSError, URLError):
        return False


def upload_static_file(ftp, connect, remote_dir, filename, local_path, attempts=3):
    for attempt in range(attempts):
        try:
            # Reopen from byte zero: a prior timeout may have written only a prefix.
            ftp.cwd(remote_dir)
            # A shared host can leave a timed-out temporary STOR target stuck/locked.
            # Use a fresh bounded temp name on every retry, then atomically rename.
            temp_name = f".{filename}.armaghan-upload-{attempt + 1}"
            with open(local_path, "rb") as source:
                ftp.storbinary(f"STOR {temp_name}", source)
            ftp.rename(temp_name, filename)
            return ftp
        except (OSError, EOFError, error_temp):
            ftp.close()
            if attempt + 1 == attempts:
                raise
            ftp = connect()
    raise RuntimeError("Static upload attempts exhausted")
