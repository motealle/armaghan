# Armaghan — Current Status
## Urgent presentation and offline customer login — 2026-10-03

DONE and live. Baseline6d67ffca26a70a55e3834942ef1908c7b8b4d83b; lease codex-20261003-hero-fit-customer-account. Customer supersedes earlier bare-gold/shadow/outline choice: gold text on centered navy #101a44 oval, no stroke/shadow, same typography as category titles. Main single-hero uses actual loaded image aspect ratio with auto height/min-height0 and contain; removes fixed-height/blur letterboxing for approved1672×941 image while preserving composition and caption/carousel. Intrinsic geometry is opt-in to SmartImage; other images unchanged. Production wizard category numbers01–03 and subcategory codes now obey existing default-false appearance flags; unnumbered grid uses one column. Explicit admin re-enabling remains possible. Saved published appearance overrides and device/visual acceptance must be assessed separately; no screenshot was supplied this run.

Rollout guard: dedicated POST /api/admin/customers/{customer}/account rejects absent old route rather than silently creating a new CRM record. UI shows action only for explicit server has_account=false; role/active/customer_id forged fields prohibited on dedicated route. Initial Backend CI37148102656 failed two new-test assertions (singular activity_log table and database-default refresh in test revision); no code deployment accepted. Tests corrected to use canonical fresh snapshot; dedicated route guest/customer/disabled-admin/forgery coverage added.

New canonical capability: custom Customers directory offers Create login account on active CRM-only rows. Explicit name/email/per-account initial password+confirmation and identity/access acknowledgement; one atomic server transaction creates a customer-role login and links the same customer ID, preserving company/contact/priority/notes/direct-link settings, orders, tags and history. Locked customer row must still be active/unlinked and match canonical revision; fresh active admin, reserved Google mailboxes, customer-only role and active-account requirements enforced. Existing linked identities can never be reassigned, even by technical manager; existing-login merging/relinking stays OPEN. No new schema/dependency or duplicate Customer. Audit stores IDs/initialized-field names only, not password/email payload; password is hashed, no invented verification. Admin directory adds private has_account field; no customer/public response expansion. Modal clears secrets on close/success, reloads actual records after success. Existing direct-link/session policies unchanged; this is not existing-account password reset or session-revocation completion.

Local Vue61tests/15files/type-check/build and30 current release/source/frozen/media/root checks PASS. Verified release: Backend CI37148369640 SUCCESS,123tests/1104assertions, strict Composer validation/dependency audit/secret hygiene PASS. Backend CodeDeploy37148369841 SUCCESS with guarded code-only activation/health/cleanup; no migrations/dependency changes. First FTP37148102694 and final FTP37148369664 SUCCESS (QA, smoke, scoped Test29/checksums and guarded root promotion). Fresh root and /t/29/ both200 reference index-DkHodfID.js / index-CjtaQLXW.css; fetched assets200 contain intrinsic image ratio, wizard appearance flags, navy status pill and dedicated account route/strict has_account=false rollout guard. Default #ffb514 on #101a44 contrast9.47:1. Live backend/up200; CSRF-valid guest POST to new customer account route401. Actual authenticated owner/device/visual acceptance stays OPEN. New focused tests cover no duplicate/history loss, actual password login to same customer and own orders, private-note omission, stale/inactive/missing/already-linked target rollback, reserved identity/role/password/missing revision/email duplication and secret-free audit. No live customer/account/password/email/order records mutated for testing. Root29 selector revision refreshed; frozen01–28 preserved. Backend guarded code-only lane; no deployed migration changes.

Backlog progress: offline CRM→NEW login creation implemented; persistent tags/specifications/manual timeline/product photos/codes already DONE. Existing account merge/association needs explicit safe identity proof and dependency policy, remains OPEN. Next: secure existing-account reset/session revocation, dependency-safe bulk deletion; titled WhatsApp/shared settings; customer invoice/receipt self-service/confirmed-quote corrections; configured mail, encrypted private-media backup/recovery and authenticated owner/customer/mobile acceptance. Do not claim complete project delivery or visual acceptance from automated/source checks.

## Panel entry and technical-manager label — 2026-10-03

DONE and live. Baseline f5e1df68feb93eb32b4cee7289d4f13bed10af88; lease codex-20261003-panel-entry. Custom live overview is now enabled and is the initial panel: shortcuts open the existing canonical users/customers/products/orders/content/languages/appearance surfaces; no prototype statistics or data are presented. Overview explains product photo/code and home-image edit paths. Existing product gallery file chooser remains visible, keyboard accessible, disabled before product save/during dirty changes/upload/at six-file limit; existing reason and limits plus current image count make readiness visible. Upload API/validation/storage/order stay unchanged. Technical manager replaces Primary owner display and help in four languages; primary-owner authorization/reserved-email protections are unchanged. Own-password link remains deliberate account-security surface, not ordinary Filament fallback. No claim that unimplemented bulk reset/delete/mail/shared settings are enabled.

Local Vue61tests/15files, type-check/build and30 current FTP release contracts PASS. Broader exploratory38-script sweep finds four historical Test27/28 auth/editor checks failing on both prior source and this source because they expect replaced login methods/old write namespaces; these are not current release gates and frozen files were not altered. No backend code/schema changes or live private record/password/email test mutations. Root remains29; independent root revision refreshed; tests01–28 preserved. Authenticated owner upload/navigation/mobile acceptance OPEN.

Verified publication: FTP37144966954 SUCCESS (QA, smoke, scoped Test29 transfer/checksums and guarded root promotion). Fresh root and /t/29/ HTTP200 both reference index-CXQgRmRZ.js / index-D5CM7Q1N.css; fetched assets200, JS contains Technical manager Persian label, overview help and gallery count. Backend/up200; guest products/customers/users401. Actual authenticated upload/navigation and visual acceptance remain OPEN.

Delivery estimate:4–6 focused runs for agreed core workflow,6–8 for all recorded requirements including final acceptance, conditional on configured mail and actual owner/customer validation. Ordered remaining batches: secure existing-account reset/session revocation and dependency-safe bulk removal; CRM/login association and titled WhatsApp contacts/protected admin numbers; customer invoice acceptance/receipt and confirmed-quote correction; configured per-recipient mail and shared appearance settings; encrypted private media backup/recovery and end-to-end acceptance. Product photos/codes, canonical specification schema/values, persistent tags and manual staff timeline/quotes/documents already connected, do not rebuild. This batch fixes the reproducible disabled overview and upload discoverability; unspecified further broken links require actual failing path or authenticated observation rather than speculative enabling.

## Verified customer hero and bulk tags release — 2026-10-03

DONE and live. Implementation340ffa45838a1db2b25342af2942054cd675fd2f; baselinea0102b1; lease codex-20261003-approved-hero-tags. Exact supplied Persian slogan «ارمغان ، تحفه‌ای که مسیرِ مهارت تا شایستگی را پیموده» and client image1000227542 are current single-hero defaults; equivalent Arabic/English/Sorani captions, full uncropped1672×941 foreground with ambient background, same compact mobile height. Local metadata-free WebP161990bytes; SHA256c1c3030fcb420ec5d26e1c1b8208db8cde9d0b78e7ea91bd0a3fcfb1a34a6ccf verified byte-for-byte from live image. Text remains canonical editor/Home Content editable and published Home Media can replace image. Public production texts/images were empty before release; no host-managed choice overwritten. Prior image/carousel retained, frozen01–28 untouched, root29.

Persistent private tags now connected in custom Products/Customers/Users: select one or many, add/remove/replace with count and replacement warning; tags show beside name/code after server reload.10tags perrecord,40chars pertag,100targets; plain multilingual labels, case-insensitive dedup/removal. Canonical Eloquent polymorphic tag record, no public/customer resource exposure. Core revision includes tag values; conflicts/missing targets/permissions/overflow roll back all writes and audit. Protected primary-owner account and actor excluded from user bulk; business admins cannot tag admin/reserved users or owner-linked CRM. Activity audit records mode/count without labels. No live account/password/private-data/email mutations used for tests.

Verification: Backend CI37142660696 SUCCESS,119tests/1057assertions, strict Composer validation, locked dependency audit and secret hygiene. Backend Additive37142660766 SUCCESS: consistent private SQLite backup before new create-only admin_record_tags table, guarded activation, health and cleanup; paired CodeDeploy37142660728 correctly skips migration-bearing release. Frontend61tests/type-check/build and30 source/frozen/media/root contracts PASS. FTP37142660724 SUCCESS: scoped Test29 upload/checksums then independent root promotion. Fresh root and /t/29/ both200 reference index-Cma20lYb.js / index-vxUVRJfx.css; fetched JS200 contains image path, exact Persian caption and bulk-tags endpoint; image200 has matching approved checksum. Backend/up200; guest users/customers/products directories401. Actual authenticated owner/device/visual acceptance stays OPEN, not inferred from automated tests.

NEXT bounded batches: safe bulk deletion/history policy (including owned tag-row cleanup on any permitted hard delete); secure existing-account reset/session revocation; configured per-recipient mail; CRM/login association; titled shared WhatsApp contacts/default and protected admin numbers; shared device appearance; private media encrypted backup/recovery; customer invoice acceptance/receipt self-service. Persistent tags and canonical product specifications are DONE, do not queue as disconnected again.

## Approved hero and persistent bulk tags — 2026-10-03

IMPLEMENTED, remote verification/publication pending. Lease codex-20261003-approved-hero-tags; baselinea0102b1. Exact customer slogan and supplied image1000227542 adopted as current single-hero defaults; four-language motto, full image contain with ambient backdrop, compact height preserved,161990byte metadata-free WebP. Caption remains editable, uploaded published hero media remains authoritative; production published texts/images were empty before release. Asset evidence docs/TEST29-CUSTOMER-HERO.md. No Test26 original replacement/frozen folder writes.

Custom Users/Customers/Products selection adds persistent private tags with add/remove/replace confirmation, row display,10tags×40chars,100items, plain multilingual labels and case-insensitive deduplication/removal. Canonical Eloquent polymorphic admin_record_tags table is a new create-only migration; consistent private SQLite snapshot required before guarded additive activation. No public/customer tag exposure. All records validated under transaction, same resource revision covers core/media/schema/tags, conflicts/permission/missing records roll back every write/audit. Primary-owner account and actor cannot be bulk tagged; ordinary admins cannot tag admin/reserved accounts or protected owner-linked CRM rows. Audit metadata contains mode/count, no labels.

Local frontend61 tests/type-check/build and30 source/frozen/media/root contracts PASS before final translation polish; remote backend/additive/FTP pending. Focused tests: all three domains persistence, add/dedup/remove/replace/clear, directory display/public omission, stale tag/core/status revisions, atomic rollback, bounds and owner/actor/auth boundaries. No real private records/passwords/emails changed to test. Remaining: safe bulk deletion/history policy, secure per-user reset/session revocation, configured recipient mail; CRM account association, titled WhatsApp settings, shared device preferences, private-media backup/recovery and customer invoice/receipt self-service. Persistent tags no longer an unconnected domain after verified deployment. Actual authenticated/device acceptance OPEN; active/root29; frozen01–28 retained.

## Verified canonical specifications closeout — 2026-10-03

Implementation c33fb1e71f63a00a5a11ce46a39087f55a83a033; final UI refinement2203987c0f99679ee0998cd44f89b0408ad1a41a. Lease codex-20261003-product-specifications; baseline440dfd4. DONE: custom Products→Subcategory specifications for four-language labels, fixed/negotiable type, display order and automatically generated immutable keys; explicit category-wide acknowledgement, revision conflict, atomic audit, existing definitions/values preserved. Custom Product edit/create now saves canonical values with category ownership, duplicate/plain-text/length validation, transaction rollback, omitted-value preservation and explicit empty clearing. Schema/value/gallery changes invalidate stale product revisions; specified products cannot silently switch taxonomy.

