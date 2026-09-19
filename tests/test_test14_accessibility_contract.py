from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
app = (ROOT / "platform/frontend/src/App.vue").read_text(encoding="utf-8")
css = (ROOT / "platform/frontend/src/styles/main.css").read_text(encoding="utf-8")
vite = (ROOT / "platform/frontend/vite.config.ts").read_text(encoding="utf-8")

assert 'class="skip-link"' in app
assert 'href="#main-content"' in app
assert 'id="main-content"' in app
assert 'tabindex="-1"' in app
assert "route.fullPath" in app and "focus({preventScroll:true})" in app
assert ".skip-link:focus" in css
assert "prefers-reduced-motion:reduce" in css
assert "../../t/14" in vite

print("Test 14 accessibility contract: PASS")
