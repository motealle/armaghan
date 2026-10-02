"""Promote an existing numbered UI at root without rebuilding or changing /t."""
from __future__ import annotations
import hashlib
import io
import json
import os
from pathlib import Path
import re
from ftplib import FTP, error_perm
from html.parser import HTMLParser
from urllib.parse import urljoin, urlparse
from urllib.request import Request, urlopen

SITE = "https://armaghantrading.com"

def version_path(value: str) -> str:
    if not isinstance(value, str) or not re.fullmatch(r"(?:0[1-9]|[1-9][0-9]*)", value):
        raise ValueError("Version must be a positive numbered test folder")
    return f"/t/{value}/"

def render_root(source: bytes, version: str) -> bytes:
    base = version_path(version)
    html = source.decode("utf-8-sig")
    if not re.search(r"<head\b[^>]*>", html, re.I):
        raise ValueError("Selected version has no HTML head")
    # Replace any existing base, then put our base before every relative reference.
    html = re.sub(r"<base\b[^>]*>", "", html, flags=re.I)
    additions = f'<base href="{base}">\n<meta name="armaghan-root-test" content="{version}">'
    html = re.sub(r"(<head\b[^>]*>)", lambda m: m.group(1)+"\n"+additions, html, count=1, flags=re.I)
    return html.encode("utf-8")

class AssetParser(HTMLParser):
    def __init__(self):
        super().__init__()
        self.paths = []
    def handle_starttag(self, tag, attrs):
        attrs = dict(attrs)
        value = attrs.get("src") if tag == "script" else attrs.get("href") if tag == "link" and attrs.get("rel") == "stylesheet" else None
        if value and not urlparse(value).scheme and not value.startswith("//"):
            self.paths.append(value)

def read_ftp(ftp, path):
    output = io.BytesIO()
    ftp.retrbinary("RETR "+path, output.write)
    return output.getvalue()

def source_index(ftp, version):
    prefix = "/public_html"+version_path(version)
    for name in ("index.html", "index.htm"):
        try:
            return read_ftp(ftp, prefix+name)
        except error_perm as error:
            if not str(error).startswith("550"):
                raise
    raise ValueError("Selected version is not deployed")

def asset_paths(source, version):
    parser = AssetParser()
    parser.feed(source.decode("utf-8-sig"))
    base = version_path(version)
    paths = []
    for asset in parser.paths:
        url = urlparse(urljoin(SITE+base, asset))
        if url.netloc != urlparse(SITE).netloc or not url.path.startswith("/t/"):
            raise ValueError("Selected version references an asset outside its folder")
        if ".." in url.path.split("/"):
            raise ValueError("Invalid asset traversal")
        paths.append(url.path)
    return list(dict.fromkeys(paths))

def http_bytes(path, digest):
    with urlopen(Request(SITE+path+"?release="+digest[:16], headers={"Cache-Control":"no-cache"}), timeout=30) as response:
        if urlparse(response.url).netloc != urlparse(SITE).netloc:
            raise ValueError("Unexpected HTTP origin")
        return response.read()

def promote(ftp, version, fetch=http_bytes):
    source = source_index(ftp, version)
    root = render_root(source, version)
    assets = [(path, read_ftp(ftp, "/public_html"+path)) for path in asset_paths(source, version)]
    # Check selected assets are already live before changing root.
    for path, expected in assets:
        digest = hashlib.sha256(expected).hexdigest()
        if fetch(path, digest) != expected:
            raise ValueError("Published selected asset does not match FTP source")
    previous = read_ftp(ftp, "/public_html/index.html")
    history = "/public_html/t/_root-history"
    try:
        ftp.mkd(history)
    except error_perm as error:
        if not str(error).startswith("550"):
            raise
    previous_digest = hashlib.sha256(previous).hexdigest()
    ftp.storbinary("STOR "+history+"/"+previous_digest+".html", io.BytesIO(previous))
    digest = hashlib.sha256(root).hexdigest()
    temporary = "/public_html/.armaghan-root-"+digest[:16]+".html"
    ftp.storbinary("STOR "+temporary, io.BytesIO(root))
    ftp.rename(temporary, "/public_html/index.html")
    try:
        if fetch("/", digest) != root:
            raise ValueError("Published root does not match selected release")
    except Exception:
        # Restore the previous public entry if post-publication verification fails.
        ftp.storbinary("STOR /public_html/index.html", io.BytesIO(previous))
        raise
    print(f"Root version {version}: HTML and selected assets verified PASS; /t unchanged")

def main():
    config = json.loads(Path("config/root-release.json").read_text())
    if config.get("schema") != 1:
        raise ValueError("Unsupported release selector schema")
    version = os.environ.get("ROOT_RELEASE_VERSION", "").strip() or config["version"]
    version_path(version)
    raw = os.environ["FTP_SERVER"].strip()
    parsed = urlparse(raw) if "://" in raw else None
    host = parsed.hostname if parsed else raw
    port = (parsed.port or 21) if parsed else 21
    ftp = FTP()
    try:
        ftp.connect(host, port, timeout=30)
        ftp.login(os.environ["FTP_USERNAME"], os.environ["FTP_PASSWORD"])
        if ftp.pwd() != "/":
            raise ValueError("FTP account root differs from verified hosting contract")
        promote(ftp, version)
    finally:
        ftp.close()

if __name__ == "__main__":
    main()