Public catalog and customer detail now show actual allowlisted specification labels/types/values. Explicit empty canonical schemas remain empty, never guessed from prototype defaults. Current live catalog first product has no specification definitions; the administrator must define them through the new category editor before values can be entered. No fixtures are inserted into production. During mixed-version rollout only absent API fields retain old fallback. Production wizard reads hydrated catalog rather than static product fixtures. Fixed means fixed for negotiation, not mandatory data entry.

Verified Backend CI37131839391 SUCCESS:115 tests/1004assertions, strict Composer validation, locked dependency audit and secret hygiene PASS. Backend CodeDeploy37131839432 SUCCESS: guarded code-only activation, health/resource checks, cleanup; no schema/dependency migration required. Local frontend61 tests/type-check/build and30 source/release contracts PASS. Live /backend/up200, catalog200 includes specifications, guest admin taxonomy401. Initial FTP37131839396 SUCCESS; final UI FTP37131937856 SUCCESS including scoped Test29 upload and checksum validation. Final selector/root promotion FTP37132295602 SUCCESS. Fresh root and /t/29/ both HTTP200 and reference index-DPYRUkX8.js / index-CaVA_85k.css; both assets200 and JS contains canonical schema/value UI plus automatic internal keys. Final nonvisual release verification PASS. Authenticated owner/device/visual acceptance remains OPEN; no live accounts/passwords/emails/private rows changed for tests.

NEXT: safe bulk deletion/history policy, persistent tags, secure existing-account reset/session revocation and configured recipient mail; CRM/login association; titled shared WhatsApp contacts/default and protected admin numbers; shared per-device preferences; private media off-host backup/recovery and customer invoice/receipt self-service. Product specifications VALUE and SCHEMA editing are now implemented and must not be queued as unconnected again. Tests01–28 frozen; active/root29 preserved.

## Canonical specification management — 2026-10-03

IMPLEMENTED, CI/deployment pending. Lease codex-20261003-product-specifications; baseline440dfd4. Bounded next batch: preserve custom Vue tables/forms; Product edit now uses actual subcategory specification definitions and individual values. Create/update validates at most100 distinct owned definitions, plain text1000chars; omitted values preserved, explicit empty clears only that value. Core fields + specification values save atomically, with revision including schema/values/gallery; existing specified products cannot silently change taxonomy. Activity metadata excludes values.

Custom Products→Subcategory specifications adds labels in four languages, immutable machine key, fixed/negotiable type and display order. Explicit shared-category acknowledgement, checksum conflict and transaction; existing definitions/values cannot be dropped or keys reassigned. No guessed fixtures, browser-local specification writes, schema migrations or new dependencies. No mandatory-value rule inferred from locked: locked means fixed for customer negotiation, not required data entry. Public catalog exposes allowlisted labels/types/values, customer detail displays actual values, production wizard reads hydrated catalog rather than static prototype products. Missing old API field keeps rollout fallback; explicit empty canonical schema stays empty.

Local frontend61 tests/type-check/build and30 source/release contracts PASS; remote backend CI/deploy pending. Backend focused cases cover cross-category/duplicate/HTML/length rejection, rollback, omission/clear, stale schema/value conflicts, create/move and shared schema retention/auth. Authenticated owner/device acceptance remains OPEN. Remaining P1: bulk deletion/reset/tags/mail; CRM account association; titled WhatsApp settings; shared device appearance; private media backup/recovery; customer invoice/receipt self-service. No live account/password/mail/private-data test mutations. Tests01–28 frozen, root29.

## Verified footer and business workflow closeout — 2026-10-03

Current implementation: backend46efd38d7608544491a173edfc5c3d59c418f49e; final frontendad1bac9310e3c1f30eecb98f66aa0d254ae2e31c. Lease codex-20261003-footer-followthrough; baseline34c95b7. Supplied footer and both follow-up share screenshots inspected. No frozen01–28 mutations; active/root29 preserved.

DONE this run: (1) structural compact footer/logo inside help column, single navigation clearance and full-height category image; (2) eight homepage image slots with owned validated uploads, preview, retained history, independent staging/root publish and default restoration; (3) Home Content form and visual-editor text aliases synchronized, three capability headings/descriptions included, internal field-code clutter removed; (4) scoped server retry key for repeat-safe order registration; (5) bounded line items, quantity, optional product code and exact six-currency quote totals/adjustments; (6) private invoice/receipt upload and guarded downloads, explicit customer visibility; (7) reasoned manual stage corrections, payment gates, owner-only terminal reopening; (8) urgent wishlist new-link issue fixed to stable root despite stale Test27 host setting, same ordered active selection resolves without owner identity; (9) current/handoff/backlog synchronized. Older shares already addressed to frozenTest27 must be reissued from current root; no frozen-folder rewrite or host-routing overwrite performed.

Backend CI37127752123 SUCCESS:112 tests/948assertions, strict Composer/audit/secret checks PASS. Backend Additive37127177439 and37127752119 SUCCESS, consistent private database snapshots before create-only request-key/quote tables, activation/health/cleanup; CodeDeploy paired runs correctly guard-skip migration-bearing commits. Earlier HomeMedia partial-model URL-appender error stopped CI/additive before deploy and is fixed by allowlisted persisted revision fields. Local Vue60 tests/type-check/build and30 source/release checks PASS. FTP37127988304 SUCCESS: QA, scoped Test29 upload, byte/checksum verification and guarded root promotion. Fresh root and /t/29/ HTTP200 both reference index-BXmZQIYx.js / index-C0snbSgC.css; both asset files HTTP200 and contain home upload/quote/documents/manual correction/root-share wiring and footer/banner geometry. Live backend/up200, public home-media200, guest admin home-media and both order endpoints401. Earlier transient HTTP timeouts were retried successfully; no authenticated acceptance inferred. No private live rows/payments/passwords/emails manipulated as test data.

OPEN next batches, in priority order: P1 safe bulk deletion/dependency policy, secure per-user password reset with session revocation, persistent tags and configured per-recipient mail; P1 canonical specification editing and offline CRM/login association; P1 shared titled WhatsApp contacts/default selection and per-admin protected settings; P1 shared device appearance controls; P1 private media off-host encrypted backup/recovery plus safe unpublished history cleanup; P1 customer invoice acceptance/receipt self-service, corrections to confirmed quotes and notifications as required. No automatic bank reconciliation, fiscal invoice generation, currency conversion, inventory reservation or generic accounting completeness claim. Authenticated real owner/customer end-to-end, real uploads and mobile/desktop visual acceptance remain OPEN. New user request does not authorize testing with other live accounts or sending test mail.


## Order commercial details, private files and audited corrections — 2026-10-03

IMPLEMENTED/PUBLICATION PENDING: create-only order_quotes table; up to50 rows with optional existing product code, plain description, positive bounded integer quantity and integer minor-unit price. Supported IRR/IRT/USD/EUR/IQD/AED; exact integer totals plus bounded shipping/tax/discount, reject negative total. Custom Vue form accepts decimal currency prices, lexical minor-unit conversion, note and revision; quote drafts withheld from customer until manual invoice confirmation. Confirmed invoice and terminal order quote details locked, no stock reservation/bank reconciliation/fiscal PDF generation. Admin can attach up to12 PDF/JPG/PNG/WebP files8MiB; MIME/PDF-header or image decode/pixel checks, metadata-stripped images, Spatie media on local PRIVATE disk at media/orders/<id>. Customer file visibility explicit, download only guarded own-order route, other customers404, protected owner guard, attachment disposition/nosniff/no-store; no static public/private path leak. Original files retained, no deletion. Staff receipt upload and manually confirmed deposit remain separate operations. No automatic customer upload/approval of invoice or banking claim.

Manual stage_correction requires reason>=10chars and preserves invoice/deposit gates; ordinary administrators can correct nonterminal state. Only verified primary owner can reopen terminal cancelled/delivered state. Timeline highlights observed events/current stage rather than assuming every skipped stage happened. Per-order events/activity audit preserved; optimistic conflict checks include quote/media state. Product/customer ownership protection unchanged. Local frontend60/type-check/build and30 source checks PASS; new backend tests cover amounts, confirmed-lock, draft privacy, private document visibility/ownership, correction and owner-only reopen. Additive lane snapshot/migration and runtime acceptance pending. Remaining broader admin gaps: bulk delete/reset/mail/tags, specification editor, shared titled WhatsApp/device settings; customer invoice/receipt self-service, automatic notifications and authenticated/mobile acceptance OPEN.

## Urgent wishlist share correction — 2026-10-03

P0 added during active run: owner root wishlist share opened frozen Test27 and reported invalid. Backend default and fallback now root /#/favorites/share/<token>; historical host override /t/01–28 is normalized to root without modifying frozen assets. Frontend also validates issued token fragment and normalizes root during server rollout. Existing persisted token resolves via authorized-neutral POST to the same ordered active product selection; personal identity stays omitted. Expiry/revocation rules unchanged. Tests cover stale Test27 config plus exact root URL and selection resolution. Reissue links from current root; old frozen entry is not rewritten. Home media initialCI37126932504 failed because partial Spatie models serialized URL appenders with absent disk; revision now maps allowlisted persisted fields only. Additive37126932515 stopped before production migration; undeployed create-only migration release comment retriggers guarded lane. Source/CI/publication pending fix.

## Homepage media and repeat-safe order submission — 2026-10-03

IN PROGRESS: custom Content & images includes8 allowlisted slots, decoded/metadata-stripped JPG/PNG/WebP uploads8MiB/5000px; canonical existing StyleProfile Spatie media, public media/home/<id>,64-file retained history bound. Upload remains unpublished; independent staging/production publication with revision conflicts, owned media IDs and no duplicate targets. Choosing default restores static local media; old uploaded media retained for rollback. Home resolves only published URLs; no browser-only image state or arbitrary paths/HTML. Content tab links existing visual editor. Scoped UUID+payload hash prevents order retry duplication; pending UUID retained in namespaced session storage across reload/network errors, removed only after success. New create-only key table requires guarded additive snapshot lane; old clients without key remain compatible. Production deployment/CI pending. Remaining financial detail/private attachments/manual corrections/bulk reset-delete-mail-tags/specification/WhatsApp/device settings remain OPEN.

## Footer correction and remaining integration batches — 2026-10-03

Lease codex-20261003-footer-followthrough; baseline34c95b7. Supplied screenshot shows separate full-width logo row plus duplicated bottom-nav clearance and banner min-height larger than image height. Selected structural flow fix (10) over blanket shrinking (7), gap-only (6), absolute positioning (3), hiding content (1). Footer logo remains6.9rem but moves inside help column; footer navigation clearance is owned by main content only; category photo fills the entire explicitly bounded banner height. Frozen01–28 unchanged, root remains29. Local build/type-check/60 tests PASS; publication in progress, visual/device acceptance OPEN.

Ordered followthrough: footer/banner geometry; homepage media upload/publishing; clear content-edit entry; repeat-safe order registration; structured order lines/quantity/prices/currency; private invoice/receipt attachments; audited manual stage correction; remaining custom management capabilities and account protections; end-to-end acceptance/documentation. Product photos/content/translation editing and manual timeline already implemented; do not rebuild these. Homepage shared media, bulk delete/reset/mail/tags, product specifications and shared titled WhatsApp/device settings remain real gaps, not delivered by enabling prototype links. No production data/password/email mutation for tests.

## Customer feedback: compact storefront and manual order tracking — 2026-10-03

Baseline9b7f0a72709994979bac81ddbc295af0753313d8; lease codex-20261003-customer-feedback. Seven supplied mobile screenshots inspected in order. Prior final FTP37109006579 and doc FTP37109292345 both SUCCESS; previous pending publication is complete. First screenshot is a real CUSTOMER profile using a mailbox outside the three approved administrator identities; do not infer admin capabilities from its heading Panel, and never elevate an unrelated email merely from a screenshot.

