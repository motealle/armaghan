# Armaghan Trading B2B Catalog — Project Handoff

## Standing owner authorization / lock retirement — 2026-10-05
Owner explicitly granted permanent permission for project changes and publication in GitHub repository motealle/armaghan, and abolished the shared repository write-lock protocol because work is now single-threaded under owner control. Root/backend AGENTS, PROJECT-RULES 64–74 and Copilot instructions are aligned. Prior lock/approval-blocker records are historical. Platform checks, secret protection, frozen versions and guarded host deployment remain unchanged. Card-gallery release is now authorized; backend CI and publication evidence are pending below.


## Product card performance and gallery — 2026-10-05

Owner explicitly authorized takeover of the same stopped run's lock. Base ae40af0. Prior claims of complete card acceptance are superseded by the owner's 80-second load report; actual device performance/visual acceptance remains OPEN.

Implemented: cards retain canonical server thumb/card/detail metadata and image dimensions; responsive srcset selects 320/800 WebP, detail is requested only after opening the viewer. Removed the eager CSS background-image from cards (it bypassed lazy image loading) and the padded edge-extend frame. Plain contain preserves both portrait and existing landscape garment silhouettes. Card arrows and horizontal swipe navigate real persisted media; PhotoSwipe is lazy-loaded for zoom/pinch, keyboard navigation and dismissal. Existing local media remains staged fallback only when no server media exists. No new dependency/schema change.

Missing legacy derivatives are generated once on the controlled media route using GD, source dimension bounds, nonblocking per-directory lock, transparent aspect-preserving resampling and atomic unique temporary files. Original remains untouched; generated flag recorded. Original fallback has a short cache lifetime rather than a year of immutable caching. New uploads already generate derivatives synchronously and now persist dimensions. Existing portrait guidance remains; old landscape photos are not rejected or destructively cropped.

Validation: local frontend type-check PASS, 89 unit tests across 20 files PASS, production build PASS, four relevant media source contracts PASS; backend verification is through existing CI gates before production activation (local PHP unavailable). Implementation commit f7ce17e is local only. Automatic approval review rejected pushing it to GitHub because external publication was not explicitly authorized. No remote implementation branch, merge or deployment was performed. Final follow-up: obtain explicit publication authorization, run Backend CI, then merge/deploy and measure production image size/time. Release/deploy and real-device acceptance must be reported separately; no promise of a measured sub-second or few-second production result until measured. Historical tests 01–28 untouched.

| Rank | Method | Score | Reason |
|---:|---|---:|---|
| 1 | Responsive variants + missing-derivative repair + existing PhotoSwipe | 10 | Addresses transfer size, eager backgrounds, framing and gestures together |
| 2 | Single resized image | 7 | Degrades detail zoom |
| 3 | Frame-only repair | 5 | Leaves heavy downloads |
| 4 | Increase timeout | 2 | Does not reduce transfer |
| 5 | Forced crop | 1 | Can cut the garment |

Selected 1. References: https://photoswipe.com/data-sources/ ; https://photoswipe.com/options/ ; https://spatie.be/docs/laravel-medialibrary/v11/converting-images/regenerating-images . Specialist terms: srcset (انتخاب اندازهٔ تصویر در مرورگر), derivative (نسخهٔ سبک ساخته‌شده از عکس), contain (نمایش کامل عکس بدون بریدن), pinch (بزرگ‌نمایی دو انگشتی), CI (آزمون خودکار پیش از انتشار).


## Product card media hotfix — 2026-10-05

- Root cause treated as a stale-client/state path rather than an upload failure: backend media routes already served real images, but product cards could continue using cached catalog data or a renderer state that had already fallen back to a placeholder.
- Catalog API fetches now use `cache: no-store` during the active healing window.
- Backend product-media URLs receive a deterministic release revision query (`v=20261005-card-hotfix-1`) so cached 404/old image responses cannot mask newly available media.
- ProductMediaCarousel remounts SmartImage when the canonical product image URL changes, clearing stale fallback state.
- Release commit `069b8d938c726884300342c069218bea21ffbd5f`; FTP Deploy `37297704339` PASS. QA: type-check PASS, 19 test files / 85 tests PASS, production build PASS, Test29 deploy PASS and root promotion PASS.
- No browser/TinyFish visual acceptance was performed. Owner screenshot of the affected product card is the visual acceptance source.

## P0 healing run — 2026-10-05
- Temporary manager aliases are time-boxed and map to the real owner/business-admin accounts; plaintext passwords are not stored in the repo. Laravel remember-login remains valid until explicit logout or owner revocation.
- Manager password setup accepts any non-empty confirmed value. Setup state moved to a dedicated additive table so production deployment no longer needs ALTER TABLE.
- Only one global logout control remains; tracking/drawer duplicates are removed.
- For one week, each fresh site visit performs one cache-busted reload before mounting, then cleans the query marker.
- Products page and live admin panel both use the canonical backend product manager/editor. The products page refreshes the backend catalog on entry so routed uploaded media replaces stale local placeholders.
- Visual acceptance is intentionally deferred to owner screenshots; no TinyFish/browser visual testing is used in this run.

## Admin UX / product editor batch — 2026-10-05

Home appearance editing is home-only. Product management is products-page-only for real server-admin sessions; the edit affordance sits beside the product code, the manager includes Add Product, and both products/admin surfaces reuse BackendProductEditor rather than creating a second editor. Product media management remains server-authoritative with real-media cards, explicit unavailable state, selection, deletion, reorder and primary ordering. Home/product media is served through application-controlled routes.

Session persistence and one-time setup behavior are included, Tracking copy remains stable after login, and redundant exit controls were reduced. Backend CI 37277405193 PASS: 129 tests / 1169 assertions. No visual acceptance was performed; request owner screenshots after release.

## P0 — Gallery Manager و ویرایش مدیر از صفحه محصولات — ۲۰۲۶-۱۰-۰۵

- علت واقعی placeholderهای گمراه‌کننده: API رسانه را داشت اما URLهای `/backend/storage/media/...` روی production پاسخ 404 می‌دادند. مسیر تحویل رسانه به route عمومی کنترل‌شده Laravel منتقل شد تا فایل thumb/card/detail از storage خصوصی برنامه stream شود و به symlink وب‌سرور وابسته نباشد.
- گالری مدیریت فقط به تعداد رسانه واقعی کارت می‌سازد؛ صفر رسانه یعنی empty-state. هر کارت عکس واقعی، checkbox، جابه‌جایی و برچسب تصویر اصلی دارد؛ خرابی فایل صریح نمایش داده می‌شود و قابل انتخاب/حذف است.
- حذف چندرسانه‌ای manager-only با revision و ownership check اضافه شد؛ stale revision و media خارجی رد می‌شوند.
- فقط session واقعی `admin.identity` روی صفحه محصولات دکمه ویرایش را فعال می‌کند. همان BackendProductEditor به شکل Bottom Sheet/AdaptivePanel باز می‌شود. backend row موجود با code دقیق ویرایش می‌شود و seed-only آینده با همان داده اولیه materialize می‌شود.
- CI شاخه `37256170956` PASS: backend 125 tests / 1132 assertions، Composer audit پاک؛ frontend 84 tests / 19 files، type-check و production build PASS. انتشار production هنوز باید بعد از merge تأیید شود.

## P0 — نجات مسیر عکس محصول — ۲۰۲۶-۱۰-۰۴

- انتخاب چندعکس، پیش‌نمایش و ارسال ترتیبی مقاوم پیاده شد؛ هر آپلود از revision تازه عکس قبلی استفاده می‌کند و شکست یک فایل، محصول یا آپلودهای تأییدشده را برنمی‌گرداند.
- عکس ورودی رابط تا ۲۰MB می‌تواند انتخاب شود و قبل از شبکه تا حداکثر ضلع ۱۹۲۰px بهینه می‌شود؛ سرور همچنان سقف ۸MB، MIME/محتوا/ابعاد، مجوز، rate limit و بازنویسی امن را اعمال می‌کند.
- خروجی سرور: master پاک‌سازی‌شده + WebP thumb 320/q72 + card 800/q78 + detail 1600/q82، بدون crop/upscale اجباری؛ رسانه قدیمی fallback امن دارد.
- Backend CI 37230605259 PASS. Frontend CI آزمایشی 37230693451 و 37230789948 PASS: type-check، 82 تست/18 فایل و production build. workflow موقت حذف شد.
- پذیرش واقعی مدیر/موبایل پس از deploy باز است.

## P0 — زیرساخت ورود OTP موبایل — ۲۰۲۶-۱۰-۰۴
پکیج `fouladgar/laravel-otp ^6.2` با lock تولیدشده واقعی Composer نصب و در `eace74da4a58c58703b58a8f36a586a5aa8ccdba` ادغام شد. نصب اولیه روی PHP 8.3 با Composer validate، migrate:fresh، کل تست‌های بک‌اند و audit موفق بود. Backend CI `37223470009` موفق؛ Backend Additive Deploy `37223470049` نیز نصب وابستگی، تست، ساخت vendor و انتشار واقعی روی هاست را با موفقیت کامل کرد. Backend Code Deploy به‌درستی به علت dependency drift از مسیر code-only عبور نکرد و FTP Deploy تغییری در UI/ریشه نداد. ورود پیامکی برای مشتری هنوز عمداً فعال نشده است: خرید/تنظیم سرویس SMS، API key و pattern، تغییر محدود هویت موبایل/ایمیل و مسیرهای request/verify OTP با rate limit و تست لازم است. OTP نباید در لاگ یا کد/ریپو ذخیره شود.

## P0 — Short WhatsApp FavoriteShare links — 2026-10-04
Owner-authorized takeover of stopped run `codex-20261004-short-favorite-share`. Selected design: new 22-character cryptographically random token (>128-bit entropy) plus `/#/s/<token>`; raw token remains hash-only in storage and fixed POST resolution remains unchanged. Historical 64-character tokens and `/#/favorites/share/<token>` routes stay compatible. Stale numbered-test share-path configuration is normalized to root short links. No schema/dependency change. Source commit `9e1631820006884aea08a6c06b1d7385d18e9555` is verified and released. Backend CI run 37221086606 PASS: 124 tests / 1107 assertions. Backend Code Deploy 37221086686 PASS: code-only activation, backup, no dependency/migration drift, live health/admin/catalog smokes PASS. FTP Deploy 37221086643 PASS: 80 frontend tests across 18 files, type-check/build, Test29 deployment verification and root version 29 promotion PASS. New shares use `/#/s/<22-char-token>`; historical 64-character `/#/favorites/share/<token>` links remain compatible.

تأیید نهایی انتشار این بچ: کد e6d0d9a49f8fae970cba653d08c450fa18e6ff92؛ اجرای37218127601 در همه مراحل آزمون، اتصال هاست، انتقال نسخه۲۹ و فعال‌سازی ریشه موفق شد. خواندن تازه ریشه و /t/29/ و فایل index-CehMWVjX.js همگی۲۰۰؛ برابر بایت‌به‌بایت با خروجی محلی آزموده‌شده، SHA256=a9ddd88291015d079e28a75dd837797647cde7d2833289fcfe884adbb8edaa6e. فایل فعال حاوی پیام‌های ارتباط مستقیم، شماره فروش و «ذخیره و ارسال عکس» است. هیچ درخواست خصوصی، ارسال واقعی پیام، تغییر حساب یا پایگاه داده در این بچ انجام نشد؛ پذیرش واقعی آپلود/دستگاهی همچنان باز است.

## ارتباط مستقیم مشتری و ورود با موبایل — ۲۰۲۶-۱۰-۰۴

