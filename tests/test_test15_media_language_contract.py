#!/usr/bin/env python3
from pathlib import Path

ROOT=Path(__file__).resolve().parents[1]
src=ROOT/"platform/frontend/src"
pkg=(ROOT/"platform/frontend/package.json").read_text(encoding="utf-8")
carousel=(src/"features/catalog/components/ProductMediaCarousel.vue").read_text(encoding="utf-8")
card=(src/"features/catalog/components/ProductCard.vue").read_text(encoding="utf-8")
locale=(src/"stores/locale.ts").read_text(encoding="utf-8")
geo=(src/"services/localeDetection.ts").read_text(encoding="utf-8")
css=(src/"styles/main.css").read_text(encoding="utf-8")
fetcher=(ROOT/"platform/frontend/scripts/fetch-digikala-assets.mjs").read_text(encoding="utf-8")
catalog=(src/"data/catalog.ts").read_text(encoding="utf-8")
icon=(ROOT/"platform/frontend/public/icons/whatsapp.svg").read_text(encoding="utf-8")
launcher=(ROOT/"t/index.htm").read_text(encoding="utf-8")
immutable=(ROOT/"docs/IMMUTABLE-TESTS.txt").read_text(encoding="utf-8").splitlines()

assert '"photoswipe"' in pkg
assert "PhotoSwipeLightbox" in carousel
assert "closeOnVerticalDrag: true" in carousel
assert "pinchToClose: true" in carousel
assert "bgClickAction: 'close'" in carousel
assert "IntersectionObserver" in carousel
assert "prefers-reduced-motion" in carousel
assert "visibilitychange" in carousel
assert "product.gallery" in card
assert "WhatsAppIcon" in card
assert "favorite-heart" in card
assert "#25D366" in icon
assert "fill="#25D366"" in icon
assert "border-radius:.25rem" in css
assert "@keyframes favorite-heartbeat" in css
assert "api.digikala.com/v1/search/" in fetcher
assert "api.digikala.com/v2/product/" in fetcher
assert "requiredPerProduct = 2" in fetcher
assert "digikalaMediaByProductCode" in catalog
assert "ipwho.is" in geo
for code in ["fa","ar","en","ku"]:
    assert f"'{code}'" in locale
for region in ["Kurdistan","Kermanshah","Ilam","Erbil","Sulaymaniyah","Duhok","Halabja"]:
    assert region in geo
assert "14" in {line.strip() for line in immutable}
assert launcher.index("./15/index.html") < launcher.index("./14/index.html")
print("Test 15 media/language contract: PASS")