Ranked priorities: canonical manual orders/timeline10, compact storefront/category prominence9, labels/gold8, editing/media guidance7, full automatic financial integration4. Product request labels now Persian Purchase=خرید, available-path=درخواست تغییر, unavailable-path=درخواست تولید; existing durable six path IDs remain unchanged. Production brand/packaging labels explicitly اختصاصی. Golden title stroke removed and replaced with subtle neutral shadow; typography remains matched. Mobile hero capped26svh (160–224px) and caption shortened; about/capability image heights and all intervening gaps reduced; capability icon shares title row. Full-width product banners strengthened (12rem, border, title) rather than overflowing viewport. Safe button scrolls to category banners without changing Vue hash route. Footer spacing/padding reduced with6.9rem logo preserved at visual right; no section/content removed. Deferred device/browser/contrast acceptance is still OPEN.

Canonical tracked_orders and append-only tracked_order_events are new create-only tables. Guarded additive deployment must snapshot private SQLite before migration; do not use code-only lane for this change. Admin order cards also show canonical customer name and ID; customer responses omit that admin metadata. Customer selection includes ID/email to disambiguate similar names. Active admin lists/registers requests for existing active CRM customers, sees/changes adjacent stages, adds reason notes (internal default or explicit customer visibility), manually confirms invoice and deposit once. Deposit requires invoice; production/preparation and subsequent stages require both confirmations. Terminal delivered/cancelled records allow notes only; all changes require content revision and emit per-order events plus activity audit in transaction. Other admins cannot view/create/change primary-owner-linked orders. Customer endpoints always use authenticated session customer, reject submitted customer_id, expose only own orders and public notes, exclude admin revisions/identity/internal events. No hard deletion or stock reservation/payment automation.

Custom Admin gains Orders & tracking tab; real customer panel gains own timeline/empty state above profile. Admin can register offline/WhatsApp orders manually. Both product WhatsApp selector and final production wizard offer explicit Register request for tracking for actual backend customers (WhatsApp remains independently available to guests). Successful registration returns reference and panel link and disables resubmission for the unchanged form. Changing request opens a new explicit request. No claim of exactly-once across network timeouts/browser reload: server idempotency is NEXT P1.

Manual confirmations are operational records, NOT automatic bank settlement or generated fiscal invoices. Remaining order work: structured quantities/product lines/pricing/currency, invoice files/receipts with private authorized uploads, customer invoice acceptance if required, payment amount/reconciliation, idempotent request tokens, admin/customer corrections and notification delivery. Customer notes/forms are plain text only, no HTML. Actual payment evidence/date/amount and backward-stage correction need review before accounting use. Do not expose demo order fixtures on root.

Images: real PRODUCT upload already exists under custom Products→edit→gallery (6 JPG/PNG/WebP,8MiB,5000px limits). Homepage text edits exist under manufacturer/content and live visual editor with save/publication; generic dictionary via languages. Shared uploaded HOME hero/about/capability/banner images do NOT yet have a custom canonical management interface; next media batch must add safely owned uploads/validated target mapping/revision/publication, NOT browser data URLs or instructions to email images. Existing customer profile uploads remain unimplemented. Explain these distinctions in handoff; do not claim all photos can be replaced already.

Validation local full app type-check/build60tests PASS;30 source checks PASS; WhatsApp tests updated to requested new text. Five focused backend tests added for full lifecycle, confirmation gates, stale rollback, customer ownership/private-note exclusion and owner protection; Initial backend CI37117547262 / additive37117547259 correctly stopped on first-edit409 failures caused by transient create/save model defaults/timestamps. Fixed snapshots to refresh persisted rows before revision/output. The harmless release comment retriggered the guarded additive lane without modifying any deployed migration. Backend Additive Deploy37117683517 SUCCESS: private database snapshot followed by create-only order/event tables and health verification. Final implementation abfd780e4d5a83a866ad85c5117bff1dbbdc4c34: Backend CI37118280997 SUCCESS (104 tests), Backend Code Deploy37118280989 SUCCESS, FTP37118280992 SUCCESS including QA, Test29 and guarded root promotion. Local Vue60 tests/type-check/build and30 source checks PASS. Root and /t/29/ HTTP200 both reference index-Cc9nTUyh.js / index-Cragsg8W.css; backend/up200, guest admin/customer order endpoints401. No actual production customer/order/payment rows modified for testing. Authenticated owner/customer end-to-end, upload and mobile visual acceptance remain OPEN; do not claim those were performed. Frozen Tests01–28 unchanged. Public/customer_name privacy assertion added; manager customer selection includes ID/name/email.


## Bounded bulk status and bright gold typography — 2026-10-03

Closeout within owner20-minute cap: Backend CI37108898574 and Backend Code Deploy37108898659 PASS;99 tests/830 assertions and strict Composer/audit/hygiene PASS. Initial UI740a7dfd2daf64408281d0dddeb843f521f7de4d published by FTP37108813424 at root and Test29: fresh HTTP200, index-CPDSNzmh.js/index-BPuGIeWU.css. Core gold and bulk-status controls are live. Final source5defdacbce28e5beae422c4cde3f1fb814463e57 also restores translation fallback/editor reactive refs/full app type check and theme-token outline; local app type-check/build/60tests PASS. Final FTP37109006579 still IN PROGRESS at closeout; final index-pb7y_mLD.js/index-ZFrz1V-D.css not yet confirmed live. Guest noCSRF bulk POST419 and health200. Actual owner/browser/visual acceptance OPEN. No production rows/passwords/email sends used as test data. Monitor final FTP before another UI publication; do not repeat completed core. Deletion/password/mail/tag and remaining domain connections remain open.


Owner requests maximum ~20 minutes; baseline 44ed36ffa4a5da89cf3ec0106b99c11080b79baf; lease codex-20261003-bulk-status. Ranked options: focused canonical bulk status (10 selected), one heavy domain (7), all-at-once (5), stock interface (3), browser-only actions (1).

Custom Users/Customers/Products tables gain visible-page selection, select-all, selected count, clear and confirmation modal for activation/deactivation. Server accepts at most100 distinct ID/revision pairs; outer database transaction reuses existing single-row authorization, revision validation, account/customer synchronization and per-record audit; any missing/stale/forbidden item rolls back all changes and audit. Protected owner and actor are excluded from account bulk operations. Products preserve taxonomy/specs/media/names/availability. No schema/dependency change.

Producible text now matches subcategory title .78rem/900 (English600), centered, bright brand gold with fine dark outline in light mode and unoutlined gold in dark; no navy badge. Supersedes smaller400 typography and brown #805300 per latest explicit owner instruction. Full visual/device acceptance OPEN; do not claim bright fill alone meets contrast without its outline.

Full application type-check/build/60 tests and30 source checks PASS. Correct pre-existing translation fallback recursion and pass reactive enabled references to visual-editor selection/resizing; annotate target registry and safe regex indexes. Package type-check now explicitly checks tsconfig.app rather than empty root references. Initial backend focused suite failed only because new tests named activity_logs instead of the existing activity_log model table; assertions corrected to canonical ActivityLog::count(). Backend focused tests added for rollback/conflict, owner/actor permissions, linked customer status, product visibility and bounded/duplicate/auth failures; remote CI/deploy results pending at implementation commit. Actual authenticated owner/browser acceptance remains OPEN.

NEXT: safe bulk deletion with explicit count warning and dependency/history policy; secure existing-account password reset with session revocation (never same shared password), real configured email composition/delivery and per-recipient audit, persistent editable tags displayed in rows. NOT delivered in this batch. Canonical specification editing, shared titled WhatsApp settings, orders/timeline/payments and device settings remain open; do not imply all prototype domains connected.



## Light corrections; next urgent integration batch — 2026-10-03

Baseline c988c69fbad27582fa9412d8395ce1bc4abc64d1; lease codex-20261003-light-corrections. Owner explicitly chooses a bounded light batch, then a separate heavier batch to preserve token budget. Ranked: preserve custom UI and canonical permissions (10, selected), all-at-once (7), stock fallback (5), rebuild (3), browser-only settings (1).

Footer columns occupy full width; enlarged logo occupies its own compact bottom row aligned visually right in every locale. Remove internal integration-progress and social-placeholder copy. Top admin entry is «پنل مدیریت»; signed-in navigation reads «پنل». /tracking recognizes actual admin identity and renders live custom AdminDashboard instead of another sign-in gate; customers retain their own dashboard.

Product table and messages emphasize six subcategories; existing individual names remain stored and editable inside collapsed optional controls. New products can initialize the internal Persian name from canonical subcategory/code. Status choices are available/producible. Old unavailable and made_to_order records remain intact, both match the producible filter; untouched old availability stays unchanged when saving. Producible gold text is #805300 in light mode (4.90:1 on mint, 6.66:1 on white) and unchanged brand gold in dark mode (9.22:1 on dark surface). No navy badge backing reintroduced.

Only verified primary owner can edit its own name with revision checking. No other account may edit it. Disable/demotion/deletion are additionally rejected at model level; self-deletion is forbidden too. Existing reserved email/role hierarchy remains protected. No password reset/role conversion added in this batch.

WhatsApp destination defaults to 09933509793 / 989933509793. Prior 09381009231 / 989381009231 remains retained. Shared editable/addable titled Business/Personal contacts, selected default and per-admin contact numbers are NOT implemented yet. NEXT urgent batch: canonical specification editing with typed schema/required/negotiable fields and conflict checks; then shared WhatsApp settings with initial Business/Personal titles, normalized numbers, protected admin/primary-owner authorization, audit and revision checks. Never fake shared settings with browser storage or replace existing specifications with guessed fixtures.

Core integration estimate: 4–6 focused remaining runs: specifications/taxonomy/media gaps; shared contacts/settings; secure existing-account reset/CRM association; real orders/timeline/payments (1–2 runs); shared device preferences and end-to-end acceptance. Advanced accounting/analytics may require additional runs. Report actual capabilities, not a blanket backend-complete claim.

Validation PASS: local/FTP type-check/build and 60 frontend tests / 15 files; 30 source/frozen/media/release checks. Backend CI 37104373470: 94 tests / 791 assertions, strict Composer validation, locked dependency audit and secret hygiene PASS. Backend Code Deploy 37104373420: guarded code-only activation, health/auth/resource smoke and cleanup PASS. Final FTP 37104477652: QA, scoped Test29 publication/checksums and guarded root promotion PASS. Backend source ec36645f849fa2f64780379a0033308aa0ef3048; final UI/runtime c9e44fbb8ca3d0b49955030a82e6346dfd6a92a5. Fresh root and Test29 both HTTP200 and load index-DDbx9iHz.js / index-BaryU0P4.css; fetched JS contains 989933509793 and CSS contains contrast gold plus separate footer row. Live health200 and guest account/customer directories401. The tracking route embeds live AdminDashboard directly, avoiding a session-hydration remount loop, and pauses the inactive visual editor. Owner-linked CRM row is hidden from other admins and rejects their edits. Actual-owner/device browser acceptance OPEN. No schema/dependency or private production data changes. Tests01–28 frozen; root remains29.


## Custom administration default and responsive header — 2026-10-03

Baseline e4bc63346d9afb4ad8a7a7a795a93303d0212f7d; lease codex-20261003-custom-only-header. Owner requires the preserved Vue interface as the ordinary management surface. Historical direct Filament fallback links and incomplete real-domain integrations explain stock-panel entry; this release removes those ordinary links and protects the entire stock panel, including persistent Livewire requests. Only active verified primary owner can explicitly choose integration-gap, diagnosis or recovery, yielding an audited session-scoped 15-minute permit. Logout, expiry and revocation clear access. Ordinary admins keep business API access.

