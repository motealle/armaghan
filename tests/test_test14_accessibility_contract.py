from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
app = (ROOT / "platform/frontend/src/App.vue").read_text(encoding="utf-8")
css = (ROOT / "platform/frontend/src/styles/main.css").read_text(encoding="utf-8")

# Regression contract for capabilities introduced in Test 14.
# Build target belongs to the current iteration and must not be frozen here.
assert 'class="skip-link"' in app
assert 'href="#main-content"' in app
assert 'id="main-content"' in app
assert 'tabindex="-1"' in app
assert "route.fullPath" in app and "focus({preventScroll:true})" in app
assert ".skip-link:focus" in css
assert "prefers-reduced-motion:reduce" in css

print("Test 14 accessibility regression contract: PASS")
