#!/usr/bin/env python3
"""Vendor licensed Wikimedia Commons imagery into the host-local frontend asset tree."""
from __future__ import annotations

import hashlib
import html
import io
import json
import re
import sys
from datetime import datetime, timezone
from pathlib import Path
from urllib.parse import urlencode
from urllib.request import Request, urlopen

from PIL import Image, ImageOps

ROOT = Path(__file__).resolve().parents[2]
CONFIG = ROOT / "platform" / "assets" / "web-stock" / "queries.json"
MANIFEST = ROOT / "platform" / "assets" / "web-stock" / "manifest.json"
PUBLIC = ROOT / "platform" / "frontend" / "public" / "images" / "web-stock"
CREDITS = PUBLIC / "credits.json"
API = "https://commons.wikimedia.org/w/api.php"
MAX_DOWNLOAD = 20 * 1024 * 1024

TAG_RE = re.compile(r"<[^>]+>")

def clean(value: str | None) -> str:
    return html.unescape(TAG_RE.sub("", value or "")).strip()

def ext(meta: dict, key: str) -> str:
    item = meta.get(key) or {}
    return clean(item.get("value"))

def api_json(params: dict[str, str], user_agent: str) -> dict:
    url = API + "?" + urlencode(params)
    req = Request(url, headers={"User-Agent": user_agent, "Accept": "application/json"})
    with urlopen(req, timeout=30) as response:
        return json.load(response)

def get_file_info(title: str, width: int, user_agent: str) -> dict:
    payload = api_json({
        "action": "query",
        "format": "json",
        "formatversion": "2",
        "prop": "imageinfo",
        "titles": title,
        "iiprop": "url|mime|size|extmetadata",
        "iiurlwidth": str(width),
    }, user_agent)
    pages = payload.get("query", {}).get("pages", [])
    if not pages or pages[0].get("missing"):
        raise RuntimeError(f"Commons file not found: {title}")
    info = (pages[0].get("imageinfo") or [None])[0]
    if not info:
        raise RuntimeError(f"No imageinfo for: {title}")
    info["_page_title"] = pages[0].get("title", title)
    return info

def allowed_license(short_name: str, allowed: list[str]) -> bool:
    normalized = short_name.strip().lower()
    if not normalized or "noncommercial" in normalized or "-nc" in normalized or "no derivatives" in normalized or "-nd" in normalized:
        return False
    return any(normalized.startswith(item.lower()) for item in allowed)

def download(url: str, user_agent: str) -> bytes:
    req = Request(url, headers={"User-Agent": user_agent, "Accept": "image/*"})
    with urlopen(req, timeout=45) as response:
        data = response.read(MAX_DOWNLOAD + 1)
    if len(data) > MAX_DOWNLOAD:
        raise RuntimeError("Remote image exceeds 20 MiB safety limit")
    return data

def crop_ratio(image: Image.Image, aspect: str) -> Image.Image:
    w, h = [int(v) for v in aspect.split(":")]
    target = w / h
    current = image.width / image.height
    if current > target:
        new_w = round(image.height * target)
        left = (image.width - new_w) // 2
        return image.crop((left, 0, left + new_w, image.height))
    new_h = round(image.width / target)
    top = (image.height - new_h) // 2
    return image.crop((0, top, image.width, top + new_h))

def convert(raw: bytes, output: Path, aspect: str, max_width: int, quality: int) -> tuple[int, int, int, str]:
    with Image.open(io.BytesIO(raw)) as source:
        image = ImageOps.exif_transpose(source)
        if image.mode not in ("RGB", "RGBA"):
            image = image.convert("RGB")
        if image.mode == "RGBA":
            background = Image.new("RGB", image.size, "white")
            background.paste(image, mask=image.getchannel("A"))
            image = background
        image = crop_ratio(image, aspect)
        if image.width > max_width:
            height = round(image.height * max_width / image.width)
            image = image.resize((max_width, height), Image.Resampling.LANCZOS)
        # Re-encoding deliberately strips EXIF/GPS/profile metadata.
        output.parent.mkdir(parents=True, exist_ok=True)
        image.save(output, "WEBP", quality=quality, method=6)
    blob = output.read_bytes()
    return image.width, image.height, len(blob), hashlib.sha256(blob).hexdigest()

def main() -> int:
    cfg = json.loads(CONFIG.read_text(encoding="utf-8"))
    PUBLIC.mkdir(parents=True, exist_ok=True)
    records = []
    for item in cfg["assets"]:
        info = get_file_info(item["title"], int(cfg["max_width"]), cfg["user_agent"])
        meta = info.get("extmetadata") or {}
        license_name = ext(meta, "LicenseShortName")
        if not allowed_license(license_name, cfg["allowed_licenses"]):
            raise RuntimeError(f"Rejected license {license_name!r} for {item['title']}")
        mime = info.get("mime", "")
        if mime not in {"image/jpeg", "image/png", "image/webp"}:
            raise RuntimeError(f"Rejected MIME {mime!r} for {item['title']}")
        source_url = info.get("descriptionurl") or info.get("url")
        file_url = info.get("thumburl") or info.get("url")
        if not file_url or not source_url:
            raise RuntimeError(f"Missing URL for {item['title']}")
        raw = download(file_url, cfg["user_agent"])
        output = PUBLIC / item["output"]
        width, height, size, digest = convert(raw, output, item["aspect"], int(cfg["max_width"]), int(cfg["quality"]))
        record = {
            "slot": item["slot"],
            "file": item["output"],
            "commons_title": info["_page_title"],
            "source_page": source_url,
            "creator": ext(meta, "Artist") or ext(meta, "Credit") or "Wikimedia Commons contributor",
            "license": license_name,
            "license_url": ext(meta, "LicenseUrl"),
            "usage_terms": ext(meta, "UsageTerms"),
            "derivative_note": "Downloaded thumbnail; center-cropped/resized; metadata stripped; re-encoded to WebP.",
            "width": width,
            "height": height,
            "bytes": size,
            "sha256": digest,
        }
        records.append(record)
        print(f"Vendored {item['slot']}: {item['output']} ({license_name})")

    manifest = {
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "source": "Wikimedia Commons API",
        "policy": "Temporary licensed stock for prototype; not actual Armaghan product/factory photography.",
        "assets": records,
    }
    MANIFEST.parent.mkdir(parents=True, exist_ok=True)
    MANIFEST.write_text(json.dumps(manifest, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
    CREDITS.write_text(json.dumps(records, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
    if len(records) != len(cfg["assets"]):
        raise RuntimeError("Not all configured assets were vendored")
    return 0

if __name__ == "__main__":
    try:
        raise SystemExit(main())
    except Exception as exc:
        print(f"web-stock vendor failed: {exc}", file=sys.stderr)
        raise