Custom content, shared four-language translations and the existing visual editor now use canonical Laravel Style Profile drafts, checksum conflicts, version history, restore and root/test publication. Existing semantic target IDs remain authoritative; additional dictionary keys use collision-free UTF-8 encoded IDs. Root uses published production copy rather than stale browser-local overrides. Real admin authentication enables the editor. Device-specific appearance preferences and prototype analytics remain unintegrated; do not present those as shared production administration.

Desktop language selector shows only the language name. Mobile always exposes help, globe plus abbreviated language opening a centered themed modal, brightness toggle and actual-session sign-in/logout icon. Unsaved theme defaults light and preserves explicit saved choices. Why-number border is 1.85px gold, text secondary, font 1000 .72rem / 1 Inter, Roboto, sans-serif. Capabilities heading/intro defaults hidden through reversible appearance schema6 flag; schema5 preferences migrate without loss and visual-editor visibility override remains available.

Migration inventory: real product core editing, upload/order/archive, customer CRM core, account create/status and owner hierarchy, style/text/history/publication and content/languages are connected. Product specifications/taxonomy management, physical gallery removal, existing-account password reset with session revocation, role conversion and CRM-account association still require custom integration. Prototype orders/timelines/payments/analytics and shared per-device preferences are NOT completed. Never enable browser-only demo mutations to satisfy a management link. Preserve existing custom forms/tables/editor while integrating those domains.

Validation PASS: local and FTP type-check/build, 60 frontend tests / 15 files, 30 source/frozen/media/release contracts. Backend CI 37101039781: 93 tests / 783 assertions plus strict Composer validation, locked dependency audit and secret hygiene PASS. Backend Code Deploy 37101039794: guarded code-only activation and health/resource smoke PASS. Final FTP 37101470563: QA, scoped Test29 publication/checksums, production custom-admin authentication boundary and guarded root promotion PASS. Runtime 0214da02c90abd29c62a61b17a479ed83dd7dfd4; final JS index-DGmG5vAo.js and CSS index-yH_Z3kwn.css match fresh root and Test29 HTTP200. Live /backend/up=200; guest stock login/products return302 to https://armaghantrading.com/#/admin; guest real products API=401. Initial payload and middleware-order failures were rejected and corrected before successful backend activation; obsolete root-readiness stock-login expectations were corrected. Inactive editor synchronization is paused to avoid duplicate writers when using custom content/language panels. No schema/dependency changes or private production records changed. Tests01–28 frozen; selected root remains29. Visual/device and real-account acceptance remain OPEN.


## Real account management and owner hierarchy — 2026-10-03

Owner asks why prototype management links remain unavailable, authorizes amirmashti1378@gmail.com as a business administrator and motealle@gmail.com as the superior primary owner. Baseline 4443bd35246aaa0b283b30730cd306d0fe2c75f0; lease codex-20261003-admin-authority. Ranked options: preserve custom domain integration (10, selected), all domains at once (7), existing stock fallback (6), rebuild custom administration (4), browser-local authority (1).

- Verified Google allowlist now contains motealle@gmail.com, armaghantrading.company@gmail.com and amirmashti1378@gmail.com. The new identity becomes an administrator on its next successful stateful, verified Google callback; code deployment alone does not claim an existing database row has been elevated. Inactive accounts remain rejected. Primary-owner authority requires active admin role, verified email and exact configured primary email; no browser/request role can grant it.
- Preserve custom AdminDashboard and table/AdaptivePanel surfaces; Users joins real Customers and Products. Server-paginated/searchable accounts, customer account creation with confirmed hashed initial password and linked canonical Customer, name/status edits and archive without hard deletion. Owner alone may create additional administrators and view/manage administrator account status. Other admins can manage customer accounts, never an admin or owner. Reserved Google identities cannot be claimed by admin-created or public signup passwords. Owner account cannot be edited/deactivated via these APIs, including by itself; ordinary own password changes remain on the existing protected account-security page.
- Administrator emails are immutable at the User model level, including the existing Filament profile: a lower admin cannot claim the owner's mailbox even before the owner account exists. Owner may reactivate a reserved inactive customer account; only its next verified Google callback can elevate it. Focused regressions cover both paths.
- Existing emails and roles are immutable in this slice. Resetting another existing account password, role conversion, destructive deletion, impersonation and CRM-account linking are NOT delivered. Password recovery/reset must include existing-session revocation before implementing. Creating a CRM customer is still distinct from creating a login account.
- Add shared real-admin identity/navigation/logout on public pages so a genuine admin is not shown only a customer/demo role. Administrator session reports primary-owner state. Customer session additionally rejects a disabled/noncustomer linked User even if its CRM record is reactivated. Product/customer links require that same real web session. Guest/disabled/customer authority cannot mutate management data.
- Disabled custom content/appearance/language/overview tabs reflect incomplete real-data integration, not proof of an account permissions failure. Existing business resources remain available in Filament. Do not enable prototype-local stores as production management. Next: secure existing-account recovery/role management with authentication proof and session revocation, canonical product specifications, then preserved visual editor/style profile and device/content/language settings with real admin authorization and shared persistence.
- Verification PASS: local type-check/build and 58 frontend tests / 15 files; 30 workflow source/frozen/media/release checks plus two custom-admin source contracts. Backend CI 37097360140: 89 tests / 741 assertions, strict Composer validation, locked dependency audit and secret hygiene PASS. Backend Code Deploy 37097360143: guarded code-only activation, health/admin/catalog/resource smoke and temporary cleanup PASS. FTP 37097360139: QA, smoke, Test29 publication/checksums and guarded root promotion PASS. Runtime f989865bac8eaa5cc6f3a9851c516d6deb3ca65f; final JS index-LY52vbHs.js / CSS index-CPT5jm1O.css. Fresh live health GET=200, guest account directory GET=401, no-CSRF account-create POST=419. Actual owner/new-admin sign-in and authenticated live account operations remain OPEN; no production accounts/passwords/private data changed by the agent. Tests01–28 frozen; root remains selected29 with guarded revision refresh.


## Preserved custom administration — products and media, 2026-10-02

Owner authorizes the next bounded custom-admin domain. Baseline/rollback checkpoint: `b2d0b7cdf12d0359789b98d95cce995a1901f475`; lease `codex-20261002-custom-products-media`. Five ranked options: preserve existing UI with canonical Laravel/Spatie (10, selected), all domains at once (7), restyle stock admin (6), independent product/media services (3), browser-only persistence (1).

- Preserve AdminProductsPanel/ProductEditorPanel entry components, AdaptivePanel, existing table/form/surface styling; add live server mode. Products tab joins Customers in the custom real-admin route; other tabs remain unavailable pending integration. Existing Filament remains an operational fallback.
- Same Product, taxonomy and Spatie product-gallery models; authenticated active-admin, CSRF-protected, bounded paginated/search/filter API. Create/update names in four languages, code, subcategory, availability, active/archive and order. No hard deletion. Existing specification values are preserved; moving a product with existing values across taxonomy is rejected. Structured specification editing remains next work, never fake prefix defaults.
- Real multipart image upload; JPEG/PNG/WebP, 8 MiB, 5000x5000, six items; MIME/decode verification, server-generated safe filename, existing EXIF/orientation sanitization and non-upscaled card/thumb conversions. Preview and exact-owner scoped gallery ordering; files stored only in existing host-centric canonical paths. No new tables/dependencies/migrations. Physical image removal remains existing Filament fallback; no custom destructive action introduced.
- Content revision covers fields, specification values and ordered gallery, checked under transaction before changes. Failed processing cleans only this request's new media/files before rollback. Audit actor/subject/field names only. Same public adapter refreshes after successful editor close and hides backend-archived local copies.
- Local build/type-check and 56 frontend tests / 15 files PASS; 31 source/frozen/media/release contracts PASS. Backend CI #68 (37061931861) PASS: 79 tests / 662 assertions, strict Composer validation, locked audit and secret hygiene. Backend Code Deploy #24 (37061931645) PASS: production health/admin/catalog/resource smoke HTTP 200 and temporary cleanup PASS. FTP #353 (37061066948) PASS: QA, smoke, Test29 publish/checksums and guarded root promotion. Later backend-only FTP #358 (37061931696) passes QA/smoke and correctly skips UI/root. Final source runtime `d729344e42218fe45af33d818e021a19a35d7e84`; initial frontend publication `02bbb142cccb81de14f759c66d5b8d730a23ea60`; JS index-DpQkpQj3.js / CSS index-CPT5jm1O.css. Root browser reload loads this JS, guest custom-admin page shows sign-in gate and zero management tables. Fresh live health GET=200, admin products/taxonomy GET=401; no-CSRF create/image-upload/image-order POST/PUT=419. Actual owner authenticated product/media browser acceptance and deferred device/visual checks remain OPEN. No production product/customer records or credentials changed by the agent.
- Acceptance caught and corrected partial Spatie model serialization and the canonical `order_column` mapping before backend deployment. Real JPEG with comment metadata verifies stripping, safe generated name and non-upscaling; executable uploaded extensions remain rejected. Upload failures preserve prior files. General admin and image-upload throttles have distinct keys: 10 upload attempts/minute; list reads do not consume upload budget, verified by a focused test. Initial backend test gates refused deployment until final PASS; no migration/dependency or private-data changes.


Next: structured canonical specifications, then existing style-profile/editor/device-settings authorization and persistence. Do not claim all custom management modules migrated.



## Preserved custom administration — customer vertical slice, 2026-10-02

Owner explicitly requests the previously designed administration interface connected to Laravel rather than replacement with stock panel styling. Baseline checkpoint: `4b19f5da25734a47facd68396ed6538bf79fc207`. Shared lease: `codex-20261002-custom-admin`.

| Rank | Path | Score | Reason |
|---:|---|---:|---|
| 1 | Reuse custom UI, connect one complete domain flow at a time | 10 | Preserves design and permits focused persistence/security acceptance |
| 2 | Connect all administration at once | 7 | Larger failure scope |
| 3 | Restyle the stock panel | 6 | Rebuilds approved interaction details |
| 4 | Independent admin data stores | 3 | Divergent data |
| 5 | Browser-local administration | 1 | No shared persistence or real authorization |

Selected option 1. This run connects `/\#/admin` and `/t/29/\#/admin` to real admin session and Customer records using the existing AdminDashboard tab shell, table, form, profile-head and AdaptivePanel styles. Customers are the first migrated domain; other custom tabs remain visible but disabled until wired. Existing Filament product/media/customer operations remain available through explicit links. No stock replacement, new database, dependency or migration.

- Active administrator is checked by existing Laravel `active.admin` middleware on every API request. No review role/storage state grants access. Session and customer responses are no-store/private; inactive/customer/guest principals cannot read or mutate. Administrator logout rotates session state while retaining independent customer authentication.
- Paginated/searchable customer list; create CRM record; edit company/contact/country/internal notes/priority/access flags; deactivate/reactivate without deleting records. Same Customer Eloquent model and database as Filament, with explicit fields. Public account registration remains owner-operated; admin form never changes account email/password/user ownership.
- Revision fingerprint prevents stale edits even within one timestamp second, inside a database transaction. Conflicts require reload; no silent overwrite. Audit stores actor/id/field names only, not note/contact contents. Failures remain visible and no fake row/demo-order data is used in the live custom screen.
- Customer password/timeline/payment/photo/impersonation and bulk deletion prototypes are not exposed as live operations. Customer counts come from the server page, not fixtures. Disabled customer access retains records.
- Password administrator login opens the custom route; verified Google owner setup page links there after private password setup. Ordinary Filament login remains available. Test29/root selector preserved; 01–28 frozen.
- Backend CI #62 (37051870668) PASS: 70 tests / 538 assertions; Backend Code Deploy #18 (37051870771) PASS, health/admin/catalog/resource smoke and temporary cleanup PASS. Frontend 53 tests / 14 files, type-check/build and frozen guards PASS. Initial FTP #348 (37051870707) Test29/root PASS. Final UI label release FTP #351 (37052472009): QA, smoke, Test29 publish/checksums and root promotion all PASS. Final JS index-LZ6WcOKf.js / CSS index-B0UfuJw4.css, lazy AdminView-C-JWJxiT.js; root browser reload confirms final JS and custom admin route. Live guest admin session/list GET=401 and no-CSRF customer-create/admin-logout POST=419. No production customer records or credentials inspected, created, edited or deleted by the agent. Actual owner authenticated browser writes/reload, mobile/tablet visual acceptance and valid owner Google provisioning remain OPEN.