دکمه پیاده، آزموده و منتشر شده؛ بررسی عمومی فعال موفق است. مبنا58f6a034676b908a51240e62002ce246a7869b94؛ قفلcodex-20261004-customer-direct-contact. کارت «ارتباط با مسئولان ارمغان» در حساب/پنل مشتری مستقل از ورود/محصول/سفارش، گفت‌وگوی خالی با شماره فروش موجود989933509793 باز می‌کند. لینک از سرویس مشترک ساخته می‌شود؛ متن، شناسه، مشخصات حساب/محصول/سفارش به واتساپ اضافه نمی‌شود. چهار زبان، عنوان و نام دسترس‌پذیر و حفاظت مرورگر مقصد رعایت شدند؛ پیام واقعی ارسال نشد. ۷۹ آزمون رابط در۱۸فایل، بررسی نوع‌ها، ساخت برنامه و۳۱بررسی انتشار موفق‌اند. پشتیبانی ابزار Vue (سازنده رابط) موجود به تنظیمات آزمون افزوده شد تا نمایش واقعی کارت در چهار زبان بررسی شود؛ وابستگی تازه‌ای نصب نشد.

آپلود عکس محصول قبلاً با انتشار4fad101/اجرای37184885632 تحویل شده است؛ انتخاب عکس سپس «ذخیره و ارسال عکس» برای مدیر کسب‌وکار فعال است. مشتری عادی اختیار ویرایش عکس کاتالوگ ندارد. آزمون خصوصی واقعی همچنان باز است.

ورود/ثبت‌نام با موبایل یا ایمیل هنوز فعال نیست. نقشه اجرایی docs/PHONE-OR-EMAIL-LOGIN.md اولویت بچ مستقل بعدی است: حساب اصلی با شماره یکتا/ایمیل اختیاری و رمز، بدون ایمیل صوری و وابستگی پیامک. users.email اکنون اجباری است؛ تغییر محدود ساختار و مسیر انتشار با پشتیبان و آزمون لازم است. شماره تماس فعلی به‌تنهایی شناسه ورود نیست. بازنشانی رمز/قطع نشست و سایر موارد پیشین باز می‌مانند. نسخه‌های۰۱–۲۸ و سرور/حساب‌ها در این بچ تغییر نکرده‌اند.


## آپلود فوری عکس محصول — ۲۰۲۶-۱۰-۰۴

تأیید نهایی: کد4fad101b82ecda95e55e8adecd1131e684cef166؛ اجرای37184885632 در آزمون، اتصال، انتقال نسخه۲۹ و فعال‌سازی ریشه موفق شد. فایل عمومی اصلی و /t/29/ هر دو index-RyIuwwOI.js را ارائه می‌دهند؛ برابر بایت‌به‌بایت با فایل آزموده‌شده، SHA256=c1941613bf40e3a73586b1b13aaecc5f64c7de0106409fbff5af3bd8afa5235f و شامل «ذخیره و ارسال عکس». دو تصویر ناقص نوزادی/بچگانه بازیابی و با اصل۱۰۰۰۷۶/۸۸۸۰۵بایتی برابرند. ۱۱۲ فایل عمومی برابر بدون بازفرستادن حفظ شدند. ۷۴ آزمون رابط،۴ آزمون بازیابی و۳۱ بررسی انتشار موفق‌اند. پذیرش واقعی با حساب مدیر/گوشی همچنان باز است. هیچ آزمون خصوصی تولیدی در این ران اجرا نشد.

اصلاح تکمیلی پس از شکست37184397000: تطبیق بایت‌به‌بایت فایل عمومی با خروجی ساخت پیش از ارسال؛ تنها فایل متفاوت ارسال می‌شود. فایل متفاوت با نام موقت ارسال و بعد از تکمیل با همان روش جایگزینی اتمی ریشه تغییر نام می‌یابد. تصویر نوزادی بازیابی و با اصل۱۰۰۰۷۶بایتی برابر شد؛ تصویر بچگانه هم باید پس از انتشار بررسی شود. ۴ آزمون بازیابی/تطبیق و۳۱ بررسی انتشار موفق‌اند. انتشار همچنان در انتظار تأیید است.

انتقال اولیه و تکرار آن در اجرای37184108039 بر اثر TimeoutError در ارسال فایل ثابت متوقف شدند؛ فعال‌سازی ریشه انجام نشد. اصلاح بازیابی محدود انتقال: حداکثر۳ تلاش، اتصال تازه، بازکردن فایل از بایت صفر، مهلت۶۰ثانیه؛ خطای مجوز دوباره امتحان نمی‌شود. ۳ آزمون بازیابی و مجموع۳۱ بررسی انتشار موفق شدند. انتشار اصلاح در انتظار تأیید است.

| رتبه | روش انتقال | امتیاز | دلیل |
|---:|---|---:|---|
| ۱ | تلاش محدود با اتصال تازه و بازفرستادن کامل فایل ثابت | ۱۰ | بازیابی فایل ناقص بدون تغییر دامنه |
| ۲ | افزایش مهلت تنها | ۷ | اتصال خراب را درمان نمی‌کند |
| ۳ | تکرار کل انتشار | ۵ | دو بار در همان نقطه شکست خورد |
| ۴ | انتقال دستی فایل‌های منتخب | ۳ | احتمال جاافتادن فایل و فقدان تطبیق |
| ۵ | اعلام انتشار پیش از تکمیل | ۰ | خلاف شواهد |

ادامه حساب‌ها: CustomerSession فعلاً فقط شناسه مشتری را نگه می‌دارد؛ اصلاح قطع نشست باید ورود رمزی، گوگل و لینک ورود را پوشش دهد، حفظ سوابق/هویت و مرز حساب مدیر رعایت شود، و آزمون‌های نشست قدیمی به قرارداد تازه به‌روز شوند. این تحلیل انجام شد؛ بازنشانی رمز هنوز پیاده نشده است.

پیاده‌سازی، آزمون و انتشار تأیید شدند. انتخاب عکس برای محصول تازه و فرم تغییرکرده فعال است. عکس تنها با دکمه صریح «ذخیره و ارسال عکس» ارسال می‌شود؛ ابتدا محصول ذخیره شده و شناسه/نسخه پاسخ سرور برای ارسال عکس استفاده می‌شود. خطای ذخیره مانع ارسال عکس است؛ خطای عکس پس از ذخیره، محصول موجود و فایل انتخابی را نگه می‌دارد. نتیجه نامشخص همچنان ارسال مجدد را مسدود می‌کند. عکس انتخابی بدون ارسال با تأیید خروج حفاظت می‌شود؛ حذف انتخاب ممکن است. محدودیت شش عکس، هشت مگابایت، نوع/ابعاد/محتوا و پاکسازی سرور تغییر نکرده‌اند.

۷۴ آزمون رابط در ۱۷ فایل، بررسی نوع‌ها، ساخت برنامه و ۳۰ بررسی انتشار موفق‌اند. آزمون واقعی با حساب مدیر و گوشی هنوز باز است؛ ادعای بدون نقص یا پذیرش خصوصی نداریم. تغییر فقط رابط نسخه۲۹ و انتخاب ریشه است، سرور/داده/نسخه‌های۰۱–۲۸ تغییر نکردند. بعد از تأیید انتشار، اولویت بعدی بازنشانی امن رمز و قطع نشست‌های قبلی است؛ زمان‌بندی تازه ایجاد نشده.

| رتبه | روش | امتیاز | دلیل |
|---:|---|---:|---|
| ۱ | انتخاب عکس و ذخیره/ارسال صریح ترتیبی | ۱۰ | ساده، استفاده از مسیر موجود، بازیابی مرحله‌ای |
| ۲ | ذخیره جدا و انتخاب بعدی | ۷ | امن ولی مسیر فعلی مبهم |
| ۳ | ذخیره خودکار هنگام انتخاب | ۵ | ذخیره ناخواسته فرم ناقص |
| ۴ | مسیر آپلود موازی | ۳ | پیچیدگی و ذخیره‌ساز موازی |
| ۵ | عکس صرفاً مرورگری | ۱ | ناپایدار و فاقد انتشار |

انتخاب: گزینه۱. منبع بررسی امنیت: https://cheatsheetseries.owasp.org/cheatsheets/File_Upload_Cheat_Sheet.html .


تأیید انتشار: ثبت کد `8639b3e37944f8c2147f02e26414ca57b507ce10`؛ اجرای انتشار `37173258992` در همه مراحل آزمون، اتصال هاست، انتقال نسخه ۲۹ و فعال‌سازی ریشه موفق شد. خواندن تازه صفحه اصلی و `/t/29/` و فایل فعال `index-D5M8Xq_T.js` همگی پاسخ ۲۰۰؛ اثرانگشت فایل هر دو مسیر `0645b5f6328f8e31f6df1e7d629928e4ea37b3006ddfe53c2e5d0a3c323b2a83` و شامل پیام‌ها/حفاظت تازه است. عکس هیرو ۱۶۱۹۹۰ بایت و اثرانگشت تأییدشده قبلی حفظ شده است.

مرز تأیید: بررسی خودکار مجوز، دریافت نتیجه بررسی مسیرهای خصوصی مدیریت را به دلیل احتمال دسترسی به اطلاعات کاربران/مشتریان رد کرد؛ درخواست تکرار نشد. تأیید این ران از آزمون‌های مستقل و فایل‌های عمومی حاصل شده و پذیرش خصوصی/دستگاهی باز است. هشدار خروج مرورگر در بسته‌شدن اجباری برنامه روی موبایل تضمین ندارد؛ این بچ ذخیره خودکار پیش‌نویس ایجاد نکرد. منابع رسمی بررسی روش: https://developer.mozilla.org/en-US/docs/Web/API/Window/beforeunload_event و https://developer.mozilla.org/en-US/docs/Glossary/Idempotent .

## ران دستی: اطمینان‌پذیری فرم محصول — ۲۰۲۶-۱۰-۰۴

انجام‌شده و منتشرشده؛ آزمون محلی و بررسی فایل عمومی فعال تأیید شد. مبنا: `43c9e46b8cb021e3872176dd91e537172e7a754e`؛ قفل مشترک: `codex-20261004-manual-product-reliability`. این ران زمان‌بندی تازه ایجاد نکرد.

- تغییرات فرم محصول موجود و محصول تازه با تأیید صریح کنار گذاشته می‌شوند؛ ادامه ویرایش، ورودی را حفظ می‌کند. بستن یا جابه‌جایی مسیر هنگام درخواست جاری مسدود است؛ خروج از صفحه با ورودی ذخیره‌نشده هشدار مرورگر دارد.
- خطای کد تکراری/نامعتبر، زیردسته، مشخصات، عکس و محدودیت درخواست، پیام چهارزبانه مشخص دارد. فقط نام فیلدهای خطا از سرور گرفته می‌شود؛ متن خام خطا یا مقادیر ارسال‌شده نمایش داده نمی‌شوند.
- پاسخ نامشخص شبکه/خطای سرور یا تعارض نسخه، ارسال مجدد در همان فرم را می‌بندد. ابتدا باید فرم بسته و فهرست تازه دریافت شود؛ درخواست عکس خودکار تکرار نمی‌شود. خطای اعتبارسنجی ورودی را نگه می‌دارد و اصلاح و تلاش دوباره مجاز است.
- ۶۸ آزمون رابط در ۱۶ فایل، بررسی نوع‌ها، ساخت برنامه و ۳۰ بررسی انتشار موفق‌اند. آزمون‌های تازه حفظ ورودی، تغییر مشخصات تو‌در‌تو، جلوگیری از بستن حین ذخیره، خطاهای مشخص، پالایش نام فیلدها و عدم تکرار آپلود نامشخص را پوشش می‌دهند.
- سرور، ساختار پایگاه داده، ورود، عکس‌های واقعی مشتری و نسخه‌های ۰۱ تا ۲۸ تغییر نکردند. انتخاب ریشه همچنان ۲۹ است. آزمون با حساب واقعی و پذیرش بصری همچنان باز است.

