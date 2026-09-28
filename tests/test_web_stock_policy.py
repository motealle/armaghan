#!/usr/bin/env python3
from pathlib import Path
import json
import re

root = Path(__file__).resolve().parents[1]
cfg_path = root / "platform" / "assets" / "web-stock" / "queries.json"
cfg = json.loads(cfg_path.read_text(encoding="utf-8"))
assets = cfg.get("assets", [])
assert len(assets) >= 10, "Web-stock bootstrap should cover hero/category/product/trust surfaces"
allowed = tuple(cfg.get("allowed_licenses", []))
assert {"CC BY", "CC BY-SA", "CC0", "Public domain"}.issubset(set(allowed))
outputs = [item["output"] for item in assets]
assert len(outputs) == len(set(outputs)), "Web-stock output filenames must be unique"
for item in assets:
    assert item["title"].startswith("File:"), f"Use deterministic Commons file titles: {item}"
    assert item["output"].endswith(".webp"), f"Vendored derivatives must be WebP: {item}"
    assert item["aspect"] in {"16:9","4:3","3:2","1:1"}

src = root / "platform" / "frontend" / "src"
test26_requirements = (root / "docs" / "TEST26-IMAGE-REQUIREMENTS.md").read_text(encoding="utf-8")
test26_placeholder_registry = src / "data" / "home26.ts"
for path in src.rglob("*"):
    if path.suffix not in {".vue", ".ts", ".css"}:
        continue
    text = path.read_text(encoding="utf-8")
    runtime_urls = re.findall(r'https?://[^"\'\s]+\.(?:jpe?g|png|webp|avif)(?:\?[^"\'\s]*)?', text, re.I)
    if not runtime_urls:
        continue
    if path == test26_placeholder_registry:
        for url in runtime_urls:
            assert url.startswith("https://placehold.co/"), f"Only documented placehold.co prototype fallbacks are allowed in {path}: {url}"
            assert url in test26_requirements, f"Undocumented Test 26 placeholder URL: {url}"
        continue
    raise AssertionError(f"Runtime hotlinked image found in {path}: {runtime_urls[0]}")

manifest = root / "platform" / "assets" / "web-stock" / "manifest.json"
if manifest.exists():
    data = json.loads(manifest.read_text(encoding="utf-8"))
    records = data.get("assets", [])
    assert len(records) == len(assets), "Manifest must cover every configured stock slot"
    for record in records:
        license_name = record.get("license", "")
        assert license_name and "NC" not in license_name.upper() and "ND" not in license_name.upper()
        assert record.get("source_page", "").startswith("http")
        assert record.get("creator")
        assert record.get("sha256")
        local = root / "platform" / "frontend" / "public" / "images" / "web-stock" / record["file"]
        assert local.exists(), f"Missing vendored file: {local}"

print("Web-stock policy: PASS")