Next bounded slices: (1) preserve AdminProductsPanel/ProductEditorPanel and connect canonical Product CRUD and existing Spatie media validation/ordering; (2) real admin access to existing style profile/editor publishing and device settings; (3) remaining dashboard/lead data only when actual services exist. Do not duplicate services or claim a fully migrated custom admin.

Implementation references: official Laravel 13 request-forgery protection and database transaction documentation. Existing session/CSRF mechanism remains unchanged.

## Owner administrator access and real password accounts — 2026-10-02

- Owner explicitly authorized administrator access for motealle@gmail.com and armaghantrading.company@gmail.com, real email/password sign-in, public customer signup, removal of the direct-link login tab, transparent gold producible labels, and a footer logo 130% larger at bottom right.
- Five ranked methods: verified Google owner provisioning + real password flows (10), separate admin password panel (8), Google-only (7), public magic-link flow (5), browser demo authentication (1). Selected the first; no browser-local role or typed email alone grants administrator rights.
- The server owner-email allowlist is configuration-backed. Only stateful Socialite with a valid subject and verified Google email can create/elevate those administrators. Disabled accounts remain disabled; promoting an existing customer disables its customer access and replaces its old password. Existing administrator passwords survive repeat Google login.
- Owner Google login establishes real Filament web authentication and opens /backend/account/security with a 10-minute, own-user, one-use password setup proof. The user chooses the password privately in the browser; no password is seeded, displayed, transmitted in chat or saved in Git. Existing password changes require current password or fresh Google proof. Profile management is enabled in Filament.
- Same-origin CSRF-protected POST login/register routes hash passwords, enforce at least 12 characters with letters/numbers and confirmation, rate-limit login per account and IP, and issue real customer sessions. Public signup always creates customer role, cannot claim owner emails, cannot set verified/admin/internal fields, and rejects duplicate emails. No email verification or password-reset mail delivery is claimed.
- LoginSheet uses only real server login/signup on root and Test29. Direct-link tab removed; existing safe manager-issued Magic Link consume remains functional for compatibility. Clear administrator password-panel shortcut remains visible. Real customer account includes a password-settings link.
- Producible badge keeps its gold text, centered small weight 400 typography, with transparent background and no navy border/shadow. Footer brand moved after columns in DOM and aligned physical bottom right; 3rem to 6.9rem is +130% (2.3×), with contained image and no adjacent text. Editor target IDs remain stable.
- Tests 01–28 frozen; Test29 remains active and selected at root. Local type-check/build + 51 frontend tests PASS; Backend CI #61 (37046579401): 63 tests / 473 assertions PASS. Code deploy #17 (37046579406): deployed and health/admin/catalog smoke PASS. Initial FTP #345 (37046579358) stopped before UI deployment on an obsolete active-source assertion requiring browser-local signup and a public Magic Link tab; owner-requested semantics were corrected in Test19/Test24 source contracts, without changing frozen numbered folders. FTP #346 (37046866862) QA/type-check/51 frontend tests PASS; Test29 and root publication/checksums PASS; live root reload loads index-DhRFzVDI.js and confirms both real login/signup forms, no public direct-link tab, and the real admin login destination. Actual owner Google sign-in/password setup and live authenticated logout remain OPEN until exercised; no production credentials or customer records inspected.

## Real customer profile isolation, logout and Why cards — 2026-10-02

- Owner reported a retry after browser Back, generic Google failure, a Baghdad Buyer profile and no visible logout. Root customer UI requires real backend authentication, but the customer snapshot adapter incorrectly merged a real numeric ID into a same-ID demo fixture. Null company/country fields inherited the demo name, address, flag and fake order metadata. This is a client fixture collision, not evidence of another real customer's records being disclosed.
- Replace all fields of a colliding record with server-authoritative identity or empty/default state; never borrow fixture details. Authenticated self-session/Magic Link responses now expose only own user name/email as read-only additions; profile PATCH still cannot alter user name/email or internal fields.
- Real customer dashboard omits browser-only photo/address editing and fabricated order timeline/payment actions; those are not implemented production order workflows. Name/company and WhatsApp retain existing guarded backend editing; email is read-only.
- Explicit logout is visible in Tracking header, uses the real server logout, disables while pending and reports failure without claiming logout.
- Invalid Socialite state now returns only a safe `auth_reason=expired` marker. Localized guidance starts a fresh login from the site rather than replaying browser Back. Authentication state validation is preserved; no stateless bypass.
- User explicitly requested visual repair of Why Armaghan. Test29 uses spaced mint cards, green 3px side accents, rounded corners and smaller numbered markers. Historical selectors/older numbered snapshots remain untouched; approved semantic colors and editor targets remain available.
- Root selector remains 29; revision bumped solely to repromote the updated active Test29. Test29 and root deployed and verified. Tests 01–28 remain frozen.
- Local type-check/build and 51 frontend tests across 13 files PASS. Backend CI #60 (37032219502), code deploy #16 (37032219436), and FTP #343 (37032219602), including Test29 and root promotion, PASS. Backend: 52 tests / 376 assertions. Fresh live status enabled:true, guest session 401, and invalid-state callback 302 to root with auth_reason=expired. Actual customer logout/repeat Google login acceptance remains OPEN.

| Rank | Profile method | Score | Reason |
|---:|---|---:|---|
| 1 | Authoritative full snapshot + own readonly identity | 9.5 | Removes actual ID-collision cause without account bypass |
| 2 | Clear browser data manually | 5 | Temporary, owner-dependent |
| 3 | Renumber demo customers | 4 | Future collisions remain possible |
| 4 | Hide every profile name | 3 | Conceals legitimate identity |
| 5 | Bypass OAuth state | 0 | Rejected security weakening |

Selected: option 1. Visual choice: semantic mint cards with green side dividers over one continuous blue-bordered block; preserve current four texts and editor registry.

Deployment closeout: implementation commit `8136892dc9c689c11f5d84a622236811a4f2b0d2`. Published active assets `index-BN1V-7yO.js` / `index-DMxFJMmU.css` passed HTTP verification; root version 29 HTML and selected assets verified, launcher still links Test29 first. Browser checked Persian Why cards on /t/29 (four rounded cards, 13.6px gap, no list border, 1px card border and 3px side accent); desktop overflow absent. Root browser reload confirmed the same new JS and localized expired-login guidance with a fresh sign-in button. Guest Tracking shows no demo customer profile. This is limited desktop visual acceptance, not full mobile/multilingual QA or real-account authenticated logout acceptance. Product/customer admin resources and product-image upload are implemented; production admin upload acceptance and complete customer order/payment workflows remain open.



## Google activation and frontend return repair — 2026-10-02

- Owner created the Google web client and installed private credentials in both `armaghan-data/.env` and `armaghan-backend/.env`; neither file nor secret is in Git. Live status now returns enabled:true.
- Read-only redirect check: HTTP 302 to accounts.google.com; expected client ID/callback, openid/profile/email and state present.
- Owner reported the Laravel welcome page after Google login. A fresh cancellation probe reproduced HTTP 302 to https://armaghantrading.com/backend/#/tracking?auth_error=google. Laravel prefixes frontend-relative redirects with the production /backend root.
- Repair uses the fixed production origin plus the existing strict root/numbered-test path allowlist for success, cancellation and invalid-state returns. No authentication bypass or email-only linking.
- Two regression cases force a /backend URL root and cover successful root login and cancellation to root/numbered paths, including hostile path fallback. Existing assertions now require the fixed production origin.
- Deployment/tests and live root cancellation probe PASS; see closeout evidence. Real Google signup/repeat login still OPEN; the screenshot is not proof of an authenticated customer session.
- Tests 01–28, Test29 assets, root selector, host credentials and database schema remain unchanged.

| Rank | Method | Score | Reason |
|---:|---|---:|---|
| 1 | Fixed frontend origin + existing strict path allowlist | 9.5 | Repairs actual subdirectory behavior without extra configuration |
| 2 | Configurable frontend origin | 8 | Flexible but adds a host setting |
| 3 | Change all backend base URL settings | 5 | Affects unrelated backend links |
| 4 | Redirect backend welcome route | 4 | Masks the wrong callback destination |
| 5 | Browser-side forwarding | 2 | Depends on loading another page |

Selected: option 1.


Last reconciled: 2026-10-02
Canonical source checkpoint for Test29 promotion: `1fafd9638777e26796acedc60b27dfd545736794`

This file is the **current operational source of truth** for the next run. Historical decision records remain useful, but when an older document conflicts with this file, use this file plus the latest `docs/HANDOFF.md` and `docs/BACKLOG.md`.


## Root Test29 and Google preparation — verified 2026-10-02

- Selected root version: 29, independently from active numbered development. Public root: https://armaghantrading.com/#/ ; /t/29 remains live; Tests 01–28 remain frozen.
- Frontend release 5d7af888959604518cca693e8747c8dbc36fb2f1, FTP run 37009040766: QA, 49 Vue tests across 13 files, type-check/build, scoped /t upload, published artifact checksum/launcher verification and root promotion PASS.
- Root/auth verification commit 1a4de2db706f6b14bcf2e5e63fc79c9ecaa34c86, FTP run 37009477221: static QA, eight promotion/rollback tests, guest customer HTTP 401, Products/Customers login boundary and root HTML/asset verification PASS; /t deployment SKIPPED.
- Google backend 0a07d9b98bfc8e5a4d297c02b4626064167255fb: Backend CI 37008505779 and Additive Deploy 37008505784 PASS. 49 backend tests / 348 assertions; one create-only identity table; consistent production database snapshot before migration; activation and admin-login smoke PASS.
- Live readiness reports Google provider configured=False. Credentials/Cloud registration and an actual Google signup/repeat login are OPEN. Never claim real Google login acceptance from mocked tests.
- Fresh desktop DOM at root: all four home eyebrows display:none; product badges read Producible in the observed English locale, 11.2px, weight 400, centered with zero center offset. Four locale source defaults preserve the concise equivalent (Persian تولیدپذیر). Root product links resolve /#/products; Home logo/hero/about images loaded. No mobile/tablet or broad theme/language visual acceptance is inferred.
- Real product upload/customer CRUD already exist in Filament. Guest /backend/admin/products redirects to /backend/admin/login. No authenticated upload/customer edit was performed in this run because no real admin session was available.
- Root accepts real Backend customer authentication only; browser-only review/admin roles do not authenticate root. Root LoginSheet hides local demo password/signup forms and offers backend Google/Magic Link and real admin entry.
- Root Style Profile reads production; /t reads staging. The root asset base does not change native/router/skip-link navigation destinations.
- Earlier pipeline attempts stopped safely before the new upload because of workflow-string escaping or historical text-only assertions. Corrected YAML was locally parsed; current release and all publication gates PASS. Use callback replacements for JavaScript replacement text containing shell dollar/apostrophe sequences.
- Follow-up P0: owner Cloud setup/private server credentials; real authenticated product-image/customer/Google acceptance; verify canonical root fragment paths for customer Magic Link/FavoriteShare against private server configuration (source historical defaults remain /t/27); complete deferred mobile/tablet and broad visual acceptance.

## Test29 — prior nonvisual release, 2026-10-02