ادامه: بچ مستقل حساب‌ها برای بازنشانی رمز و قطع نشست‌های قبلی؛ سپس حذف با حفظ سوابق، تنظیمات مشترک واتساپ/ظاهر، فاکتور و رسید، ایمیل و پشتیبان رمزگذاری‌شده فایل‌ها. قطع نشست مشتری باید مسیر اختصاصی ورود مشتری را نیز پوشش دهد؛ استفاده صرف از خروج نشست معمول مدیر کافی نیست. شرط تأیید سطح مدل نباید به جای انجام کار دستی، مانع مصنوعی شود؛ محدودیت واقعی ابزار صادقانه گزارش می‌شود.

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

**Current-state pointer:** read `docs/CURRENT-STATUS.md` first. Older numbered sections below preserve useful history; when an older statement conflicts with the current-status file or a later handoff section, the newer verified state wins.

## 1. Project identity

- Repository: `motealle/armaghan`
- Current prototype/development scope: `/t`
- Root website (`/public_html`) is a protected landing page and must remain untouched by prototype work.
- Test 26 customer-facing UX is frozen pending customer feedback; do not mutate `/t/26`.
- Core Backend MVP productionization is complete. The active lane is the Test29 nonvisual release; visual QA is deferred by owner instruction. Tests 01–28 are frozen; current corrections belong only in Test29.

## 2. Deployment and hosting contract

- FTP secrets already configured in GitHub Actions:
  - `FTP_SERVER`
  - `FTP_USERNAME`
  - `FTP_PASSWORD`
- FTP smoke test has succeeded.
- Root landing deployment has succeeded.
- `/public_html/index.html` and `/public_html/logo.png` are the approved root landing assets.
- No remote delete/sync-delete is allowed.
- Prototype deployments must be scoped to `/public_html/t` only.
- Root `/public_html` must not be overwritten by prototype changes.
- All prototype links and asset references must be relative so `/t` can later be moved to the root without path rewrites.
- GitHub Actions is the CI/CD mechanism.

## 3. Business

Armaghan Trading is a B2B clothing production/trading catalog for wholesale/export-oriented customers, primarily serving Iran, Iraq, Kurdistan Iraq and Arab markets.

Main categories:
1. نوزادی / Baby
2. بچگانه / Kids
3. زنانه / Women

Phase one is not a conventional ecommerce checkout. There is no online payment, final checkout or fixed public price. Negotiation, price announcement and final deal happen through WhatsApp.

The website must:
- present products clearly;
- help customers select products and/or custom services;
- collect structured request details;
- generate a clean WhatsApp message with product codes, options, quantities and request type;
- feel fast, beautiful, simple and app-like, especially on mobile.

## 4. Prototype architecture

All prototype work lives under `/t`.

Required archive structure:
- `/t/index.htm` — launcher
- `/t/01/index.htm` — Test 01
- `/t/01/assets/app.css`
- `/t/01/assets/app.js`
- future tests: `/t/02/`, `/t/03/`, etc.

Never overwrite an earlier test. Each numbered test is an archive snapshot. New tests are added to the top of the launcher.

Shared files may live under `/t/shared/`, but each test should remain self-contained where practical.

## 5. Language and localization

The product is Persian-first and RTL-first.

Core labels include:
- خانه
- محصولات
- دسته‌بندی‌ها
- نوزادی
- بچگانه
- زنانه
- استعلام قیمت
- ارسال به واتساپ
- مشخصات ثابت
- قابل سفارشی‌سازی
- افزودن به لیست استعلام
- تولید سفارشی
- برند اختصاصی
- بسته‌بندی اختصاصی

Do not build full multilingual logic now, but design strings and structure so later translation is straightforward.

## 6. Visual direction

### Dark theme
The dark visual reference is the current Midjourney web experience: image/product-first presentation, deep dark surfaces, restrained chrome, strong visual hierarchy, compact controls, rounded surfaces and an editorial/creative feel. Midjourney's current web experience centers visual feeds, search/filter controls and fullscreen/detail interactions. citehttps://docs.midjourney.com/hc/en-us/articles/33329460426765-Website-Overview

### Light theme
The light visual reference is Digikala: Persian ecommerce information density, strong search/navigation, practical product cards, familiar RTL commerce patterns, clear CTAs and functional hierarchy. This is a taste reference, not a request to copy Digikala branding or proprietary UI.

### Armaghan brand layer
Use Armaghan's own identity above both references:
- primary navy: `#151DAB`
- landing/logo blue: `#0613BF`
- white: `#FFFFFF`
- gold: `#FFD80D`
- mint: `#C8E3DB`
- action green: `#21946A`
- light background: `#F7F8FC`
- dark background: `#090B18` or refined equivalent
- text: `#111827`
- muted: `#6B7280`
- danger: `#DC2626`

The result must be B2B/export-professional, not childish, even for baby/kids products.

## 7. Mobile UX

Mobile-first PWA-like shell:
- sticky bottom navigation;
- large touch targets;
- bottom sheets for product details and request forms;
- sticky primary CTAs;
- clear icons and labels;
- safe-area padding;
- low cognitive load.

Suggested bottom nav:
1. خانه
2. محصولات
3. استعلام
4. خدمات
5. واتساپ

On desktop, this may transform into a top nav/sidebar while retaining the same information architecture.

## 8. Product cards

Each product card should support:
- 1–3 images / visual switcher;
- product code;
- category;
- availability: موجود or ناموجود / قابل تولید;
- short summary;
- WhatsApp action;
- secondary heart/wishlist;
- details action.

Details open as a mobile bottom sheet and desktop modal/drawer.

Product details must separate:
- fixed specs: locked / non-changeable;
- variable specs: selectable/customizable.

Fixed examples:
- تعداد هر پک: ۱۲ عدد
- سایزبندی پک: ۰ تا ۱۲ ماه
- جنس پایه: نخ پنبه
- مدل پایه: استاندارد

Variable examples:
- رنگ موردنظر
- تعداد درخواستی
- نوع اجرا
- بسته‌بندی
- توضیحات تغییرات

Never communicate fixed/variable state by color alone; use icons, labels and disabled/badged states.

## 9. Six business request paths

1. خرید ساده محصول موجود — same product, quantity only.
2. خرید محصول موجود با تغییرات — product + selected changes + quantity + notes.
3. تولید از مدل ناموجود/قابل تولید — simple purchase disabled; production request enabled.
4. تولید سفارشی — product group, specs, quantity, image-upload placeholder, notes.
5. برند اختصاصی — Armaghan design + customer brand OR customer design + customer brand; label, hang tag, patch, print, embroidery, logo placeholder, notes.
6. بسته‌بندی اختصاصی — product group, packaging material, dimensions, color, print, brand/logo, quantity, notes.

## 10. Recommended product decision

Use guest-first, login-later.

A guest can browse, view details, add inquiry items, start any request path, review the request and send it to WhatsApp.

Login belongs later to proforma, official invoice, packing list, order timeline, payment status and reorder history.

Primary business concept is **لیست استعلام / سبد استعلام**, not merely Wishlist. Heart can remain a secondary shortcut.

## 11. Test 01 — Unified Request Builder

Recommended UX model: one central request builder collecting product and service requests.

Entry points:
- product WhatsApp button;
- product details;
- افزودن به استعلام;
- bottom-nav استعلام;
- خدمات: تولید سفارشی، برند اختصاصی، بسته‌بندی اختصاصی.

Lightweight flow:
1. نوع درخواست
2. context محصول/خدمت
3. quantity/options
4. review
5. WhatsApp

Request chips:
- خرید ساده
- خرید با تغییرات
- تولید از مدل ناموجود
- تولید سفارشی
- برند اختصاصی
- بسته‌بندی اختصاصی

Available product: enable خرید ساده + خرید با تغییرات.
Unavailable/producible product: disable خرید ساده; enable تولید از مدل ناموجود.
Service-originated requests do not need product context.

## 12. WhatsApp

Use a simple encoded WhatsApp URL with placeholder phone `989000000000`.

Before opening WhatsApp, show a message preview.

Message should contain:
- request type;
- product/service context;
- code/category/status where applicable;
- fixed specs;
- selected variable specs;
- quantity;
- notes;
- product link placeholder where applicable.

Do not attempt direct file attachment through the simple WhatsApp link.

## 13. Homepage prototype

Include:
1. header: logo, language visual, search, customer panel placeholder;
2. hero: B2B/export positioning + product/request CTAs;
3. category cards: نوزادی، بچگانه، زنانه;
4. trust/value: تولید عمده، کنترل کیفیت، آماده صادرات، اسناد و اعتبار تجاری;
5. product grid with available/unavailable examples;
6. services: تولید سفارشی، برند اختصاصی، بسته‌بندی اختصاصی;
7. inquiry summary/floating CTA;
8. simple footer.

## 14. Mock data

Use JavaScript mock data. At least 8 products:
- 3 baby
- 3 kids
- 2 women

Each product should have id, code, title, category, subcategory, status, images, fixedSpecs, variableSpecs, packQuantity, minOrder and summary.

Use visual/gradient placeholders rather than real product photography.

## 15. Prototype roadmap

01 — Unified Request Builder — Recommended
02 — Product Card Bottom Sheet Focus
03 — RFQ Cart / سبد استعلام
04 — Services Hub
05 — Customer Profile Model (comparison only)
06 — WhatsApp-first Quick Actions
07 — Classic Catalog

## 16. Acceptance criteria for Test 01

- launcher exists and links to Test 01;
- Test 01 exists and is polished/customer-presentable;
- mobile-first responsive;
- RTL Persian UI;
- relative paths only;
- homepage sections present;
- product cards with 1–3 image switcher;
- detail bottom sheet/modal;
- fixed specs with lock icon;
- editable variable specs;
- available/unavailable behavior;
- mobile bottom navigation;
- services entry points;
- unified request builder;
- inquiry list/summary;
- WhatsApp preview and encoded link;
- no backend/database/auth/API;
- no heavy framework/build dependency;
- no absolute internal asset paths.

## 17. Future Laravel mapping (not implemented now)

Expected future stack:
- Laravel
- Filament
- Blade / Livewire / Alpine
- Tailwind
- MySQL/MariaDB
- object storage for images/PDFs
- GitHub Actions
- PWA-like frontend

Future entities include categories, products, product_images, product_specs, inquiry_requests, inquiry_items, customers, proforma_invoices, official_invoices, packing_lists and order_timelines.

## 18. Explicit prohibitions

Do not build backend, database, real login, real APIs, payment, checkout, PrestaShop, WordPress or a framework-heavy prototype. Do not store real customer files. Do not use real product images. Do not hide the main action behind login. Do not use absolute internal paths. Do not put paths 4–6 only inside profile in the recommended version.

## 19. Customer testing questions

1. آیا مسیر انتخاب محصول و ارسال به واتساپ واضح است؟
2. آیا مشخصات ثابت و موارد قابل سفارشی‌سازی قابل فهم‌اند؟
3. آیا «لیست استعلام» بهتر از «علاقه‌مندی» است؟
4. آیا خدمات سفارشی بهتر است عمومی شروع شود یا داخل پنل؟
5. آیا Bottom Navigation حس اپلیکیشنی و راحتی می‌دهد؟
6. آیا WhatsApp preview مفید است؟
7. آیا مشتری خارجی بدون لاگین می‌تواند درخواست بفرستد؟
8. آیا مسیرها زیاد و گیج‌کننده‌اند؟
9. آیا ظاهر به اندازه کافی صادراتی، تمیز و حرفه‌ای است؟
10. کدام تست به نسخه نهایی نزدیک‌تر است؟

## 20. Current status

