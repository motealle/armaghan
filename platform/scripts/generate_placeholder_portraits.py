#!/usr/bin/env python3
"""Generate portrait placeholder derivatives without stretching or cropping the source.

Original landscape assets remain untouched under public/images/placeholders.
Portrait derivatives are rebuilt deterministically under public/images/placeholders-portrait.
"""
from __future__ import annotations

from hashlib import sha256
from pathlib import Path
import json

try:
    from PIL import Image, ImageDraw, ImageStat
except ImportError as exc:
    raise SystemExit("Pillow is required: python -m pip install pillow") from exc

ROOT = Path(__file__).resolve().parents[1]
FRONTEND = ROOT / "frontend"
SOURCE = FRONTEND / "public/images/placeholders"
TARGET = FRONTEND / "public/images/placeholders-portrait"
SETS = ("paper-cut", "flat-geometric", "dimensional")
CODES = ("11", "12", "21", "22", "31", "32")
TARGET_SIZE = (960, 1440)

def average_edge(image: Image.Image, top: bool) -> tuple[int, int, int]:
    rgb = image.convert("RGB")
    edge_h = max(2, round(rgb.height * 0.035))
    box = (0, 0, rgb.width, edge_h) if top else (0, rgb.height-edge_h, rgb.width, rgb.height)
    stat = ImageStat.Stat(rgb.crop(box))
    return tuple(round(v) for v in stat.mean[:3])

def mix(a: tuple[int,int,int], b: tuple[int,int,int], t: float) -> tuple[int,int,int]:
    return tuple(round(x + (y-x)*t) for x,y in zip(a,b))

def render(source: Path, target: Path) -> dict[str, object]:
    image = Image.open(source).convert("RGB")
    tw, th = TARGET_SIZE
    scale = min(tw / image.width, th / image.height)
    resized = image.resize((round(image.width*scale), round(image.height*scale)), Image.Resampling.LANCZOS)
    top = average_edge(resized, True)
    bottom = average_edge(resized, False)

    canvas = Image.new("RGB", TARGET_SIZE)
    draw = ImageDraw.Draw(canvas)
    for y in range(th):
        t = y / max(1, th-1)
        draw.line((0, y, tw, y), fill=mix(top, bottom, t))

    x = (tw-resized.width)//2
    y = (th-resized.height)//2
    canvas.paste(resized, (x, y))

    # Feather the extended areas into the source edge colors.
    feather = min(72, max(28, y//3 if y else 28))
    overlay = Image.new("RGBA", TARGET_SIZE, (0,0,0,0))
    odraw = ImageDraw.Draw(overlay)
    for i in range(feather):
        alpha = round(34 * (1 - i/feather))
        if y-i-1 >= 0:
            odraw.line((0, y-i-1, tw, y-i-1), fill=(*top, alpha))
        by = y + resized.height + i
        if by < th:
            odraw.line((0, by, tw, by), fill=(*bottom, alpha))
    composed = Image.alpha_composite(canvas.convert("RGBA"), overlay)

    # Very mild edge vignette: enough to define the media frame, not tint the garment.
    vignette = Image.new("RGBA", TARGET_SIZE, (0,0,0,0))
    vdraw = ImageDraw.Draw(vignette)
    steps = 34
    for i in range(steps):
        alpha = round(7 * (1 - i/steps))
        vdraw.rounded_rectangle((i, i, tw-1-i, th-1-i), radius=44, outline=(18,22,28,alpha), width=2)
    composed = Image.alpha_composite(composed, vignette).convert("RGB")

    target.parent.mkdir(parents=True, exist_ok=True)
    composed.save(target, "WEBP", quality=88, method=6)
    return {
        "source": str(source.relative_to(FRONTEND/"public")).replace("\\","/"),
        "target": str(target.relative_to(FRONTEND/"public")).replace("\\","/"),
        "source_sha256": sha256(source.read_bytes()).hexdigest(),
        "target_sha256": sha256(target.read_bytes()).hexdigest(),
        "bytes": target.stat().st_size,
    }

def main() -> None:
    rows = []
    for set_id in SETS:
        for code in CODES:
            source = SOURCE / set_id / f"sub-{code}.webp"
            if not source.is_file():
                raise SystemExit(f"Missing placeholder source: {source}")
            rows.append(render(source, TARGET / set_id / f"sub-{code}.webp"))

    manifest = {
        "schema": 1,
        "generator": "platform/scripts/generate_placeholder_portraits.py",
        "strategy": "center source; sample top/bottom edge colors; extend vertically; subtle vignette",
        "width": TARGET_SIZE[0],
        "height": TARGET_SIZE[1],
        "items": rows,
    }
    TARGET.mkdir(parents=True, exist_ok=True)
    (TARGET/"manifest.json").write_text(json.dumps(manifest, ensure_ascii=False, indent=2)+"\n", encoding="utf-8")
    print(f"Generated {len(rows)} portrait placeholder derivatives in {TARGET}")

if __name__ == "__main__":
    main()