Owner instruction: defer visual testing; preserve all previous versions; create a new numbered folder and prepend its link.
- Tests 01–28 are frozen. Test28 source checkpoint: `snapshot/test28-final` at `1fafd9638777e26796acedc60b27dfd545736794`.
- Test29 is the active release lane. Build/deploy target: `/t/29`; link `./29/index.html` is first in the launcher.
- Numbered build and FTP boundaries refuse targets <=28. Root was prohibited in the prior nonvisual run; the newer owner-authorized root promoter supersedes that restriction only for root index.html. Remote deletion remains prohibited.
- Test29 browser state uses `armaghan:test29:*`, including the formerly shared manual-language setting. No migration writes previous-version keys; production Backend sessions/catalog remain intentionally shared.
- Localized Why Armaghan eyebrow defaults for Persian, Arabic and Sorani; English retained. Existing locale/text overrides retain priority.
- Three new automated locale tests cover old-key preservation, new-version reload and four-language defaults. The dark-heading numeric guard is carried forward.
- Visual/device/admin-session acceptance remains OPEN and deferred by owner instruction. Automated PASS must not be presented as visual acceptance.
- Test29 release commit: `29dac32927dffd128a388a69fec5f9888ce352a9`. FTP Deploy #328, run `37003816665`: plan, QA, type-check, 44 Vue tests (12 files), build, FTP smoke, deploy-t and nonvisual HTTP verification PASS; deploy-root SKIPPED.
- Published index.html and initial JS/CSS SHA-256 match the built artifact; published launcher lists `./29/index.html` first. Tests 01–28 build/deploy boundaries PASS. All 19 prior numbered source blobs present in Git retain their starting SHA.
- First attempt #327 (`37003682710`) deployed successfully but its verification incorrectly included external font stylesheets. The verifier now checks build-owned local assets; corrected #328 passes. Theme bootstrap also uses the Test29 namespace.
- URLs: https://armaghantrading.com/t/29/ and https://armaghantrading.com/t/index.htm . Visual/device/authenticated-browser acceptance is still OPEN; no visual tests performed in this run.

| Rank | Method | Score | Reason |
|---:|---|---:|---|
| 1 | Isolated Test29 + localized defaults + automated freeze/storage contracts | 9.5 | Concrete release without visual-layout changes; protects old snapshots |
| 2 | Copy current release into a new lane only | 8.0 | Safe but fewer functional corrections |
| 3 | Documentation-only closeout | 6.0 | Does not deliver a new version |
| 4 | Add more admin capabilities | 4.0 | Core capabilities already exist |
| 5 | Layout redesign | 2.0 | Requires deferred visual testing |

Selected: option 1. Storage rationale: https://developer.mozilla.org/en-US/docs/Web/API/Web_Storage_API/Using_the_Web_Storage_API .

## Executive status

The original minimal backend is operational. Preserved custom administration is partially integrated: the customer slice is delivered, products/media/settings integration and actual-owner authenticated browser acceptance remain open. Core admin/catalog/media/customer-auth/share flows, guarded deployment, encrypted off-host SQLite backup and artifact round-trip restore verification are live. Test 29 is the active mutable review lane; FTP #328 deployment and nonvisual HTTP verification passed. Final end-to-end QA and handoff remain open; visual checks are deferred by owner instruction.

Current UI lane:
- Tests 01–28: frozen/immutable; preserve their live folders.
- Test29: active; build/deploy only /t/29 and the mutable launcher.
- Source checkpoint: snapshot/test28-final. Browser keys: armaghan:test29:*.
- Test29 inherits the delivered Test28 behavior with storage isolation and localized Why Armaghan defaults.
- Prior FTP #320 and #325 evidence belongs to Test28, not Test29. Test29 FTP #328 and nonvisual HTTP verification passed.

Current backend lane:
- Laravel 13.34.0 is live under `/backend`.
- Production SQLite is active and is the primary database.
- Production Style Profile persistence/version/restore semantics are verified against the live SQLite database.
- One real active production administrator exists.
- Filament login page is live.
- Category/Subcategory/Product/Customer Filament Resources are implemented and live in production. Taxonomy commit `4281034656...` passed Backend CI #31; Product/Customer commit `de450a15...` passed Backend CI #33; Backend Code Deploy #3 passed with all four Resource routes returning HTTP 200.
- A guarded code-only Backend update lane is operational: it refuses Composer/migration drift, creates a pre-swap SQLite snapshot, stages private/public code, HTTP-smokes the release and rolls code back on failure. Run #1 deliberately rolled back after a public-permission smoke failure; Runs #2–#5 passed.
- Public catalog API is live at `/backend/api/catalog/categories` and `/backend/api/catalog/products`; production catalog is initialized with **3 categories / 6 subcategories / 18 products**.
- Test 27 performs non-blocking Hybrid Sync: Backend-managed names/status/active state overlay the browser catalog. Server product media now overrides the local gallery when present; local media/specs remain fallback while a server gallery is empty or the API is unavailable.
- Production product media is live through Spatie Media Library + the official Filament integration: ordered `product-gallery`, JPEG/PNG/WebP validation, 8 MiB and 5000×5000 limits, metadata-stripping re-encode, non-cropping/non-upscaling card/thumb conversions, and public delivery through `/backend/storage`.
- The production `media` table is migrated and the public-storage symlink is verified. The current 18 bootstrapped products still have zero server media rows until an authenticated administrator uploads real product images; Test 27 therefore continues to show the existing local fallback images without regression.
- A guarded additive Backend deploy lane is production-proven for create-only migrations/dependency changes. Backend Additive Deploy #3 created a consistent SQLite snapshot, applied exactly one additive media migration, verified the public storage link, passed all HTTP smokes, and cleaned temporary files.
- Customer Backend session + Magic Link core is live. Links are high-entropy, hash-only in SQLite, one-time, expiring, revocable, audited and rate-limited. Customer session state is separate from Filament admin auth and rotates the session identifier without clobbering admin authentication.
- Test 27 hydrates a real Backend customer session when present and no longer generates browser-only Magic Links. Production links keep the bearer token only in the browser hash fragment (`#/magic/<token>`), then consume it through a fixed same-origin POST endpoint; the token is not sent in the initial HTTP URL.
- Backend Code Deploy #9 deliberately rolled back when the first token-in-path production smoke exposed a real 404 routing incompatibility. Repair commit `10d040c7...` moved consumption to the fixed POST endpoint; Backend Code Deploy #10 PASS with guest customer session = 401 and GET on POST-only consume endpoint = 405.
- FTP Deploy #314 PASS delivered the repaired Test 27 auth flow; root deployment remained skipped.
- Persisted FavoriteShare/WhatsApp is live. New share links are server-backed, carry only a high-entropy bearer token in the browser fragment, store only SHA-256 token hashes in SQLite, preserve product order, validate active taxonomy/products and resolve through a fixed POST endpoint.
- Guest shares expire after 7 days; authenticated customer shares expire after 30 days and may be revoked by that customer. Public share creation/resolution is CSRF-protected + rate-limited; customer revocation requires the real Backend customer session.
- Test 27 no longer generates product-code-in-URL links. Old `?shared=v1:` links remain read-only compatible, while all new sharing uses persisted Backend links. The WhatsApp action sends the persisted share URL through the existing seller WhatsApp path.
- Favorite changes now attribute real customers using `currentCustomerId`; the old hard-coded Customer #1 bug is removed.
- Backend Code Deploy #11 PASS with FavoriteShare resolve route GET = 405; independent Production probe confirmed both FavoriteShare issue and resolve endpoints exist as POST-only. FTP Deploy #316 PASS delivered Test 27; root skipped.
- Read-only live browser acceptance with an intentionally invalid 64-character share token reached the persisted shared-favorites route and ended in the correct invalid/unavailable state without crash or legacy product-code sharing.
- Final browser-authenticated Test 27 Style Profile acceptance is still open but is non-blocking. A read-only check of the saved browser profile redirected to `/backend/admin/login`, so delivery does not rely on that session.

## Production backend — verified complete

The current production backend is no longer a planning-only or local-only implementation.

Verified:
- PHP 8.3.33 on LiteSpeed.
- PDO SQLite: PASS.
- SQLite3 extension: PASS.
- SQLite 3.53.4.
- Private SQLite file create/read/write: PASS.
- SQLite foreign keys: PASS.
- SQLite `VACUUM INTO`: PASS.
- Private application/data sibling outside `public_html`: PASS.
- HTTPS execution: PASS.
- Laravel 13.34.0 deployed under `/backend`.
- Host-only `.env` and APP_KEY remain outside Git and logs.
- Production SQLite database created outside `public_html`.
- Production migrations: PASS.
- Initial consistent SQLite snapshot: PASS.
- Core schema checks: users, products, customers, Style Profile tables, media table.
- Production image runtime requirements: GD + EXIF accepted by the guarded Media deployment preflight.
- Public product media link: `/backend/storage` active.
- Public backend smoke:
  - `/backend/` → 200
  - `/backend/up` → 200
  - `/backend/api/style-profile/staging` → 200
  - `/backend/admin/login` → 200
  - `/backend/api/catalog/categories` → 200
  - `/backend/api/catalog/products?per_page=1` → 200
  - `/backend/api/customer/session` → 401 for guest
  - GET `/backend/api/customer/magic-link/consume` → 405 (POST-only route exists)
  - GET `/backend/api/favorite-shares/resolve` → 405 (POST-only route exists)
- Public Laravel permissions normalized to LiteSpeed-safe directories `0755` / files `0644`; private data permissions were not widened.

## First production administrator — verified complete

A real production active-admin account exists.

- Account identity: `admin@armaghan.local`.
- No fixed/default password is committed or documented.
- Plaintext credential is not stored in the repository.
- Provisioning/recovery flow is fail-closed and tested.
- Active-admin policy and password hashing verification passed.
- Filament login route smoke passed.

Do not create another bootstrap admin unless the existing account is intentionally rotated/removed through an explicit administration decision.

## Test29 visual editor — deployed implementation (visual acceptance open)

Current Test29 editor capabilities inherited from delivered Test28:
- Admin-only visual editor launcher.
- Mobile non-modal resizable bottom sheet.
- Direct touch selection.
- Explicit chooser for nearby/nested targets.
- Structured element browser.
- Recoverable hidden targets.
- Text editing for registered locale-aware targets.
- Approved-token text/background/border controls.
- Hide/show and per-target reset.
- Contrast guard for explicit text/background token pairs.
- Browser-local autosave.
- Sanitized JSON export/import for manual portability only.
- Laravel Style Profile adapter.
- Sync status: local / checking / saving / synced / conflict / error.
- 900ms debounced authenticated server draft save.
- `expected_checksum` optimistic-concurrency protection.
- Explicit HTTP 409 conflict resolution.
- Staging Publish.
- Recent immutable version history.
- Restore-as-new-version behavior.

### Current Test 28 customer color state

Canonical approved palette:
- Brand Green: `#21946A`
- Brand Blue / logo-background blue: `#0714C2`
- Brand Mint: `#C8E3DB`
- Brand White: `#FFFFFF`
- Brand Gold: `#FFB514`

The old canonical blue `#151EDA` is retired for Test 28.

Current semantic defaults:
- Header/Footer/Hero brand chrome → Brand Blue `#0714C2`.
- Home light-mode page background → Brand White.
- Primary Home panel background → Brand Mint.
- `home.page` background is editable but the root itself cannot be hidden.
- `home.content` is separately hideable/selectable.

The current Test 28 source inherits the validated color/saveability implementation; Test 27 remains its frozen predecessor.

## Style Profile production persistence — verified server-side

Production Style Profile persistence has passed a live transactional acceptance test against the real production SQLite database.

Production acceptance Run `36878931948`: **PASS**.

Verified inside one outer transaction:
- draft save;
- first staging Publish;
- second changed staging Publish;
- Restore from the first version as a new immutable version;
- staging publication pointer update;
- ActivityLog writes.