- FTP smoke test: PASS.
- Root landing deployment: PASS.
- `logo.png` is present in repository.
- `/t` remains the active product-design/prototype workspace.
- Tests 01–26 are released snapshots and must remain immutable.
- Test 26 is the frozen customer-review snapshot; snapshot branch: `snapshot/test26-final`.
- Any further UI work starts at Test 27 or higher. Run 2 customer-approved integration is delivered: per-viewport Appearance controls now drive the live Header/Home/Products/Footer; Home uses single Hero + About + Why + Capabilities overlay + product-category banners by default; Favorites lists are anonymously shareable.
- Test 26 rollback checkpoint: `rollback/test25-pre-test26` at `328f6c48ea9c4f1346702282ddb6d21ae1685a16`.
- Test 26 ranked UX/architecture decisions: `docs/TEST26-UX-AUDIT.md`.
- Test 26 image handoff contract: `docs/TEST26-IMAGE-REQUIREMENTS.md`.
- Test 26 customer clarification script: `docs/TEST26-CUSTOMER-QUESTIONS.md`.
- Test 26 foundation delivery: frontend build commit `1b92b0f69a3e2ab68a9e8ce21b93eb3e735f2a74`; **FTP Deploy Run #131 PASS**; `deploy-root` skipped; Test 25 unchanged.
- Test 26 Run 2 rollback: `rollback/test26-foundation-pre-integration` → `152e20e5ec062f48319a45727c7448d4ee68843e`.
- Test 26 customer integration build: `fafcb0d31e248e3c12ca4533ee85708f5ab08502`; **FTP Deploy Run #185 PASS**; full frontend build/type-check/unit tests/contracts passed; `deploy-root` skipped.
- Mutable `/t` launcher completion: `57d47e3919a9b87a3fb540723c832ca64fd23d4a`; **FTP Deploy Run #186 PASS**.
- Remaining Test 26 asset work is explicit rather than hidden: final capability images, final three banner originals/generated assets, and the optimized local derivative of the customer-approved About handshake image are tracked in `docs/TEST26-IMAGE-REQUIREMENTS.md`.
- Mobile PWA navigation invariant for Test 26+: the original app-like five-item BottomNav is mobile-only (<48rem/768px). From tablet upward, those primary routes live in the expanded blue top navigation. This placement is enforced at runtime and must not become an arbitrary mode that can duplicate/remove primary navigation.
- SmartImage AVIF invariant: never derive an `.avif` URL merely because a `.webp` exists. Advertise AVIF only for asset families known to ship a real AVIF sibling; Test 26 selected media are WebP-only.
- Mobile PWA BottomNav regression fix deployed from `c49a37c0aa0e56759e4a3418c8c798232f4e64b5`; **FTP Deploy Run #195 PASS**; `deploy-root` skipped; Test 25 and older snapshots unchanged.
- Live Test 26 P0 screenshot fix deployed from `596c86bf1009741a95d1c76ec168bc5c4519104e`; **FTP Deploy Run #208 PASS**. This fixes WebP→nonexistent-AVIF fallback, enforces mobile BottomNav vs tablet/desktop top-nav state, cache-busts the Test 26 launcher, skips root deployment, and leaves Test 25+ older snapshots untouched.
- Test 26 Run 7 mobile UX polish deployed from `633e7e1399db1e6cca84256bcfdd516004088476`; **FTP Deploy Run #223 PASS**. Header mode/hamburger are reversible again; mobile default hamburger is off; BottomNav is mobile-only with responsive-emulation fallback; mobile footer is edge-to-edge; product title/code are centered; pale active subcategory chips use readable text. Root deploy skipped; Test 25 and older snapshots unchanged.
- Test 26 Run 8 behavior: on mobile the Footer is Home-only; tablet/desktop keep the existing Footer scope. Mobile compact header exposes language selection directly in the blue top bar and does not duplicate it in the drawer.
- Permanent technical-term explanation rule: in user-facing Armaghan explanations, define each specialist term on first use with a short Persian explanation in parentheses; canonical rules are `docs/PROJECT-RULES.md` 75–77.
- Test 26 Run 8 deployed from `5a7ca47291b64bd70cf1fed2b4a32f94e9753798`; **FTP Deploy Run #234 PASS**. Mobile Footer is Home-only, mobile top bar exposes language selection, technical terms must be explained in Persian parentheses on first use, `deploy-root` skipped, Test 25 and older snapshots unchanged.
- Operational project memory hierarchy: `docs/PROJECT-RULES.md` → `docs/HANDOFF.md` → `docs/BACKLOG.md` → current test audit / asset docs. Chat memory is secondary.


## Test 26 current image state

Owner-selected generated images are wired into Test 26: Hero 1-2, About 2-2, Production 3-1, Export 4-3, Trade Documents 5-2, Baby banner 6-3 and Kids banner 7-3. They remain at native 800×450 / 800×300 resolution and are described as conceptual, not verified Armaghan photography. The previous hero file and Test 25 snapshot are preserved. The final Women-banner candidate was automatically selected as option 8-2, a 800×300 scene of loose fully covered outfits on headless mannequins; the slot now uses a local WebP and no placeholder.

Deployment record: selected Test 26 media commit `e2d9931f9c5398de9320ef698051d0f9c56db991` passed FTP Deploy Run #188. QA, FTP smoke and `deploy-t` passed; `deploy-root` was skipped. Test 25 and earlier snapshots were untouched.

Run 4 deployment record: women banner choice 8-2 is live in Test 26. Commit `a35dfacde3140618899b3784db7275edea85dba2`; FTP Deploy Run #190 passed QA, FTP smoke and scoped `deploy-t`; `deploy-root` was skipped. All eight Test 26 image slots now have local selected media; Test 25 and earlier snapshots remain unchanged.


## 21. Backend MVP transition

- Laravel is now installed under `platform/backend`; bootstrap resolved Laravel Framework **13.34.0**.
- Filament Panel Builder is installed at **5.9.0** and `app/Providers/Filament/AdminPanelProvider.php` exists.
- `composer.json` and `composer.lock` are committed; `.env` and `vendor/` remain ignored.
- Permanent Backend CI validates Composer metadata, local SQLite migrations, framework/admin major versions, tests, security audit and secret hygiene.
- Local/dev/test uses SQLite. Verified production hosting lacks PDO SQLite but has PDO MySQL, so production uses MySQL/MariaDB.
- Core domain models now exist for Customer, Category, Subcategory, Product, specification definitions/values, FavoriteShare, MagicLink and ActivityLog. Filament CRUD Resources/admin-core are still a separate unmerged backend batch; current Vue customer-facing catalog/customer data remains browser-local until API wiring.
- Current Favorites sharing is still a versioned product-code URL implemented entirely in the frontend until the persisted share flow is built.
- Current color system already has five-color palettes and semantic CSS variables, but the mapping is fixed in `stores/design.ts`; Backend MVP should persist a safe semantic role mapping instead of exposing arbitrary CSS.
- Canonical backend plan: `docs/BACKEND-MVP.md`; bootstrap analysis/result: `docs/BACKEND-BOOTSTRAP.md`.

## 22. Frozen Test 26 / open Test 27+ CI handoff

- Test 26 is registered as immutable in `docs/IMMUTABLE-TESTS.txt`.
- Main must never build or deploy `/t/26`; the next CI UI release lane is Test 27.
- Vite local builds default to `.build/frontend`; numbered output requires an explicit unfrozen `ARMAGHAN_UI_TARGET`.
- Vite-config-only maintenance does not auto-publish a numbered UI test.
- Test 22 and Test 26 CI contracts are historical/frozen release contracts rather than assertions about the current evolving frontend source.
- The mutable launcher remains on Test 26 until an actual Test 27 customer-facing change is intentionally added.
- Backend MVP and UI changes can proceed in parallel; customer UI corrections go to Test 27+.
- Communication terminology rules 75–77 apply across chat replies, reports, handoffs, automation summaries and review notes.
- Repair validation: GitHub Actions **FTP Deploy Run #244 PASS**; QA and FTP smoke passed, while both deploy jobs were skipped. Test 26 stayed untouched and no Test 27 release was published.


## 23. Hosting preflight result

- Hosting Preflight Run #1 executed a short-lived PHP probe and removed every temporary file/directory successfully.
- PHP 8.3.33 on LiteSpeed: PASS; all Laravel 13 required PHP extensions: PASS; HTTPS: PASS.
- FTP account root is the parent of `public_html`; PHP can read/write a verified private sibling outside the public web root even with `open_basedir` enabled.
- PDO drivers expose `mysql` but not `sqlite`. Native SQLite3 is loaded, but Laravel production SQLite is not viable without PDO SQLite.
- Architecture decision: SQLite remains local/dev/test; MySQL/MariaDB becomes the P0 production database.
- Production DB server version/credentials still need provisioning/verification before the first production migration.
- Web PHP disables `proc_open`, `exec`, and `shell_exec`; deployment must build Composer dependencies in CI rather than on the host.
- PHP `symlink()` is available. Limits are 256M upload, 256M POST, 512M memory, 300s execution.
- The preflight touched no numbered UI snapshot; Test 26 remains frozen and Test 27 remains unpublished.


## 24. Laravel / Filament bootstrap handoff

- Backend Bootstrap Run #1: PASS.
- Laravel Framework 13.34.0 and Filament 5.9.0 were resolved by Composer and locked.
- Bootstrap tests: 2 passed / 2 assertions; Composer audit found no known vulnerability advisories.
- Backend root is `platform/backend`; Test 26 and Test 27 were not touched.
- The one-shot bootstrap workflow was removed after use; permanent backend validation lives in `.github/workflows/backend-ci.yml`.
- Generated Laravel agent guidance was overridden by Armaghan-specific `platform/backend/AGENTS.md` and `CLAUDE.md`; no automatic Laravel Boost installation is allowed.
- Next safe batch is domain migrations/models plus production-safe Filament admin access foundation. Production MySQL/MariaDB provisioning can remain deferred until the first production migration and does not block repository development.


## 25. Backend domain foundation handoff

- Backend domain foundation validated in Pull Request #1; Backend CI Run #3 PASS.
- Test suite after this batch: 4 passed / 23 assertions; locked Composer audit clean.
- Users now have explicit `role` and `active` fields. Filament production access requires `role=admin` and `active=true`.
- No default admin/test credential is seeded by `DatabaseSeeder`.
- Core persistent models now exist for Customer, Category, Subcategory, Product, specification definitions/values, FavoriteShare, MagicLink and ActivityLog.
- Favorites share and magic-link token columns store hashes, not raw public tokens.
- Product media is intentionally deferred to Spatie Media Library per project rules; do not add a competing manual product-images table.
- Orders/order timeline remain deferred unless MVP delivery requires them.
- No production MySQL/MariaDB migration has been executed yet.
- Next P0 batch: Filament CRUD Resources for catalog/customers plus secret-driven first-admin provisioning that requires no manual user SQL/cPanel work.


## 26. Test 27 visual editor foundation handoff

- Test 26 remains immutable; rollback checkpoint before Test 27 UI work: `rollback/test26-pre-test27-visual-editor`.
- Test 27 visual editor architecture is documented in `docs/TEST27-VISUAL-EDITOR.md`.
- Approved palette is exactly: green `#21946A`, blue `#151EDA`, mint `#C8E3DB`, white `#FFFFFF`, gold `#FFB514`.
- Header, Footer and Hero brand-chrome surfaces share the semantic `--role-brand-chrome` token instead of unrelated hard-coded navies.
- Admin-only visual editor uses a non-modal, resizable mobile bottom sheet. The page remains visible/selectable above it.
- Dense touch selection samples the rendered touch neighborhood through `elementsFromPoint()`; multiple candidates are presented explicitly rather than guessed.
- Saved style profile is independent of editor visibility: editor OFF removes the editor UI/listeners but keeps the saved style applied.
- Text overrides are locale-specific and only allowed on explicitly registered text targets; arbitrary CSS/HTML/JS input is not accepted.
- Test 27 browser state is isolated under `armaghan:test27:*`; it no longer writes Test 26 mutable-storage keys.
- Editor internals are modular: shell, target chooser, inspector, selection composable, resizable-sheet composable, contrast utility, persistence store and runtime applier.
- Explicit token text/background pairs enforce a 4.5:1 contrast floor; unsafe choices are disabled.
- Editable target coverage now includes Header, Hero, About, Why, Capabilities, product banners, product cards, Footer and main Home surfaces.
- Latest branch-only validation: source contract PASS, TypeScript PASS, 25/25 Vue unit tests PASS, Vite Test 27 build PASS; no deployment was performed by that temporary validation workflow.
- Test 27 must not be promoted in the mutable launcher until owner/customer review.

