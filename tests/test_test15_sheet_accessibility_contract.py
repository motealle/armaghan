from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
sheet = (ROOT / "platform/frontend/src/components/ui/BaseSheet.vue").read_text(encoding="utf-8")

# Dialog keyboard contract: capture opener, move focus inside, trap Tab, restore opener.
assert 'ref="panelRef"' in sheet
assert 'previousFocus' in sheet
assert 'focusFirstControl' in sheet
assert "event.key !== 'Tab'" in sheet or "event.key === 'Tab'" in sheet
assert 'FOCUSABLE_SELECTOR' in sheet
assert 'previousFocus?.focus' in sheet
assert 'aria-labelledby="sheet-title"' in sheet
assert 'id="sheet-title"' in sheet

print("Test 15 sheet accessibility contract: PASS")