Observed during the transaction:
- 3 new immutable versions;
- 3 new activity records.

The outer transaction was rolled back. Post-test logical state exactly matched pre-test state. Temporary public helper cleanup passed.

### Only remaining editor persistence acceptance

The server-side semantics are no longer uncertain.

The remaining acceptance is **browser-only**:
1. authenticate through the real Filament login at `/backend/admin/login`;
2. open Test29 as the authenticated administrator;
3. edit a registered target;
4. confirm the editor reaches server-synced state;
5. reload and confirm the draft persists;
6. verify the shared draft from another browser/device session;
7. Publish staging through the UI;
8. Restore an older version through the UI;
9. confirm CSRF/session behavior stays valid throughout.

Do not weaken authentication or add a bypass just to automate this final browser step.

## Backend domain state

Implemented models/foundation:
- User
- Category
- Subcategory
- Product
- ProductSpecValue
- SpecDefinition
- Customer
- FavoriteShare
- MagicLink
- ActivityLog
- StyleProfile
- StyleProfileVersion
- StyleProfilePublication

Implemented production-facing controllers:
- Public Style Profile read controller.
- Admin Style Profile controller.
- Public Catalog controller + API Resources for Category/Product reads.

Production catalog state:
- 3 active categories.
- 6 active subcategories.
- 18 active products initialized from the canonical MVP fixture.
- Public catalog filtering/search/pagination and managed-code metadata are live.

Customer session/Magic Link and FavoriteShare/WhatsApp HTTP flows are implemented and live. The customer-facing session API exposes only the bounded self-service profile subset; internal notes/user ownership are not returned or customer-writable. FavoriteShare resolution returns only ordered active product codes and share expiry metadata; it does not expose share-owner identity.

Product media ownership/upload is implemented and live. The public catalog returns a `media` array per Product and Test 28 prefers that server gallery when non-empty. Because the current production Products do not yet have uploaded server media, local media/specs remain the visible fallback. Test 28 still retains demo/local auth and local favorites storage only as review/resilience fallbacks; real customer sessions, Magic Links and newly generated FavoriteShare links are Backend-backed.

## Persistence policy

Current policy:
- SQLite is the primary Laravel database for local/dev/test/production.
- JSON is ordinary import/export/fixture interchange only.
- MySQL/MariaDB is optional later as a logical mirror/export/disaster-recovery target.
- Never dual-write normal live requests to SQLite + MySQL.
- Primary production backups use a consistent SQLite `VACUUM INTO` snapshot.
- Daily off-host backup is operational through GitHub Actions at `23 1 * * *` UTC with 14-day artifact retention.
- The plaintext SQLite snapshot is encrypted on the production host with AES-256-GCM before leaving the host; the public GitHub repository artifact contains only ciphertext plus a non-secret manifest.
- SQLite Off-host Backup #1 (`36974268704`) PASS. Artifact `armaghan-sqlite-backup-36974268704` is 337,663 bytes and expires 2026-10-16T06:35:35Z.
- The restore-drill job downloaded that uploaded artifact, decrypted it only on an isolated runner, verified ciphertext/plaintext checksums, `PRAGMA integrity_check`, `foreign_key_check`, full table inventory and critical row counts, opened/rolled back a write transaction, then discarded the restored plaintext.
- First-run encryption used the explicit documented fallback `ftp_password_kdf_fallback` because `ARMAGHAN_BACKUP_PASSPHRASE` is not yet configured. Add a dedicated backup secret as an operational P1; do not rotate away the fallback secret while retained fallback-encrypted artifacts still need recovery unless the old secret is retained securely.

## Remaining core delivery — estimated 1 bounded run + opportunistic browser acceptance

This estimate excludes open-ended new customer UI revisions. Browser-authenticated Style Profile acceptance and one real Filament product-image upload remain opportunistic parallel checks rather than blockers because their server-side foundations are already production-verified.

1. **Final end-to-end QA + handoff**
   - mobile/tablet/desktop;
   - RTL/LTR;
   - light/dark;
   - admin/customer permissions;
   - live catalog/media/auth/share paths;
   - backup/restore evidence;
   - deployment smoke and final delivery documentation.
   - mobile/tablet/desktop;
   - RTL/LTR;
   - light/dark;
   - admin/customer permissions;
   - backup/restore;
   - deployment smoke;
   - final customer changes and documentation.


## Highest-priority open items

P0:
1. Complete final end-to-end QA/handoff across responsive/RTL-LTR/light-dark/auth/share/backup/deploy paths.
2. Complete browser-authenticated Test29 Style Profile + one real Filament product-image upload + one real customer Magic Link acceptance opportunistically when a valid real admin session is available.
3. Keep code-only, additive and off-host-backup lanes green through final handoff.

P1:
- Add a dedicated GitHub Actions secret `ARMAGHAN_BACKUP_PASSPHRASE` and let future backups use it instead of the current FTP-secret KDF fallback. Retained fallback-encrypted artifacts still require the original fallback secret until they expire.
- No other core P1 blocker remains; optional acceptance/polish only.

P2 / optional:
- MySQL logical mirror/export with verification.
- Supabase only if a concrete managed-Postgres/Auth/Storage/Realtime need appears.
- Advanced analytics/orders/queues unless required for delivery.

## Recent authoritative runs

- Production Style Profile Acceptance `36878931948`: PASS.
- FTP Deploy #272: PASS after production acceptance merge; UI/root deploy skipped.
- FTP Deploy #273: PASS for final documentation; UI/root deploy skipped.
- Backend CI #30: PASS for first-admin provisioning tooling.
- FTP Deploy #270: PASS for first-admin provisioning merge.
- FTP Deploy #268: PASS for live Test 27 color/saveability update.
- Backend CI #31: PASS for Category/Subcategory Resources.
- Backend Code Deploy #2: PASS for first live taxonomy deployment after automatic rollback of failed Run #1.
- Backend CI #33: PASS for Product/Customer Resources.
- Backend Code Deploy #3: PASS; `/backend/up`, admin login, categories, subcategories, products and customers all HTTP 200.
- Backend CI #35: PASS for Public Catalog API + Test 27 Hybrid Sync implementation.
- FTP Deploy #295: PASS; Test 27 type-check/unit/build/deploy completed and root deployment was skipped.
- Backend CI #36: PASS; Backend Code Deploy #4 PASS with both public catalog endpoints HTTP 200.
- Backend CI #37: PASS for guarded catalog-bootstrap command/fixture.
- Backend Code Deploy #5: PASS; bootstrap command deployed with no migration/dependency drift.
- Catalog Bootstrap Production #1: PASS; pre-state 0/0/0, SQLite snapshot created, post-state 3 categories / 6 subcategories / 18 products, representative codes verified, helper cleanup PASS.
- Backend CI #39/#40: PASS for additive-deploy infrastructure and up-only additive migration validator.
- Backend CI #41: PASS on the pre-merge Product Media PR; official locked Spatie/Filament dependencies, media migration and feature tests all passed.
- Product Media merge: `4d8f660a06df7b4e4a340502d6d65255b41cc949`.
- Backend CI #42: PASS after merge.
- Backend Code Deploy #7: code-only deployment correctly skipped because Composer/migration drift was detected.
- Backend Additive Deploy #3: PASS — backup created, dependency changed=true, exactly 1 create-only migration applied, `public_storage_link=true`, health/login/products/catalog smokes all 200, temp cleanup PASS.
- FTP Deploy #307: intentionally failed before Test27 deployment because the anti-hotlink policy rejected an external-domain URL used only in a unit-test fixture; no `/t/27` mutation occurred.
- Test-fixture repair: `b3722d3f4643ee403972b7a03215339e5aa802ad`.
- FTP Deploy #308: PASS — immutable contracts, web-stock policy, TypeScript, Vue unit tests, Test27 production build and FTP smoke all passed; `deploy-t` PASS; `deploy-root` skipped.
- Customer/Magic Link PR #7 merge `e311d732...`; Backend CI #47 PASS; Backend Code Deploy #8 PASS.
- FTP Deploy #311 exposed a stale historical Test19 browser-Magic-Link assertion; future-proof repair `5b1bcffc...`; FTP #312 PASS.
- Backend Code Deploy #9 activated the follow-up release but correctly rolled back after production returned 404 for the token-in-path Magic Link smoke; rollback health + cleanup PASS.
- Magic Link routing repair PR #8 merge `10d040c7d1706d68a5018e1240c797011440000c`; Backend CI #49 pre-merge + #50 post-merge PASS.
- Backend Code Deploy #10 PASS — snapshot created, no drift, all CRUD/catalog smokes 200, guest customer session 401, fixed Magic Link consume route GET 405, cleanup PASS.
- Independent production probe reconfirmed customer session 401 and fixed Magic Link consume GET 405.
- FTP Deploy #314 PASS — Test27 customer-auth contract, TypeScript, Vue tests/build, FTP smoke and `/t/27` deploy PASS; root skipped.
- FavoriteShare PR #9 pre-merge Backend CI #51 PASS; squash merge `76f80179292f743cb54443e540602bba47c4d8bb`.
- Backend CI #52 PASS after merge.
- Backend Code Deploy #11 PASS — snapshot created, no dependency/migration drift; existing backend smokes remain green and FavoriteShare fixed resolve route GET returns 405; cleanup PASS.
- Independent production probe: GET `/backend/api/favorite-shares` = 405 and GET `/backend/api/favorite-shares/resolve` = 405, confirming both fixed POST endpoints exist.
- FTP Deploy #316 PASS — Test27 FavoriteShare source contract, TypeScript, Vue unit tests/build, FTP smoke and `/t/27` deploy PASS; `deploy-root` skipped.
- Live read-only invalid-token acceptance PASS on `/t/27/#/favorites/share/<invalid-token>`: shared route loaded, server resolution attempted, invalid/unavailable shared-list state rendered, no crash or legacy local-code share.
- Backend CI #53/#54 pre-merge and #55 post-merge: PASS for Test28/off-host-backup hardening.
- Test28 hardening squash merge: `6593f6124bfccfd25ae5899d2690608940214905`.
- SQLite Off-host Backup #1 (`36974268704`): PASS — production `VACUUM INTO`, AES-256-GCM encryption before transfer, encrypted artifact upload, temp cleanup PASS.
- Restore drill in the same workflow: PASS — downloaded artifact digest matched; decryption, plaintext checksum, integrity check, foreign-key check, table inventory/counts and write-transaction rollback all PASS; restored plaintext not persisted.
- FTP Deploy #318: stopped before build/deploy on a stale Test26 current-active-test assertion; Test26/27/root remained untouched.
- Follow-up `39bd08346420a74bd217ee908c6782c7403da7f1`: future-proofed only the historical Test26 contract and added the Test28 release marker.
- FTP Deploy #319: stopped before deployment because two session unit-test fixtures still wrote Test27 browser keys; runtime source was already correctly namespaced Test28.
- Follow-up `3158ffbbcd1ad7fe95cf3c096d0cf576b1b8cfde`: aligned those fixtures with Test28.
- FTP Deploy #320: PASS — frozen Test26/27 contracts, Test28 visual/auth/share contracts, backup safety contract, Python syntax, TypeScript, 41 Vue unit tests, Test28 production build and FTP smoke PASS; 112 files uploaded; `deploy-t` PASS; `deploy-root` skipped.
- Independent live verification: Test28 and frozen Test27 both load successfully; launcher lists Test28 first.
- FTP Deploy #321: failed before any deployment because the historical Test26 rule-text assertion still expected the exact freeze range 01–26 after Test27 was frozen.
- Contract-only fix `72a45224e0bd6fd6547f30f2fa9983e6f1424a0b` made the Test26 freeze assertion range-aware without changing Test26/27 runtime content.
- FTP Deploy #322: PASS; full static QA/Python checks passed, frontend build intentionally skipped, `deploy-t` and `deploy-root` skipped.

## Rules for the next run