- Test 27 visual-editor modularization/contrast batch landed on main at `d9a6a68353345a34b0379feca77663a240e1c856`; **FTP Deploy Run #254 PASS**. Full QA/build/smoke passed; `deploy-t` published staging `/public_html/t/27`; `deploy-root` skipped; no remote files deleted. Mutable launcher content still points to Test 26 and contains no Test 27 entry.


## 27. Versioned Style Profile backend handoff

- Canonical design/validation record: `docs/STYLE-PROFILE-BACKEND.md`.
- Laravel now has persistent Style Profile storage split into mutable draft, immutable version history and channel publication pointers.
- Current channels are `staging` and `production`; publishing Test 27 styling to staging does not imply production publication.
- Public read endpoint: `GET /api/style-profile/{channel}`.
- Admin endpoints live under `/api/admin/style-profile/*` and require the persisted active-admin identity.
- Server accepts only structured style/text payloads; arbitrary CSS/HTML/JavaScript is rejected. CSS is generated server-side from the approved token ids only.
- Draft writes support `expected_checksum` optimistic concurrency. A stale editor receives HTTP 409 rather than overwriting newer work.
- Restore does not mutate historical rows; it copies the historical payload into the draft and creates a new immutable published version.
- Publish/restore events are written to ActivityLog.
- A pre-existing ActivityLog mapping bug was fixed: the model now explicitly uses the existing singular `activity_log` table.
- Backend CI Run #10: **PASS** — 12 tests / 84 assertions; migrations, API/security/versioning/restore/conflict tests, Composer audit and secret hygiene all passed.
- No production MySQL migration has been run and no Test 26/frontend file was changed in this backend batch.
- Next safe batch: connect Test 27's existing local visual-editor store through a small adapter to this API, preserving local fallback during staged rollout.

## 28. SQLite-first persistence handoff

- Canonical persistence plan: `docs/SQLITE-FIRST-PERSISTENCE.md`.
- The owner reports production SQLite support was enabled on 2026-10-01; SQLite is now selected as the primary Laravel database.
- The earlier custom JSON runtime-store layer was removed before any production data migrated to it. JSON remains ordinary import/export/fixture interchange only.
- Production `.env` should use `DB_CONNECTION=sqlite` and an absolute `DB_DATABASE` path outside `public_html`.
- Consistent SQLite snapshots are created with `php artisan armaghan:backup-sqlite`, which uses SQLite `VACUUM INTO`.
- Before the first production migration, re-probe `pdo_sqlite` and confirm the private database and backup directories are writable by web PHP. The old 2026-09-30 result is historical and predates host enablement.
- MySQL/MariaDB remains available for a later logical mirror/export. It is not the only backup and must not be live dual-write.
- Test 27 was explicitly approved for launcher visibility on 2026-10-01; add it above Test 26 in `t/index.htm`. Test 26 remains immutable.

- SQLite-primary pivot delivery: main implementation head `4cd840e9261fb2b3ee2baf661b67f6958856cc80`; Backend CI Run #17 PASS.
- Test 27 launcher promotion: FTP Deploy Run #258 PASS; only `/public_html/t/index.htm` uploaded, no remote deletes, root deploy skipped. Test 27 now appears above Test 26 in the mutable test index; Test 26 itself remains immutable.


## 29. Urgent Test 27 visual-editor access handoff

- Test 27 remains the active mutable review lane; Test 26 is unchanged/frozen.
- After an administrator signs in on Test 27, a persistent floating `ویرایش ظاهر` button is visible from any page while the editor is off.
- Pressing that button enables the visual editor and routes to Home, where the non-modal bottom sheet opens.
- The editor bottom sheet now includes a grouped target browser for Header, Hero, About, Why Armaghan, Capabilities, product banners, product-card surfaces and Footer.
- Each registered target can be selected without precise touch targeting and can be toggled visible/hidden directly from the browser. The existing inspector continues to handle allowed text editing plus approved-token text/background/border colors and per-target reset.
- Direct page-touch selection and the ambiguity chooser remain available in parallel.
- Editor controls remain admin-only; no URL/query-parameter bypass or automatic admin mode was added.
- Branch-only urgent validation passed: Test 27 contract, TypeScript type-check, Vue unit tests and numbered Test 27 Vite build.
- Next UI batch is the Laravel Style Profile frontend adapter (server baseline, debounced draft save, 409 conflict handling, staging publish/history/restore) while retaining local fallback.
- Backend work remains queued independently: production SQLite re-probe/activation, Filament CRUD, share flow, customer Magic Link and delivery hardening.

- Urgent editor deployment: **FTP Deploy Run #260 attempt 2 PASS**. Test 27 contract, TypeScript, Vue tests, numbered build, FTP smoke and deploy-t passed. `/public_html/t/27` and `/public_html/t/index.htm` were updated; 112 files uploaded; no remote files deleted; root deployment skipped.
- Run #260 attempt 1 was infrastructure-only feedback: Pillow download from files.pythonhosted.org timed out before frontend build/deploy. Re-running failed jobs succeeded without source changes.


## 30. Test 27 Style Profile frontend adapter handoff

- Test 27 now includes a local-first adapter to the existing Laravel Style Profile API.
- Public staging publication loading is conservative: an existing local edit is never silently replaced. A browser profile may be refreshed from server only when it is empty or still equals the last adopted public baseline.
- A real active-admin Laravel session enables 900ms debounced draft autosave with `expected_checksum`.
- HTTP 409 stops autosave and exposes explicit conflict actions: load server version or explicitly replace server draft with this device version.
- 401/403/API absence falls back to local browser persistence; no local Test 27 admin credential is treated as a Laravel backend login.
- The editor has staging Publish plus up to ten recent immutable versions with Restore.
- API client uses same-origin credentials and forwards `X-XSRF-TOKEN` when Laravel has issued an XSRF cookie.
- Branch validation passed: Test 27 contract, TypeScript, 30/30 Vue unit tests and numbered Test 27 build.
- Historical adapter-stage note: this dependency has since been resolved at the backend/service level. Production Laravel/SQLite and a real admin now exist; only browser session/CSRF/cross-device acceptance remains.

- Style Profile adapter staged deployment: **FTP Deploy Run #262 PASS**. Test 27 QA/build/smoke passed; `/public_html/t/27` updated; 112 files uploaded; no remote files deleted; root deployment skipped.
- Historical adapter-stage note: the Laravel backend/admin deployment is now complete and production Style Profile service semantics are acceptance-tested PASS. Browser-authenticated UI acceptance remains open.


## 31. Production SQLite / Laravel activation handoff

- Fresh production SQLite probe on 2026-10-01: **PASS**.
- Verified web runtime: PHP 8.3.33, PDO SQLite, SQLite3, SQLite 3.53.4, private file read/write, foreign keys, `VACUUM INTO`, HTTPS, private sibling access, ZipArchive and PharData.
- Laravel Framework 13.34.0 is now deployed on the host with application/shared state outside `public_html`.
- Shared host-only `.env` and APP_KEY were generated/preserved on the host and were never committed or logged.
- Production SQLite database was created in private shared storage; migrations completed successfully.
- Initial consistent SQLite snapshot completed successfully.
- Core schema existence checks passed: users, products, customers, style_profiles, style_profile_versions, style_profile_publications.
- Public backend surface is `https://armaghantrading.com/backend`.
- HTTP smoke after permission repair:
  - `/backend/` → 200
  - `/backend/up` → 200
  - `/backend/api/style-profile/staging` → 200
  - `/backend/admin/login` → 200
- The first deployment workflow reported failure only because post-deploy HTTP smoke ran before public permissions were corrected; activation, migrations and backup had already succeeded.
- Root cause of public 404: public Laravel directories were created too restrictively for LiteSpeed. Public permissions were repaired to directories `0755` / files `0644`; private app/data permissions were not widened.
- Temporary activation, diagnostic and permission-repair files were cleaned up.
- First production admin is now provisioned securely as `admin@armaghan.local`; no seeded/default password exists and no plaintext password is stored in Git or repo docs. Next acceptance task is browser login followed by Test 27 shared Style Profile save/reload/publish/restore verification.
- Test 26 and Test 27 UI files were not modified in this backend activation batch.

- Historical note: an early Production SQLite Reprobe attempt was INCONCLUSIVE because the temporary probe URL could not be reached. A later authoritative canonical-URL probe passed PDO SQLite, private file R/W, foreign keys and `VACUUM INTO`; production SQLite activation is complete.


## 32. Test 27 color/saveability handoff

- Production Laravel/SQLite activation is already complete and healthy at `/backend`; SQLite 3.53.4, PDO SQLite, private read/write, foreign keys, `VACUUM INTO`, migrations and an initial private snapshot have passed.
- The first real production administrator is now provisioned securely. No default/seeded password exists; the plaintext credential is intentionally not stored in the repository.
- Actual repository logo pixel probe result: image-dominant background `#0714C2`; this is now the canonical Brand Blue and replaces the earlier `#151EDA` token.
- Test 27 light-mode Home background default is Brand White through `--role-page-background`; the root `home.page` target exposes background color only and cannot be hidden.
- `home.content` is a separate hideable/selectable target so the editor cannot accidentally remove its own recoverable page root.
- Primary Home panel default is Brand Mint through `--role-panel-background`; About copy, Why list and Capability cards consume it.
- Why reason titles/texts and Capability titles/texts now have stable explicit editor ids and locale-aware text overrides.
- Structured target browser exposes recoverable hidden orphan targets and marks protected non-hideable roots.
- Inspector respects per-target allowed controls; protected roots do not show irrelevant text/border/Hide actions.
- Style Profile API defaults to same-origin `/backend` and bootstraps Laravel CSRF tokens for mutating requests, retrying once on HTTP 419.
- Browser-local persistence remains automatic. Sanitized JSON Export/Import is available for simple manual backup/transfer and does not create a JSON database.
- Latest branch validation runs for `ui/test27-editor-color-saveability-20261001` are PASS. Test 26 has not been modified.
- Remaining acceptance test: authenticate the already-provisioned real admin at `/backend/admin/login`, edit Test 27, observe server sync, reload/cross-device, Publish staging and Restore.


### Test 27 color/saveability live delivery

- Main implementation before final docs: `7c688673b52d9ffe6108f53e80544e95d474da4b`.
- **Backend CI Run #19: PASS**.
- **FTP Deploy Run #268: PASS**.
- Full historical Test 11–27 QA, TypeScript/unit/build pipeline and FTP smoke passed.
- Active Test 27 was rebuilt and uploaded to `/public_html/t/27`; 112 files uploaded; no remote files deleted; root deployment skipped.
- Live Test 27 now uses logo-background blue `#0714C2` as canonical Brand Blue.
- Light Home page default is white; primary Home panels default to Brand Mint; both remain structured token-controlled editor surfaces.
- `home.page` is protected from Hide, while `home.content` remains independently controllable.
- Nested Why/Capability text targets, hidden-orphan recovery, per-target Inspector controls, sanitized JSON export/import and same-origin `/backend` Style Profile API/CSRF support are live in Test 27.
- Shared cross-device saving now requires only the verified authenticated browser cycle: log into `/backend/admin/login`, edit Test 27, confirm server sync, reload/cross-device, Publish staging and Restore.


## 33. First production administrator provisioning handoff

