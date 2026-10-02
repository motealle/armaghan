"""Root promotion guards, rollback and immutable version behavior."""
import importlib.util
from pathlib import Path
import unittest
from ftplib import error_perm

spec=importlib.util.spec_from_file_location("promote_root",Path(__file__).resolve().parents[1]/"platform/scripts/promote_root_release.py")
module=importlib.util.module_from_spec(spec)
spec.loader.exec_module(module)
SOURCE=b'<html><head><title>Test29</title><link rel="stylesheet" href="./assets/site.css"><script src="./assets/site.js"></script></head><body><div id="app"></div></body></html>'
OLD=b'<html><head><title>old root</title></head><body>landing</body></html>'

class FakeFTP:
    def __init__(self):
        self.files={"/public_html/index.html":OLD,"/public_html/t/29/index.html":SOURCE,"/public_html/t/29/assets/site.js":b"js","/public_html/t/29/assets/site.css":b"css"}
        self.writes=[]
    def retrbinary(self,command,callback):
        path=command.removeprefix("RETR ")
        if path not in self.files: raise error_perm("550 missing")
        callback(self.files[path])
    def storbinary(self,command,stream):
        path=command.removeprefix("STOR ")
        self.writes.append(path);self.files[path]=stream.read()
    def mkd(self,path): pass
    def rename(self,source,target):
        self.files[target]=self.files.pop(source)
    def fetch(self,path,digest):
        return self.files["/public_html/index.html" if path=="/" else "/public_html"+path]

class RootReleaseTest(unittest.TestCase):
    def test_only_positive_version_folders(self):
        for version in ("../29","29/..","0","00","-1","29?x","29a",""):
            with self.assertRaises(ValueError): module.version_path(version)
        self.assertEqual(module.version_path("01"),"/t/01/")
    def test_replace_base_and_keep_content(self):
        page=module.render_root(SOURCE.replace(b"<head>",b'<head><base href="/wrong/">'),"29").decode()
        self.assertEqual(page.count("<base "),1)
        self.assertIn('<base href="/t/29/">',page)
        self.assertIn('<div id="app">',page)
    def test_success_preserves_previous_numbered_files(self):
        ftp=FakeFTP();before={k:v for k,v in ftp.files.items() if k.startswith("/public_html/t/29/")}
        module.promote(ftp,"29",ftp.fetch)
        self.assertEqual(before,{k:v for k,v in ftp.files.items() if k.startswith("/public_html/t/29/")})
        self.assertTrue(any(k.startswith("/public_html/t/_root-history/") for k in ftp.writes))
        self.assertIn(b'content="29"',ftp.files["/public_html/index.html"])
    def test_missing_version_never_writes(self):
        ftp=FakeFTP()
        with self.assertRaises(ValueError): module.promote(ftp,"28",ftp.fetch)
        self.assertEqual(ftp.writes,[])
    def test_asset_failure_never_changes_root(self):
        ftp=FakeFTP()
        with self.assertRaises(ValueError): module.promote(ftp,"29",lambda *_:b"wrong")
        self.assertEqual(ftp.files["/public_html/index.html"],OLD)
        self.assertEqual(ftp.writes,[])
    def test_root_verification_failure_restores_entry(self):
        ftp=FakeFTP()
        def fetch(path,digest): return b"bad" if path=="/" else ftp.fetch(path,digest)
        with self.assertRaises(ValueError): module.promote(ftp,"29",fetch)
        self.assertEqual(ftp.files["/public_html/index.html"],OLD)
    def test_legacy_index_and_shared_t_assets(self):
        ftp=FakeFTP();ftp.files["/public_html/t/01/index.htm"]=b'<html><head><script src="../shared.js"></script></head></html>'
        ftp.files["/public_html/t/shared.js"]=b"shared"
        module.promote(ftp,"01",ftp.fetch)
        self.assertIn(b'content="01"',ftp.files["/public_html/index.html"])
    def test_reject_non_test_local_assets(self):
        with self.assertRaises(ValueError):module.asset_paths(b'<head><script src="/backend/private.js"></script></head>',"29")

if __name__=="__main__": unittest.main()