1. Read this file first, then `docs/HANDOFF.md`, `docs/BACKLOG.md`, `docs/PROJECT-RULES.md`, and the shared lock.
2. Do not repeat production SQLite activation; it is complete.
3. Do not repeat first-admin bootstrap; one active admin already exists.
4. Do not claim Style Profile server persistence is pending; its production service semantics are verified.
5. The remaining Style Profile task is browser/session/CSRF acceptance.
6. Keep Test 26 immutable.
7. Keep Tests 01–28 immutable. Test 29 is the only active mutable UI lane; use only `armaghan:test29:*` browser-state keys.
8. Never store production plaintext credentials in Git, docs, logs or artifacts.
9. Do not rebuild product-media ownership, Customer/Magic Link, FavoriteShare/WhatsApp or off-host backup/restore; all are live. The next P0 is final end-to-end QA + delivery handoff.
10. Do not claim the 18 current Products have server media yet: their last verified API `media` arrays were empty and Test 29 retains local media fallback until an admin uploads images. Recheck current catalog data before any new media-state claim.
11. Use the guarded additive lane for future approved create-only migration/dependency changes; ordinary Backend changes stay on the code-only lane.


## 2026-10-02 bounded documentation reconciliation

- Compared canonical status, latest handoff, backlog, Test28 hardening audit and released shared lease with main `9ee3a206...`.
- Corrected stale next-run instructions that still called Test27 mutable and off-host backup pending. Historical deployment evidence remains unchanged.
- GitHub Actions run `36975429480` for the inspected main head is completed/success; this is prior-run evidence, not a new browser acceptance.
- No live multi-device or authenticated browser acceptance was performed in this documentation run. Final QA remains open; do not infer completion from existing unit/build/route checks.

## 2026-10-02 live Test28 guest acceptance and dark-title repair

Inspected source baseline: `cfb28307e3be1c56e54a210fcc211ab8f1323a31`. Shared write lease: `codex-20261002-live-qa`. Public browser viewport: 1363 × 936 CSS pixels.

| Check | Direct evidence | Result |
|---|---|---|
| Desktop Home / Products | Home rendered; 18 products rendered with local image fallback; no horizontal overflow in observed viewport | PASS for observed viewport |
| Product search / category filtering | Code 11001 returned 1 product; Baby returned 6; clearing returned 18 | PASS |
| Product details | Specs opened and closed; focus returned to invoking control | PASS |
| Locale direction on Products | en/ltr; fa/rtl; ar/rtl; ckb/rtl; no observed horizontal overflow | PASS for Products only |
| Guest favorites | Initially empty; added 11001; survived reload; removed test selection and returned to empty | PASS for browser-local guest persistence only |
| Invalid persisted-share route | Synthetic all-zero token reached generic invalid/expired state without crash | PASS; valid issue/resolve/WhatsApp not newly tested |
| Real admin session | Filament login form displayed; no authenticated session available | OPEN; no login bypass or credential change |
| Dark normal-size headings | Green #21946A on card #1E2024 measured 4.279:1 | FAIL before repair |
| Mobile/tablet, valid customer link, admin editor/upload | Not performed; supported browser API has no viewport-resize control; RC has no connected devices | OPEN |

Repair: change only the dark `--role-heading-strong` default to approved Brand Mint #C8E3DB. Light defaults, explicit editor overrides, brand tokens and frozen Tests 01–27 stay unchanged. Mint/card contrast is 12.015:1. Numeric regression coverage resolves actual CSS defaults and requires >=4.5 on dark page/card/input surfaces; the new check fails on original source and passes after repair.

| Rank | Dark title method | Score | Reason |
|---:|---|---:|---|
| 1 | Approved mint through existing semantic heading role | 9.5 | Strong measured contrast; one declaration; preserves palette and override priority |
| 2 | Approved white through heading role | 9.0 | Readable but less color differentiation |
| 3 | Mix green with white | 8.0 | Adds another combination to verify |
| 4 | Lighten dark surfaces | 6.0 | Larger visual scope |
| 5 | Increase title sizes | 4.0 | Alters layout to address color failure |

Selected automatically: option 1. Standard: https://www.w3.org/WAI/WCAG22/Understanding/contrast-minimum.html (normal text >=4.5:1). Responsive simulation is not equivalent to real hardware: https://developer.chrome.com/docs/devtools/device-mode.

Next: confirm guarded Test28 build/deploy and fresh live mint computed color; complete mobile/tablet and authenticated acceptance when available. Do not close broad final-delivery checklist from this partial desktop evidence.

### Verified closeout for this bounded run
- Implementation commit: `88d4172e50413dec3d8951ba093535894b659db4`.
- FTP Deploy #325, run `37001349371`: QA, type-check, 41 Vue tests, active Test28 build, FTP smoke and deploy-t PASS; deploy-root SKIPPED. Frozen snapshot guard PASS for all 27 frozen tests.
- Fresh live browser read after deployment: heading/category/product title RGB(200,227,219), card RGB(30,32,36), no horizontal overflow at 1363 CSS px; repaired default is live (12.015:1 on card).
- Fresh guest request to `/backend/admin/products` redirected to `/backend/admin/login`; no authenticated-admin acceptance is inferred.
- Additional observed copy follow-up: Persian Home still displays the English eyebrow "Why Armaghan?". Keep the broad multilingual Home acceptance open and localize that label in the next bounded fix.
- Mobile/tablet and valid customer/admin/share/WhatsApp end-to-end acceptance remain open. This run advances partial final QA; it does not declare final project delivery.


## Owner-authorized root Test29 release — 2026-10-02
Owner explicitly authorizes root index replacement with selected Test29 while preserving /t. Independent selector: config/root-release.json. Tests 01–28 remain frozen; Test29 is mutable. Requested eyebrow/producible defaults and real Filament navigation are included. Root deployment and guest-auth readiness verified PASS; Google credential activation remains OPEN. Google requires owner Cloud credentials; no real login acceptance is claimed.

Google customer signup/sign-in implemented using official Socialite with a create-only identity table and eleven backend security cases. Root hides local demo password/signup and ignores browser-only admin roles; real customer sessions remain authoritative. Backend additive deploy PASS; real Google credentials are still absent; never claim live Google login before actual provider verification.

### Verified deployment closeout
- Repair source commit: 8676b469d0159848f775689c55b7286e35cc932f; corrected test request commit: ad02b2e50ad5e6dd70196533a700c10a947a26b9.
- Backend CI 37029811115 PASS: 51 tests / 362 assertions, dependency audit and secret hygiene PASS.
- Backend Code Deploy 37029811169 PASS: pre-swap SQLite backup created, no dependency/migration drift, health/admin/catalog smokes 200, guest session 401, POST-only endpoints 405, temporary cleanup PASS.
- FTP QA 37029811251 PASS; root and numbered UI deploys skipped. No frontend/frozen version files changed.
- Independent live cancellation probe now returns HTTP 302 to https://armaghantrading.com/#/tracking?auth_error=google (before repair: /backend/#/tracking).
- First attempt was safely stopped before host deployment by two test-harness 404s: forced production URL root also prefixed the test request. Using explicit localhost test request URLs keeps the /backend URL generator simulation intact; all tests now PASS.
- Real Google signup/repeat login still requires owner confirmation; no actual-account login success is inferred from cancellation/automated tests.

- Independent numbered-path probe: start at /backend/auth/google/redirect?return_path=/t/29/, cancel with the same cookie jar, return HTTP 302 to https://armaghantrading.com/t/29/#/tracking?auth_error=google. Provider enabled:true and guest session 401 remain verified after deploy.

### Explicit owner direction for the NEXT run — 2026-10-02 21:48 Tehran

Preserve the polished administration interface developed in the numbered tests and connect that same structure/styling to real Laravel services. Do not replace it wholesale with default Filament screens or recreate its appearance. This work begins after the current login/admin/badge/footer run is completed.

- Inventory existing admin sections and retain their approved layouts/components.
- Add real server-authoritative admin-session/read permissions; never trust browser-local admin role.
- Migrate one vertical feature at a time (product CRUD/media, customer management, device settings/editor), keep API writes allowlisted, CSRF-protected and authorized, and expose real loading/errors.
- Remove demo data/write paths from production admin; preserve frozen snapshots and reversible review alternatives.
- Existing Filament remains the operational access/fallback while the custom interface is integrated. Reuse canonical Eloquent/media/customer/style services; avoid parallel data stores.
- Prepare a bounded five-option decision and checkpoint at the delivered commit before implementation. Acceptance must include actual authenticated writes/reload; local visual resemblance alone is insufficient.


### Current-run deployment closeout — 2026-10-02
- Delivered runtime commit: 35dabd38d557e7b40567cdfafe863ecd63be34b8. Backend CI #61: 63 tests / 473 assertions PASS; Backend Code Deploy #17 PASS. FTP #346: QA, smoke, Test29 publish and root promotion all PASS. Published HTML/JS/CSS checksum verification PASS; /t launcher puts active Test29 first, /t preserved.
- Live root refreshed and observed script /t/29/assets/index-DhRFzVDI.js (CSS index-DAjvviV3.css). Root and Test29 show sign-in/signup tabs, password confirmation/strength guidance, Google button, administrator shortcut, and no direct-link login tab. Administrator shortcut resolves to https://armaghantrading.com/backend/admin/login with real Email/Password form.
- Live computed styles: seven producible labels have transparent background, 0px border, gold rgb(255,181,20), weight 400. Footer brand is last after columns, physical right/end alignment, logo approximately 110.4px square (6.9rem, +130%); no desktop horizontal overflow.
- Read-only live probes: Google enabled true; guest customer/security 401; login without CSRF 419; administrator login page 200. No production signup, password change, owner sign-in or authenticated logout was performed by the agent. Actual owner Google provisioning/private password setup, authenticated acceptance and full mobile/tablet QA remain OPEN. Password reset/email-verification delivery remains unconfigured; do not describe the whole backend as feature-complete.
- NEXT run remains the owner's explicit preserved-design Laravel administration integration above; do not substitute default panel styling for the approved custom test interface.


### Custom administration integration follow-up details
- Product migration must map by backend IDs/codes explicitly; never merge server record IDs into browser fixtures. Preserve existing ProductEditorPanel code/category inference and four-language fields, but validate canonical taxonomy and ProductSpecValue relations server-side. Reuse SanitizeProductMedia and product-gallery collection; do not persist compressed browser data URLs as canonical images.
- The transport reuses the existing same-origin CSRF retry handling; no extra authentication/token storage layer. A stale-positive administrator identity is cleared on failed recheck; save failure preserves draft and never reports success. Security tests include actual isolated SQLite create/edit/reload/deactivation, account ownership restrictions and logout session isolation.
- GitHub concurrency keeps one running and one pending run; a newer pending run can replace an older pending run even with cancel-in-progress:false. Do not assume a canceled frontend-only commit will be deployed by a later root-only commit. Final publication therefore explicitly changes frontend and root revision together at 429698496f643137f89416b42f43d1ea601b2077; verify both final deployment jobs before closeout.


### Verified custom admin closeout — 2026-10-02
Runtime: 429698496f643137f89416b42f43d1ea601b2077 (backend source 7812c241f7cb5a3997fe3bf4767396e3c3977fb9). Backend CI 70/538 PASS, frontend 53/14 PASS; FTP #351 all jobs PASS. The custom route is https://armaghantrading.com/#/admin, retained at /t/29/#/admin. Initial live guest screen shows login-required guidance without any customer table. Final root loads the final bundle; no authenticated browser or mobile acceptance is inferred.
First owner Google login still proves ownership and opens private account security; its management link now targets the preserved custom route. Real password admin sign-in routes there too. The stock Filament panel remains available for product/media operations until their existing custom forms are connected in the next slice. Do not claim all custom administration is migrated or production writes were acceptance-tested with the actual owner's browser.