- Production active-admin account exists: `admin@armaghan.local` / `Armaghan Administrator`, user id 1.
- The account was created by the Armaghan one-shot bootstrap at 2026-10-01T13:35:31Z.
- The first bootstrap run created the intended account but failed during credential encryption because a temporary public-key payload was malformed.
- Recovery was not blind: a read-only inspector first verified there was exactly one active admin and that email, display name and creation timestamp matched that failed bootstrap.
- Recovery then rotated only that exact account using a strong random on-host password.
- Credential encryption is now performed before any future account mutation; this prevents an unrecoverable admin if encryption fails.
- Laravel password hashing verification passed after recovery; active-admin policy passed; `/backend/admin/login` HTTP smoke passed.
- Plaintext password was never committed and was not emitted to GitHub logs. Only RSA ciphertext was logged and decrypted outside the repository workflow.
- Persistent tooling added:
  - `armaghan:provision-first-admin` fail-closed Artisan command for future clean deployments;
  - `platform/scripts/provision_first_admin_ftp.py` with inspect/provision/guarded-recovery support.
- The one-shot workflow used for this production bootstrap was deleted after success.
- Next task: authenticate the browser against the real Filament login and verify Test 27 Style Profile autosave, reload, staging Publish and Restore end-to-end.

- Final first-admin provisioning validation: **Backend CI #30 PASS**; **FTP Deploy #270 PASS**. No Test 26/27 UI files were changed by this security batch.


## 34. Production Style Profile persistence acceptance

- Live production SQLite Style Profile semantics are now independently verified, not merely covered by CI/local tests.
- Acceptance helper: `platform/scripts/verify_style_profile_ftp.py`.
- The helper uploads a short-lived random-token PHP probe, boots the deployed Laravel app, and exercises the existing `StyleProfileService` against the live database.
- It does **not** log an administrator into the browser, expose a password, create an auth bypass, or persist test changes.
- Production acceptance Run `36878931948`: PASS.
- Verified inside one outer transaction:
  - draft save;
  - staging Publish;
  - second changed Publish;
  - Restore from the first version as a new immutable version;
  - staging publication pointer update;
  - ActivityLog writes.
- Inside the transaction the test created 3 versions and 3 activity records as expected.
- The outer transaction was rolled back and post-test logical state exactly matched pre-test state.
- Temporary public helper cleanup PASS.
- This closes uncertainty around production SQLite/Style Profile service behavior.
- Remaining acceptance is browser-only: establish a real Filament session at `/backend/admin/login`, edit Test 27, verify autosave status, reload/cross-device, Publish staging and Restore through the actual UI/CSRF path.
- Do not weaken authentication to automate that final browser step.

- Final merge head before this handoff note: `0fe333ad3ea7e2cdf21c9245ac99fbe22f9c3507`.
- Main post-merge validation: **FTP Deploy #272 PASS**; QA/smoke passed, `deploy-root` skipped and `deploy-t` skipped. No UI release was touched by this acceptance batch.


## 35. Consolidated current status reconciliation — 2026-10-01 18:45 +03:30

Canonical current-state file: `docs/CURRENT-STATUS.md`.

Verified current state:
- main at reconciliation start: `79ee20ad14414298be2ce6f129e6c48922017200`;
- Test 26 frozen;
- Test 27 live/active mutable review lane;
- canonical Brand Blue `#0714C2`, Home light background white, primary Home panels Brand Mint;
- Laravel 13.34.0 live under `/backend`;
- production SQLite active and verified;
- first real active production administrator exists;
- Style Profile production draft/publish/restore semantics acceptance-tested PASS with rollback;
- final editor persistence acceptance is browser-only: real Filament session + CSRF + reload/cross-device + staging Publish/Restore.

Still open in backend/domain delivery:
- no Filament Product/Category/Subcategory/Customer Resource classes exist yet;
- no public catalog/customer backend controllers exist yet;
- FavoriteShare and MagicLink have domain models but not complete HTTP/session flows;
- product-media ownership/upload flow remains open;
- rotated off-host SQLite backup + restore drill remains open;
- safe repeatable backend update workflow with pre-migration snapshot/rollback guard remains open.

Estimated remaining core work: about 6 runs, or 6–7 if Filament CRUD/media is split into two bounded runs.

Next P0:
1. browser-authenticated Test 27 Style Profile acceptance;
2. Filament CRUD;
3. catalog/customer API/media wiring.


## 36. Test 27 customer visual corrections — implementation handoff

Customer-requested Test 27 corrections are implemented on the active mutable UI lane, with Test 26 untouched.

- Rollback checkpoint: `rollback/test27-pre-customer-ui-corrections` → `40dc89b5aa97bd09fb664b88b1fb8dc9d54c15d9`.
- Footer brand area is logo-only; brand name/description beside the logo are no longer rendered or exposed as phantom editor text targets.
- Customer-facing product label is derived centrally: available products show the localized subcategory (one of six); unavailable/made-to-order products show a unified localized unavailable/producible label.
- Domain `Product.name` values remain preserved for admin/backend compatibility.
- Product codes use Brand Blue capsule + Brand White text + restrained Brand Gold edge.
- New Appearance Feature Flag: `showSubcategoryCodes`; default OFF on all viewport profiles. Appearance schema is v5; schema-v4 migration preserves existing navigation/section choices.
- Products light background defaults to Brand White; cards default to Brand Mint; dark-mode defaults remain dark. `products.page` is a structured editor background target and `product.card` remains editor-controlled.
- Strong content headings use an accessibility-aware dark-green role derived primarily from Brand Green plus the neutral text role on light surfaces; dark/image/brand-chrome headings retain safer contrast behavior.
- Why Armaghan separators are 3px Brand Blue; numbered circles are Brand Blue with Brand Gold numerals.
- Dedicated presentation unit tests and Test 27 source-contract assertions were added.
- Delivery sequence: implementation `972d66bcd6e0e3ab72946d2fd14ffe8a6d329052`; historical Test 23 contract repair `fb7ee2a1c4200f8080c68e37e10edb238cda9833`; validated release marker `65b9f1158019aece79438fbc680af4070e0c0665`.
- FTP Deploy #275 intentionally stopped before deployment when the historical Test 23 contract detected its stale current-source assumption. FTP Deploy #276 then passed the repaired historical contract. **FTP Deploy #277 PASS** completed the full Test 27 QA/build/smoke/deploy lane.
- Run #277 uploaded 112 files to `/public_html/t/27` plus the mutable launcher, deleted no remote files and skipped root deployment. Test 26 remained immutable.
- Final heading-green polish: `13b3dbb5abd32265f752139b9cf670127a8ce5c9`; **FTP Deploy #282 PASS**. The strong heading role now stays visibly dark green on White/Mint while retaining safe contrast; Test 26/root remained untouched.


## 37. Filament taxonomy CRUD — first bounded batch

- Commit: `4281034656ead0c0538b4f1dc4e607ea5f161438`.
- Backend CI #31: **PASS**.
- FTP Deploy #284: **PASS**, but no Backend production mutation occurred; the generic frontend FTP workflow does not deploy Laravel application code.
- Added native Filament 5 Resources for Category and Subcategory with dedicated Schema/Table/Page classes.
- Admin UX supports Create, Edit, List, Search, Sort, Active filter and parent-category relationship selection.
- Delete and bulk-delete actions are intentionally absent because the database Category → Subcategory foreign key uses cascade-on-delete; destructive taxonomy removal needs an explicit guarded workflow later.
- Feature tests verify active-admin access to all taxonomy pages, deny inactive/non-admin users, and assert destructive actions are absent.
- Production activation remains open until the repeatable Backend release/update workflow with pre-migration backup and rollback guard is implemented.
- Browser-authenticated Test 27 acceptance also remains open; Browser Context Profile `Armaghan Production Admin` was created, but no signed-in session had been saved at the time of this handoff.


## 38. Core Filament CRUD + guarded Backend code-update lane — production live

### CRUD delivery
- Category/Subcategory implementation: `4281034656ead0c0538b4f1dc4e607ea5f161438`; Backend CI #31 PASS.
- Product/Customer implementation: `de450a15c339cb8a3460c7c3f7f45d01a7074158`; Backend CI #33 PASS.
- All four Resources use native Filament 5 Resource/Schema/Table/Page structure with server-side search/sort/filter and active-admin access control.
- Destructive delete actions are intentionally absent. Customer CRUD does not expose `user_id`; account/session ownership remains separate for the later Magic Link flow.
- Product names remain internal admin data. Customer-facing Test 27 continues to derive its visible label from the six-way subcategory or unified unavailable/producible state.

### Production update lane
- Updater: `platform/scripts/deploy_laravel_code_update.py`.
- Workflow: `.github/workflows/backend-code-deploy.yml`.
- The lane is deliberately code-only and fail-closed: if production `composer.lock` or migration fingerprints differ from the candidate release, it refuses activation.
- Before a swap it creates a consistent SQLite `VACUUM INTO` snapshot, stages private and public code, performs directory swaps, runs HTTP smoke checks, and retains the previous code until smoke succeeds.
- Backend Code Deploy #1 activated the candidate but observed HTTP 404 on the public Laravel surface because the staged public root inherited 0770. The updater automatically rolled back; rollback and temp cleanup passed.
- Fix `f6c997ae9496a0cc48ff8be6a1e190278d02e115` normalizes the staged public root to 0755 and additionally verifies rollback health.
- Backend Code Deploy #2: **PASS** — health/login/categories/subcategories HTTP 200; snapshot created; no Composer/migration drift; temp cleanup PASS.
- Updater validation was added to permanent Backend CI in `420f77ba3b18c74b44c8ea9d02730174623fe478`; Backend CI #32 PASS.
- Smoke coverage was expanded in `82346c3dd4365f3d0f7f1909cdb14a59c1b80eed`.
- Backend Code Deploy #3: **PASS** — `/backend/up`, admin login, categories, subcategories, products and customers all returned HTTP 200; snapshot created; no migration/dependency drift; temp cleanup PASS.
- The code-only lane does not pretend to support schema/dependency-changing releases. Add a separate migration-aware guarded path only immediately before such a release is needed.

### Remaining browser acceptance
- Real browser Test 27 shared-persistence acceptance remains open because the dedicated browser profile does not yet report a saved signed-in session for armaghantrading.com.
- Do not request or store the administrator password in chat and do not weaken authentication.


## 39. Public catalog API + production bootstrap — live

### Public catalog API
- Main implementation: `f318225e476de4503f75dd5ea0a83c4265936eea`.
- Public endpoints: `GET /backend/api/catalog/categories` and `GET /backend/api/catalog/products`.
- Category/Product JSON is shaped through dedicated API Resources rather than exposing raw Eloquent models.
- Products support bounded search/filter/pagination; relationships are eager-loaded.
- API metadata includes every Backend-managed code, including inactive rows. Test 27 uses this to distinguish “not migrated yet” from “explicitly inactive in Backend”.
- Backend CI #35 PASS.
- Test 27 Hybrid Sync is live from FTP Deploy #295 PASS: Backend data overlays matching local rows; inactive managed rows suppress stale local rows; unmanaged staged rows remain; local media/specs are fallback; API failure does not blank the storefront.
- Production catalog smoke was added in `cfd94a0fe0b0271f4d9162e7402b9decb22af7d3`; Backend CI #36 and Backend Code Deploy #4 PASS with both catalog endpoints HTTP 200.

