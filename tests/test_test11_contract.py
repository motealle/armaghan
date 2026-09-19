#!/usr/bin/env python3
from pathlib import Path
import re

root=Path(__file__).resolve().parents[1]
html=(root/"t/11/index.htm").read_text(encoding="utf-8")
js=(root/"t/11/assets/app.js").read_text(encoding="utf-8")
css=(root/"t/11/assets/app.css").read_text(encoding="utf-8")
launcher=(root/"t/index.htm").read_text(encoding="utf-8")

assert "tailwindcss" in html.lower(), "Test 11 must use Tailwind"
assert "lucide" in html.lower(), "Test 11 must use the approved icon family"
for color in ["#151EDA","#21946A","#C8E3DB","#FFFEFF","#FFB514"]:
    assert color.lower() in (html+css+js).lower(), f"Missing palette anchor {color}"
for marker in ["Armaghan Core","Royal Commerce","Soft Mint Studio","Executive Dark","Editorial Luxe"]:
    assert marker in js, f"Missing design system: {marker}"
for marker in ["Brand Balanced","Deep Royal","Mint Commerce","Dark Premium","Clean Light"]:
    assert marker in js, f"Missing color set: {marker}"
assert "3000" in js and "long" in js.lower(), "3-second long-press design lab is required"
assert "username === '1'" in js and "password === '1'" in js, "Admin demo login missing"
assert "username === '2'" in js and "password === '2'" in js, "Customer demo login missing"
assert ".skeleton" in css and "@keyframes shimmer" in css, "Shimmer skeleton missing"
assert not re.search(r"(^|[,{\s])svg\s*\{[^}]*width\s*:\s*100%", css, re.M|re.S), "Do not globally force SVG width"
assert launcher.index("./11/index.htm") < launcher.index("./10/index.htm"), "Newest test must be first"
print("Test 11 static contract: PASS")
