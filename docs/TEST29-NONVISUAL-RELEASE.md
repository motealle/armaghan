# Test29 — independent release and automated acceptance

## Test29 — nonvisual release, 2026-10-02

Owner instruction: defer visual testing; preserve all previous versions; create a new numbered folder and prepend its link.
- Tests 01–28 are frozen. Test28 source checkpoint: `snapshot/test28-final` at `1fafd9638777e26796acedc60b27dfd545736794`.
- Test29 is the active release lane. Build/deploy target: `/t/29`; link `./29/index.html` is first in the launcher.
- Build and FTP boundaries refuse targets <=28. Root deployment and remote deletion remain prohibited for this run.
- Test29 browser state uses `armaghan:test29:*`, including the formerly shared manual-language setting. No migration writes previous-version keys; production Backend sessions/catalog remain intentionally shared.
- Localized Why Armaghan eyebrow defaults for Persian, Arabic and Sorani; English retained. Existing locale/text overrides retain priority.
- Three new automated locale tests cover old-key preservation, new-version reload and four-language defaults. The dark-heading numeric guard is carried forward.
- Visual/device/admin-session acceptance remains OPEN and deferred by owner instruction. Automated PASS must not be presented as visual acceptance.
- First Test29 build/deployment verification: pending in this implementation commit; record actual result during closeout.

| Rank | Method | Score | Reason |
|---:|---|---:|---|
| 1 | Isolated Test29 + localized defaults + automated freeze/storage contracts | 9.5 | Concrete release without visual-layout changes; protects old snapshots |
| 2 | Copy current release into a new lane only | 8.0 | Safe but fewer functional corrections |
| 3 | Documentation-only closeout | 6.0 | Does not deliver a new version |
| 4 | Add more admin capabilities | 4.0 | Core capabilities already exist |
| 5 | Layout redesign | 2.0 | Requires deferred visual testing |

Selected: option 1. Storage rationale: https://developer.mozilla.org/en-US/docs/Web/API/Web_Storage_API/Using_the_Web_Storage_API .

Nonvisual deployment evidence gate: compare SHA-256 of published index/initial JS/CSS to the built artifact, refuse assets outside /t/29, and verify the published launcher has Test29 first. No visual browser interaction is performed.