### Guarded production bootstrap
- Canonical MVP fixture: `platform/backend/database/fixtures/catalog-bootstrap.json`.
- Command: `armaghan:bootstrap-catalog`; dry-run by default, `--apply` required.
- The command refuses any non-empty Category/Subcategory/Product state and imports only inside one database transaction.
- Fixture/count contract: 3 categories, 6 subcategories, 18 products.
- Backend CI #37 PASS; Backend Code Deploy #5 PASS.
- One-shot production run: **Catalog Bootstrap Production #1 PASS**.
- Verified sequence: pre-counts 0/0/0 → consistent SQLite backup → import → post-counts 3/6/18 → representative codes verified.
- Temporary public helper cleanup PASS.
- Independent live API read confirmed 3 managed categories, 6 managed subcategories and 18 managed products.
- Independent JavaScript-rendered Test 27 Products page confirmed 18 products render with intended customer presentation.
- One-shot workflow was removed immediately after success in `0143e666bdfb1536172d7b0477a3d3220f213046`; reusable bootstrap tooling remains fail-closed on the now-nonempty production catalog.
- Permanent Backend CI now syntax-checks the bootstrap helper.

### Browser acceptance status
- The saved browser profile was checked read-only and redirected to `/backend/admin/login`; it was not an authenticated Filament session.
- Browser Style Profile acceptance remains open but non-blocking. Do not weaken authentication or make delivery depend on this saved browser profile.

### Next P0
1. production product-media ownership/upload;
2. minimum customer API/session surface;
3. favorites/share + Magic Link;
4. off-host SQLite backup/restore drill;
5. final QA/handoff.


## 40. Production Product Media + guarded additive deployment — live

### Architecture and ownership
- Product media is now owned by Spatie Media Library on `App\Models\Product`, collection `product-gallery`, administered through the official Filament Spatie Media Library plugin.
- Product media uses the public disk and the model-specific path generator `media/products/<media-id>/`; conversions remain inside each unique media directory.
- Filament allows up to 6 ordered JPEG/PNG/WebP images per Product, max 8 MiB and 5000×5000 each. Drag order determines primary image.
- Every accepted original is decoded and re-encoded through GD before it becomes canonical, stripping embedded metadata and normalizing common JPEG EXIF orientation.
- `card` and `thumb` conversions are synchronous, aspect-ratio preserving, non-cropping and capped at the original width to avoid upscaling.

### Public catalog + Test 27
- Public Product API eager-loads Media and returns an ordered `media` array with card + thumb URLs.
- Test 27 server media is authoritative when a Product has at least one server media item; otherwise its existing local media/spec fallback remains active.
- Independent live read after deployment confirmed the production API now exposes `media: []` on current Products and still reports all 18 managed Products.
- No real Product image has been uploaded to production yet because the saved browser profile is not authenticated. This is an acceptance item, not an implementation blocker.

### Additive deployment lane
- Infrastructure commit before Media: `905e5a073962dcfa37f334b13318739e36f0a2cf`; validator follow-up `3ec1c50eac8c0e65ba0a562e37e556d96c589c5e`.
- Code-only deploy now plans first and skips when Composer or migration drift exists.
- Additive updater requires all existing migrations to be byte-identical and currently allows only new create-only migrations. It snapshots SQLite before migration, restores the snapshot if migration execution itself fails, preserves previous code/public roots until smoke succeeds, and maintains the public storage symlink.
- Production image capability is fail-closed on missing GD or EXIF.

### Delivery record
- Draft PR #6 used only for pre-merge validation; Backend CI #41 PASS.
- Product Media squash merge: `4d8f660a06df7b4e4a340502d6d65255b41cc949`.
- Backend CI #42 PASS.
- Backend Code Deploy #7: plan PASS; deploy correctly skipped on Composer/migration drift.
- Backend Additive Deploy #3: **PASS** — `backup_created=true`, `dependency_changed=true`, `added_migrations=1`, `schema_policy=additive-create-only`, `public_storage_link=true`; health/admin/products/catalog HTTP smokes all 200; temp cleanup PASS.
- FTP Deploy #307 failed before deployment because `test_web_stock_policy.py` correctly rejected an external-domain URL used only by the new frontend unit-test fixture. Test 27/root were not changed.
- Fixture repair: `b3722d3f4643ee403972b7a03215339e5aa802ad` uses a same-origin `/backend/storage/...` test URL.
- FTP Deploy #308: **PASS** — frozen-test guard and Test11–27 contracts PASS, web-stock policy PASS, TypeScript/Vue unit tests/Test27 production build PASS, FTP smoke PASS, `deploy-t` PASS, `deploy-root` skipped.

### Next bounded delivery
- Conservative estimate: 4 runs remain; aggressive estimate: 3 if customer API/session + Magic Link + FavoriteShare can be combined without exceeding the safety/time budget.
- Next P0 is customer API/session + Magic Link core. Favorites/WhatsApp persistence follows unless it safely fits the same bounded vertical slice.
- Off-host SQLite backup/restore drill and final end-to-end QA/handoff remain after customer flows.
- Browser Style Profile acceptance and one real Filament product-image upload remain opportunistic parallel acceptance tasks when a valid real admin session is available.


## 41. Customer session + one-time Magic Link — production live

### Security/architecture
- Customer identity uses the normal Laravel same-origin session with a separate `armaghan.customer_id` session key; it does not replace or log out a simultaneously authenticated Filament administrator.
- Customer session login/logout rotates the session identifier; logout clears only the customer identity and regenerates CSRF state rather than invalidating unrelated admin auth.
- Magic Link tokens are 64 high-entropy alphanumeric characters. SQLite stores only SHA-256 hashes; raw tokens exist only at issue/click time.
- New issuance revokes every prior unused active customer-portal Magic Link for that Customer.
- Consumption is atomic and requires enabled + unused + unrevoked + unexpired + active Customer + direct-link enabled.
- Issue, consume, revoke, profile update and logout are audited without storing raw tokens.
- Public consume/admin issue routes are rate-limited. Customer session responses expose only id/company_name/WhatsApp/country code/name; internal notes and `user_id` are neither returned nor customer-writable.

### Production routing repair
- Initial core merged in PR #7: `e311d7327e249a9d57d7ed44d4a6f0c72de35b40`; Backend CI #47 PASS; Backend Code Deploy #8 PASS.
- The first implementation emitted `/backend/auth/customer/<token>`. A later live smoke in Backend Code Deploy #9 proved this token-in-path form returns HTTP 404 on the deployed `/backend` LiteSpeed layout. The guarded updater automatically rolled back; rollback health and temp cleanup PASS.
- The repaired design keeps the bearer token only in the Test27 URL fragment: `/t/27/#/magic/<token>`. Fragments are browser-side and are not sent in the initial HTTP request URL.
- Test27 consumes the token once through fixed same-origin `POST /backend/api/customer/magic-link/consume` JSON body using the existing CSRF bootstrap/retry adapter, then replaces browser history with `/tracking`.
- Repair PR #8 squash merge: `10d040c7d1706d68a5018e1240c797011440000c`.
- Backend CI #49 pre-merge + #50 post-merge PASS.
- Backend Code Deploy #10: **PASS** — snapshot created, no Composer/migration drift; health/admin CRUD/catalog smokes 200; unauthenticated `/api/customer/session` 401; GET against the POST-only magic consume endpoint 405; cleanup PASS.
- Independent production probe confirmed the same 401/405 behavior outside CI.
- FTP Deploy #314: **PASS** — frozen Test26 + Test11–27 contracts including customer auth/session contract PASS; TypeScript/Vue unit tests/Test27 production build PASS; FTP smoke and `deploy-t` PASS; `deploy-root` skipped.

### Test27 behavior
- On startup Test27 checks Backend customer session first; a real session is Backend-authoritative.
- Legacy demo/local customer auth remains only as a Test27 review fallback when no server session exists; it is not Backend authentication.
- The customer dashboard no longer falls back silently to seed Customer #1 when no customer id exists.
- Customer dashboard saves the bounded server-supported fields when using a real Backend session.
- LoginSheet no longer creates browser-only Magic Links; it explains that secure links are issued by Armaghan administration.

### Next P0
- Persisted FavoriteShare + WhatsApp handoff is next.
- Then off-host SQLite backup/restore drill.
- Then final end-to-end QA/handoff.
- Browser Style Profile acceptance, one real product media upload, and one real Magic Link issue/click remain opportunistic acceptance tasks when an authenticated real admin browser session is available.


## 42. Persisted FavoriteShare + WhatsApp handoff — production live

### Architecture/security
- FavoriteShare uses the existing `favorite_shares` + `favorite_share_product` schema; no migration or dependency change was required.
- New shares generate a 64-character high-entropy token; SQLite stores only its SHA-256 hash. The raw bearer token appears only in the browser fragment `/t/27/#/favorites/share/<token>` and is resolved through fixed same-origin POST `/backend/api/favorite-shares/resolve`.
- Issue/resolve are CSRF-protected and rate-limited. Share resolution returns only share id/expiry + ordered active product codes; owner/customer identity is never exposed publicly.
- Only active Products under active Subcategory/Category may be shared; issue fails rather than silently changing the requested product set.
- Guest shares expire after 7 days and have no authenticated revoke surface. A real Backend customer session creates a customer-owned 30-day share; only that customer can revoke it through `/api/customer/favorite-shares/{id}`.
- Issue/revoke are audited without raw tokens.

### Test27 + WhatsApp
- Test27 now creates persisted server-backed shares. Current product-code-in-URL generation was removed.
- Existing historical `?shared=v1:` links are still decoded read-only so already-sent Test27 links do not break, but no current UI generates them.
- Recipient route `/favorites/share/:token` resolves the token via Backend then maps the returned ordered codes to the live catalog.
- Share creation reuses one issued link while the favorites set is unchanged; changing favorites invalidates the cached issued link and causes a new server share on the next action.
- Native share/copy remains available. The WhatsApp CTA sends the persisted share URL through the existing seller WhatsApp path with URL-encoded text.
- Customer wishlist lead attribution now uses the real `session.currentCustomerId`; the previous hard-coded Customer #1 fallback is removed.

### Delivery record
- PR #9 head `bc5c1ef2170eaa85910ce13976a8ebb1d731ffa5`; Backend CI #51 PASS.
- Squash merge: `76f80179292f743cb54443e540602bba47c4d8bb`.
- Backend CI #52 PASS.
- Backend Code Deploy #11: **PASS** — pre-swap SQLite snapshot created, no Composer/migration drift; health/admin/catalog/customer auth smokes green and FavoriteShare resolve POST-only route verified by GET 405; temp cleanup PASS.
- Independent production probe returned HTTP 405 for GET `/backend/api/favorite-shares` and `/backend/api/favorite-shares/resolve`, confirming both fixed POST routes are live without creating share data.
- FTP Deploy #316: **PASS** — frozen Test26 and Test11–27 contracts including persisted FavoriteShare contract PASS; TypeScript/Vue unit tests/Test27 build PASS; FTP smoke + `deploy-t` PASS; root skipped.
- Read-only live browser acceptance with an intentionally invalid 64-character token loaded the persisted shared-list route and rendered the invalid/unavailable state without crash or any legacy local-code share.

### Next P0
- Production hardening: rotated off-host SQLite backup + restore drill + retention/verification.
- Then final end-to-end QA + handoff.
- Real authenticated admin acceptance for Style Profile, product media upload and a real customer Magic Link remains opportunistic/non-blocking.


## 43. Test 28 + encrypted off-host SQLite backup hardening — live

### Test 28 promotion
- Test 27 is now frozen and included in `docs/IMMUTABLE-TESTS.txt`.
- Test 28 is the active mutable UI lane and uses isolated `armaghan:test28:*` local/session-storage namespaces.
- Vite refuses numbered UI targets <=27; FTP active target is 28 and likewise refuses frozen targets <=27.
- The launcher lists Test28 before Test27. Test27 remains live and independently verified after Test28 deployment.
- Test28 carries forward the validated Test27 UI/Backend integration; `document.documentElement.dataset.uiTest='28'` is the release marker.

### Off-host SQLite backup architecture
- Workflow: `.github/workflows/sqlite-offhost-backup.yml`; helper: `platform/scripts/sqlite_offhost_backup.py`.
- Production creates a consistent live SQLite snapshot using `VACUUM INTO`, then verifies integrity/foreign keys/table inventory before encryption.
- Plaintext snapshot is encrypted on the production host using AES-256-GCM. The public GitHub Actions artifact contains only `backup.sqlite.enc` plus a non-secret manifest.
- Key derivation uses PBKDF2-HMAC-SHA256 with 600,000 iterations and per-backup random salt.
- Preferred secret is `ARMAGHAN_BACKUP_PASSPHRASE`. It is not currently configured, so Backup #1 used the explicit `ftp_password_kdf_fallback`; the raw FTP secret was never logged or persisted.
- Scheduled cadence is daily at 01:23 UTC. Artifact retention is 14 days.

### First real backup + restore evidence
- SQLite Off-host Backup #1 run: `36974268704` — PASS.
- Encrypted artifact: `armaghan-sqlite-backup-36974268704`, ID `11212796186`, 337,663 bytes; expires 2026-10-16T06:35:35Z.
- Production snapshot plaintext size: 335,872 bytes; encryption occurred before transfer; temporary production export cleanup PASS.
- Production checks: SQLite integrity PASS; foreign-key check PASS; 23 tables observed.
- Critical recorded counts at backup time: 6 migrations, 1 user, 3 categories, 6 subcategories, 18 products, and 0 customers/favorite_shares/magic_links/style_profiles/media.
- Restore-drill job downloaded the uploaded artifact and verified the artifact digest, AES-GCM decryption, plaintext checksum, `PRAGMA integrity_check`, `PRAGMA foreign_key_check`, complete table inventory and critical table counts.
- Restore drill opened and rolled back a write transaction successfully. Restored plaintext was held only in an isolated temporary runner directory and was not persisted.

### Test28 delivery record
- PR #10 pre-merge Backend CI #53 and #54 PASS; squash merge `6593f6124bfccfd25ae5899d2690608940214905`; post-merge Backend CI #55 PASS.
- FTP #318 stopped before build/deploy because the historical Test26 contract still expected active Test27. Test26/27/root were untouched.
- Fix `39bd08346420a74bd217ee908c6782c7403da7f1` future-proofed only that historical contract and added a Test28 release marker.
- FTP #319 reached unit tests but stopped before deployment because two session test fixtures still used Test27 browser keys. Runtime source was already correct.
- Fix `3158ffbbcd1ad7fe95cf3c096d0cf576b1b8cfde` aligned those fixtures to Test28.
- FTP #320: PASS. Frozen Test26/Test27, Test28 visual/auth/share, backup safety, Python syntax, TypeScript, 41 Vue unit tests, production build and FTP smoke all PASS.
- `deploy-t` uploaded 112 files into `/public_html/t/28` plus the mutable launcher; no remote file deletion; `deploy-root` skipped.
- Independent live check confirmed `/t/28` loads, `/t/27` still loads, and the launcher is Test28-first.

### Operational follow-up
- Add a dedicated `ARMAGHAN_BACKUP_PASSPHRASE` as P1. Until then, retained fallback-encrypted artifacts require the original FTP secret-derived key; do not discard/rotate that recovery material before their expiry without an explicit recovery plan.
- Core hardening is complete. The next and final core run is end-to-end QA + delivery handoff.
- Real authenticated-admin Style Profile, product-image upload and real customer Magic Link browser acceptance remain opportunistic/non-blocking if a valid Filament session becomes available.

### Documentation/contract closeout after Test28
- Documentation handoff commit: `dd38e120b69df4cb46bdede67e83b8dcc025ddbb`.
- FTP #321 correctly stopped on one remaining historical Test26 assertion that expected the literal rules phrase `Tests 01–26 are frozen`; no UI/root deployment occurred.
- Contract-only fix: `72a45224e0bd6fd6547f30f2fa9983e6f1424a0b` changed the historical assertion to require a frozen range that still includes Test26.
- FTP #322: PASS; QA complete, frontend build skipped, `/t` deploy skipped, root deploy skipped.
- Repository memory now points to final QA/handoff as the sole remaining core run.


## 44. Bounded status reconciliation — 2026-10-02

Read-only evidence: main `9ee3a20697d109194ca8ff25e172f072f3688a38`; latest FTP workflow `36975429480` completed/success. Shared coordination lease was released before this run acquired its own SHA-guarded lease.

Corrected the CURRENT-STATUS next-run list: Test27 is frozen, Test28 is active, and encrypted off-host backup/restore hardening is complete. Updated this document's opening phase pointer. No frontend, backend, frozen snapshot, launcher or production data changed.

### Selected bounded method

| Rank | Method | Score | Benefit / limitation |
|---:|---|---:|---|
| 1 | Reconcile stale operational instructions and preserve explicit QA evidence boundaries | 9.5 | Prevents accidental Test27 mutation and repeated completed work; does not close live QA |
| 2 | Live responsive/language/theme acceptance | 9.0 | Closes customer-facing risk; needs browser evidence |
| 3 | Authenticated editor acceptance | 8.0 | Verifies shared draft/reload/publish/restore; needs a valid real admin session |
| 4 | Additional CRUD work | 5.0 | Current core CRUD is already implemented |
| 5 | New visual redesign | 3.0 | Adds scope and regression risk before delivery |

Selected automatically: option 1. Reference for small changes checked by existing workflows: https://docs.github.com/en/actions/get-started/continuous-integration.

### Exact next acceptance scope

| Area | Required evidence | Status after this run |
|---|---|---|
| Responsive layout | Mobile/tablet/desktop: Home, products, details; mobile bottom nav, top nav transition, hero and footer | OPEN; no live browser check in this run |
| Languages and themes | Persian/Arabic/Sorani RTL, English LTR; light/dark readable controls | OPEN |
| Guest boundaries | Guest customer-session 401; POST-only token routes; invalid-token UI; no admin editor exposure to guest | Historical route/UI evidence exists; fresh acceptance OPEN |
| Catalog/media | Backend inactive records suppress local copies; empty server gallery uses fallback | Implemented; final browser acceptance OPEN |
| Real authenticated flows | Editor save/reload/publish/restore, one product image, one customer Magic Link | Opportunistic; requires real session; never bypass auth |
| Release/recovery | Frozen 01–27, Test28-first launcher, root skipped, encrypted backup round-trip | Prior documented PASS; record evidence in final handoff |

Keep the final QA backlog unchecked until each result has direct evidence. This run does not replace multi-device acceptance with documentation or route/unit tests.

## 45. Bounded live QA — 2026-10-02

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

## 46. Test29 nonvisual release

## Test29 — nonvisual release, 2026-10-02

Owner instruction: defer visual testing; preserve all previous versions; create a new numbered folder and prepend its link.
- Tests 01–28 are frozen. Test28 source checkpoint: `snapshot/test28-final` at `1fafd9638777e26796acedc60b27dfd545736794`.
- Test29 is the active release lane. Build/deploy target: `/t/29`; link `./29/index.html` is first in the launcher.
- Build and FTP boundaries refuse targets <=28. Root deployment and remote deletion remain prohibited for this run.
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


## Owner-authorized root Test29 release — 2026-10-02
Owner explicitly authorizes root index replacement with selected Test29 while preserving /t. Independent selector: config/root-release.json. Tests 01–28 remain frozen; Test29 is mutable. Requested eyebrow/producible defaults and real Filament navigation are included. Root deployment verification pending. Google requires owner Cloud credentials; no real login acceptance is claimed.

Google customer signup/sign-in implemented using official Socialite with a create-only identity table and eleven backend security cases. Root hides local demo password/signup and ignores browser-only admin roles; real customer sessions remain authoritative. Backend additive deploy and real Google credential readiness pending; never claim live Google login before actual provider verification.


## 47. Independent root release and Google customer identity

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


## 48. Google frontend return repair

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

### Verified deployment closeout
- Repair source commit: 8676b469d0159848f775689c55b7286e35cc932f; corrected test request commit: ad02b2e50ad5e6dd70196533a700c10a947a26b9.
- Backend CI 37029811115 PASS: 51 tests / 362 assertions, dependency audit and secret hygiene PASS.
- Backend Code Deploy 37029811169 PASS: pre-swap SQLite backup created, no dependency/migration drift, health/admin/catalog smokes 200, guest session 401, POST-only endpoints 405, temporary cleanup PASS.
- FTP QA 37029811251 PASS; root and numbered UI deploys skipped. No frontend/frozen version files changed.
- Independent live cancellation probe now returns HTTP 302 to https://armaghantrading.com/#/tracking?auth_error=google (before repair: /backend/#/tracking).
- First attempt was safely stopped before host deployment by two test-harness 404s: forced production URL root also prefixed the test request. Using explicit localhost test request URLs keeps the /backend URL generator simulation intact; all tests now PASS.
- Real Google signup/repeat login still requires owner confirmation; no actual-account login success is inferred from cancellation/automated tests.

- Independent numbered-path probe: start at /backend/auth/google/redirect?return_path=/t/29/, cancel with the same cookie jar, return HTTP 302 to https://armaghantrading.com/t/29/#/tracking?auth_error=google. Provider enabled:true and guest session 401 remain verified after deploy.

## 49. Real Google account profile and visual repair

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


## 50. Real password login and authorized owner admins

## Owner administrator access and real password accounts — 2026-10-02

- Owner explicitly authorized administrator access for motealle@gmail.com and armaghantrading.company@gmail.com, real email/password sign-in, public customer signup, removal of the direct-link login tab, transparent gold producible labels, and a footer logo 130% larger at bottom right.
- Five ranked methods: verified Google owner provisioning + real password flows (10), separate admin password panel (8), Google-only (7), public magic-link flow (5), browser demo authentication (1). Selected the first; no browser-local role or typed email alone grants administrator rights.
- The server owner-email allowlist is configuration-backed. Only stateful Socialite with a valid subject and verified Google email can create/elevate those administrators. Disabled accounts remain disabled; promoting an existing customer disables its customer access and replaces its old password. Existing administrator passwords survive repeat Google login.
- Owner Google login establishes real Filament web authentication and opens /backend/account/security with a 10-minute, own-user, one-use password setup proof. The user chooses the password privately in the browser; no password is seeded, displayed, transmitted in chat or saved in Git. Existing password changes require current password or fresh Google proof. Profile management is enabled in Filament.
- Same-origin CSRF-protected POST login/register routes hash passwords, enforce at least 12 characters with letters/numbers and confirmation, rate-limit login per account and IP, and issue real customer sessions. Public signup always creates customer role, cannot claim owner emails, cannot set verified/admin/internal fields, and rejects duplicate emails. No email verification or password-reset mail delivery is claimed.
- LoginSheet uses only real server login/signup on root and Test29. Direct-link tab removed; existing safe manager-issued Magic Link consume remains functional for compatibility. Clear administrator password-panel shortcut remains visible. Real customer account includes a password-settings link.
- Producible badge keeps its gold text, centered small weight 400 typography, with transparent background and no navy border/shadow. Footer brand moved after columns in DOM and aligned physical bottom right; 3rem to 6.9rem is +130% (2.3×), with contained image and no adjacent text. Editor target IDs remain stable.
- Tests 01–28 frozen; Test29 remains active and selected at root. Local type-check/build + 51 frontend tests PASS; Backend CI #61 (37046579401): 63 tests / 473 assertions PASS. Code deploy #17 (37046579406): deployed and health/admin/catalog smoke PASS. Initial FTP #345 (37046579358) stopped before UI deployment on an obsolete active-source assertion requiring browser-local signup and a public Magic Link tab; owner-requested semantics were corrected in Test19/Test24 source contracts, without changing frozen numbered folders. FTP #346 (37046866862) QA/type-check/51 frontend tests PASS; Test29 and root publication/checksums PASS; live root reload loads index-DhRFzVDI.js and confirms both real login/signup forms, no public direct-link tab, and the real admin login destination. Actual owner Google sign-in/password setup and live authenticated logout remain OPEN until exercised; no production credentials or customer records inspected.

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
