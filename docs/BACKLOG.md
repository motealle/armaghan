## Mobile image-format upload correction — 2026-10-08 (publication pending)

Owner screenshot: a selected ~0.9 MiB PNG is rejected with the generic format/size hint. The original file bytes were not attached, so its exact rejection cause is unproven. Reproduced four actual preparation defects before changes: noncanonical mobile PNG MIME rejected, small image bytes/extension mismatch passed unchanged, bitmap-decoder failure sent oversized originals unchanged, and disguised non-image content was never checked locally.

Frontend preparation now reads JPEG/PNG/WebP signatures, canonicalizes MIME/extension from bytes, requires decode, falls back to the native image decoder when bitmap decoding is unavailable/fails, resizes to max1920px without crop/upscale, and never selects an oversized original instead of the resized result. Temporary decode URLs are revoked; fallback decode/export are bounded to15s. Actual invalid-content and preparation failures have separate localized messages. Shared Home upload preparation receives the same fix. Server content/decode validation,8MiB/5000px/six-image limits, authorization/revision/CSRF safeguards remain authoritative and unchanged. No dependency or backend schema/code change.

Focused preparation/submission checks, type-check/build and guarded Test29/root publication are required. Physical-phone acceptance and the exact screenshot file remain unverified; intermittent hosting failures and prior queued-release cancellation remain separately open.

## Photo loading/retry controls published — 2026-10-07

Source9d6aeb614a390119f046f99774f41f7d2670c5f8. FTP37653930573 PASS: QA112904170348 (56tests/17files, type-check/build/safety gates), Test29deployment112904523305 and guarded root112905496919. Local8focused media tests PASS; a temporary DOM-render diagnostic additionally exercised actual SmartImage spinner→two automatic retries→manual arrow→fresh request→successful load/removal of controls PASS. No new dependency, backend mutation or frozen-version changes in this follow-up.

Independent HTTP200 root bundle index-D8m-etk8.js, product UI chunk ProductGrid-OSWyjao6.js and ring CSS ProductGrid-PBbJPMA8.css all match local SHA256. Three total automatic attempts, then user-initiated new cycle.25s per active request; two photos at once/browser; no fake retry for zero-photo cards. Current remaining P0s are intermittent hosting timeouts/503, real-phone/fresh authenticated upload acceptance and prior queued FTP37642388074 cancellation (GitHub sign-in approval remains unanswered). Application delivery/recovery changes are published; all-device success and elimination of hosting failures are not claimed.

## Owner follow-up: photo loading ring and manual retry — 2026-10-07 (publication pending)

Product cards show a small spinning ring only for actual photos while receiving/waiting/retrying, retaining the chosen no-photo loading image. Three total automatic attempts (initial +2 after1.5/4s); each active network attempt bounded25s. On exhaustion show an accessible48px Lucide circular-arrow button; user activation requests a fresh URL and renews the bounded cycle. No spinner/retry on products with no uploaded/source photo; translated labels in all4languages, keyboard focus and reduced-motion support.

Two simultaneous public photo requests per browser cap bursts; completion/error/timeout/navigation releases capacity, offscreen30% preload preserved. Final original-task checks encountered three timeouts and one503 during a small photo batch; next bounded health/photo reads returned200. This transient host failure is observed, not attributed conclusively to a phone or claimed eliminated. No host-account/cache/security configuration change. Focused regression/type-check/build and guarded Test29/root publication pending.

## P0 product-photo repair published — 2026-10-07

Sourcec533b8cd42230b00b8ef69a9799ffeb66e9645ea published. Backend CI37652138568/job112897902687 PASS95tests/943assertions; Code Deploy37652138589/job112897960485 PASS38tests/474assertions, guarded activation/smoke/temp cleanup. FTP37652138646 PASS53tests/16files, type-check/build, Test29job112898450733 and root112899477618. Independent root bundle index-6nlcvcCI.js matches local SHA256 and contains photo-recovery cache revision; all frozen lanes/original uploads preserved.

Post-activation public samples77/223/185/3: HTTP200 WebP thumbnails8–12KB, no Set-Cookie, independent media1200/min; categories200 without cookies/catalog120/min. Remaining host latency7–9s in this environment is real and not claimed solved. A temporary focused diagnostic merged all202 actual public catalog products/recent media223 successfully. Cloud UI cannot access /backend/api/catalog/products (ERR_BLOCKED_BY_CLIENT); real-phone and authenticated fresh-upload acceptance therefore remain open. Old FTP37642388074 remains queued; cancellation was previously blocked at GitHub sign-in by automatic approval review, explicit sign-in approval unanswered. Do not bypass that boundary; older UI publication could still supersede the current UI, while this backend fix is independent.

## P0 product-photo delivery repair — 2026-10-07 (publication pending)

Owner reports uploaded photos failing on several phones. Live guest checks: catalog 202 products, sampled legacy/recent media77/78/54/55/46/47/41/186/223/185/3/80/162 all HTTP200 WebP thumbnails7–25KB, but requests6–12s; public photo responses emit XSRF cookies and share numeric throttle bucket with catalog/auth. Device failure cannot be inferred from these successful samples alone.

Bounded repair: independent public-media1200/min/IP and catalog120/min/IP buckets; public GET catalog/Home/photo routes skip session/cookie/anti-forgery middleware while retaining route binding and all authenticated/mutating protections. Startup registration resolves Product canonical paths before model boot. Public derivative repair avoids media-row writes/editor revision changes; reduced fallback variants cache only60s. Frontend retries public product photos twice after1.5/4s with fresh URLs, cancels on navigation/unmount, keeps no-photo loading fallback and30% viewport preload; fresh media revision and up to3 concurrent catalog pages reduce serial hydration delay. Originals/private data/schema untouched. Focused regression/build and guarded Backend/Test29/root deployment pending; no claim of real-phone acceptance yet.

## انجام شد — ویرایش فقط همان محصول و دریافت تازه پس از رفرش — ۲۰۲۶-۱۰-۰۷

## انتشار ترتیب، جست‌وجو و مرکز راهنما — پایان کار کد، مانع تثبیت صف — ۲۰۲۶-۱۰-۰۷

- موجودها پیش از قابل‌تولیدها؛ جست‌وجوی کد فارسی/عربی/لاتین و نام‌های ثبت‌شده؛ راهنمای فارسی۸۶پرسش/۲۱موضوعِ بازشونده و جست‌وجوپذیر، با بارگذاری فقط هنگام بازشدن، انجام شده‌اند.
- کد نهایی9754d74 و انتشار37642866586 موفق: بررسی112865860499 با۵۱آزمون محدود انتشار/۱۵فایل، بررسی نوع و ساخت، حفظ۲۸نسخه منجمد؛ تست۲۹112866394264 و ریشه112867948523 موفق. شش آزمون محلیِ همین تغییر موفق؛ پیش‌آزمون سه شکست مرتبط را بازتولید کرد.
- ریشهassets/index-CDuF5PFl.js پاسخ۲۰۰داد و هش کاملش با ساخت نهایی یکسان بود. بدون تغییر سرور، داده تجاری، دسترسی، وابستگی یا نسخه‌های منجمد. پذیرش دیداری/گوشی یا مدیر ادعا نشده.
- مانع باز: دو انتشار قدیمی37642388074 (95dd1b8) و37642424881 (7c0037d) هنوز خاتمه نیافته‌اند؛ اجرای راهنما قدیمی پس از نسخه نهایی وضعیت درحال‌اجرا و سپس صف نشان داد. ممکن است محتوای تست۲۹/ریشه را به منبع قبلی برگردانند؛ تثبیت نهایی نیازمند لغو آن دو و بازخوانی ریشه است. ابزار متصل فرمان لغو ندارد.
- آغاز ورود به گیت‌هاب برای کنترل صف توسط بازبینی خودکار رد شد: مجوز کلی مخزن را مجوز مشخص ورود و لغو انتشار ندانست. ورود انجام نشد، اعتباری دریافت نشد، رد شدن دور زده نشد. تأیید مشخص مالک برای ورود امن/لغو فقط این دو انتشار قدیمی لازم است؛ انتشار موفق نهایی نباید لغو شود.


## مرکز راهنمای فایل مشتری — ۲۰۲۶-۱۰-۰۷

- ۸۶ پرسش در ۲۱ موضوع، بر پایه فایل «مرکز راهنما(2).docx»، با پاسخ بازشونده، جست‌وجوی سؤال/پاسخ/موضوع و انتخاب موضوع؛ ساختار بومی مرورگر و صفحه‌کلید در پنجره راهنما رعایت شده.
- راهنما فقط هنگام بازشدن بارگذاری می‌شود تا حجم خواندن نخست محصولات افزایش نیابد. متن کامل فعلاً فارسی است؛ راهنمای کوتاه و کنترل‌های چهار زبان حفظ شده و زبان متن کامل صریحاً مشخص است.
- پاسخ‌هایی که از دکمه تأیید مشتری یا سفارش مجدد ناموجود صحبت می‌کردند، با مسیر واقعی واتساپ و ثبت مدیر هماهنگ شدند؛ فایل‌های سفارش فقط در صورت انتشار واقعی برای مشتری معرفی می‌شوند. متن منبع اصلی تغییر نکرده و فایل خصوصی/پایگاه داده وارد مخزن نشده.
- شش آزمون محدودِ ترتیب/جست‌وجو/پوشش راهنما، بررسی نوع و ساخت موفق؛ کنترل‌های حفاظت انتشار محلی موفق. کد ترتیب/جست‌وجو95dd1b8 و اجرای انتشار37642388074 فعلاً در صف گیت‌هاب؛ راهنما7c0037d در مخزن ثبت شد؛ اجرای37642424881 نیز در صف و بدون هیچ مرحله شروع‌شده است. توقف صف را با موفقیت انتشار یکی ندانید؛ بعد از پایان، تأیید فایل‌های ریشه و پذیرش محدود جست‌وجو/بازشدن پاسخ لازم است. پذیرش مرورگر تولید هنوز ادعا نشده.


## ترتیب محصولات و اصلاح جست‌وجو — ۲۰۲۶-۱۰-۰۷

- موجودها پیش از قابل‌تولیدها، با حفظ ترتیب داخلی هر گروه؛ فیلترهای دسته/زیردسته/وضعیت محفوظ و آرایه اصلی بدون تغییر است.
- جست‌وجو ارقام فارسی/عربی/لاتین، پیشوند «کد»، ی/ک عربی، نیم‌فاصله و نام‌های ثبت‌شده چهار زبان را می‌پذیرد. نام فعلی محصول و عنوان زیردسته هم جست‌وجو می‌شوند.
- پیش‌آزمون سه شکستِ مرتبط را بازتولید کرد؛ چهار آزمون محدود همان فایل پس از اصلاح موفق. بررسی نوع و ساخت برای انتشار لازم است. بدون تغییر سرور، داده واقعی، وابستگی یا نسخه‌های منجمد. انتشار محافظت‌شده تست۲۹/ریشه در حال انجام؛ پذیرش مرورگر ادعا نشده.


- علت با دو آزمون قبل از اصلاح بازتولید شد: تطبیق بر اساس کد متغیر، کارت قبلی را کنار کارت جدید نگه می‌داشت؛ رکورد سروری غایب نیز به‌اشتباه نمونه محلی محسوب می‌شد. جست‌وجوی عمومی صفحه اول می‌توانست محصول اصلی را پیدا نکند و حالت ساخت را فعال کند.
- دریافت و ویرایش با شناسه ثابت سرور؛ ویرایش عادی کارت بدون شناسه سرور رد می‌شود؛ در بارگذاری یا خطای دریافت، ویرایش نمونه‌های محلی غیرفعال است. افزودن فقط از دکمه صریح افزودن محصول انجام می‌شود؛ ایجاد ضمنی از ویرایش عمومی حذف شد. غیبت یا خطای محصول ثبت‌شده هرگز به ساخت تبدیل نمی‌شود. تصویر، نام‌های زبان‌ها، ویژگی‌ها و شناسه محلی مطلوب‌ها حفظ می‌شوند. ذخیره دوم و کلیک هم‌زمان همان محصول را تغییر می‌دهند؛ پاسخ نامطمئن مانع ساخت مجدد می‌شود.
- پاسخ ذخیره فوراً همان کارت را جایگزین می‌کند. تطبیق بر اساس شناسه، نسخه نمایشی تکراری را جمع می‌کند؛ فهرست موفق سرور تنها مرجع محصولات است، پس دستگاه دوم نمونه محلی قدیمی را بازنمی‌سازد. خطای شبکه فهرست موجود را حذف نمی‌کند. هیچ محصول واقعی یا حسابی حذف یا برای آزمون ساخته نشد.
- بازخوانی پس از ذخیره از نشانی تازه برای دسته‌ها و تمام صفحات محصولات استفاده می‌کند تا پاسخ عمومی stale-while-revalidate اطلاعات ذخیره‌شده را عقب نبرد؛ پاسخ عمومی سرور دارای cache/SWR است؛ دریافت آغازین نیز تازه می‌شود تا رفرش از پاسخ عمومی قدیمی استفاده نکند. مرورگر این محیط۸۵کارت نشان داد، در حالی که دریافت مستقلِ تازه۹۱ و سپس۹۸رکورد داشت؛ این اختلاف به‌تنهایی تشخیص قطعی کش نیست، زیرا بازکردن مستقیم مسیر داده در همان مرورگر با ERR_BLOCKED_BY_CLIENT مسدود شد. کنترل۳۰ثانیه‌ای خواندن تکراری و تجمیع درخواست هم‌زمان حفظ می‌شود. دریافت تازه شروع صفحهc995a08/FTP37572750924 و محدودکردن ویرایش عمومی به رکورد موجودd5e3df5/FTP37573270337 در تست۲۹ و ریشه موفق شدند. آزمون قطعی رد ویرایش کارت بدون شناسه اضافه شد. دو نشانی تازه عمومی در تولید۲۰۰ گرفتند.
- سرورabe2309: CI37571114785/job112629731386،۹۳آزمون/۹۳۲تأیید موفق؛ Code Deploy37571114767/job112629770393،۳۲آزمون/۳۹۱تأیید و فعال‌سازی/پاک‌سازی موفق. آزمون SQL تغییر کد و ذخیره دوباره تعداد۳۱رکورد آزمایشی و همان شناسه را ثابت نگه داشت و ویژگی حفظ شد. بدون تغییر ساختار پایگاه داده.
- رابط4ea3e6d/FTP37571385827، مرجع سروریbbd92f2/FTP37571730722 و خواندن تازه99b6f81/FTP37572223386 در تست۲۹ و ریشه موفق؛ بررسی112633199686 با۴۵آزمون ضروری در مرحله خواندن تازه، نوع/ساخت، حفظ۲۸نسخه قدیمی و کنترل مرز احراز هویت موفق؛ ریشه112633794740 موفق. تغییر assertion قدیمی قرارداد مدیریت صرفاً v-if را با زنجیره واقعی v-else-if تطبیق داد؛ آزمون‌های موجود حذف نشدند.
- فایل مرحله خواندن تازه ریشهassets/index-DKFeh0g-.js با هش کامل ساخت نهایی تطبیق داده شد. مرورگر عمومی فایل نسخه تازه را بازخوانی کرد و۸۵کد یکتا نشان داد؛ این صرفاً مشاهده نمایشی است و تأیید دریافت تازه داده نیست. کاتالوگ زنده هم‌زمان تغییر می‌کرد، بنابراین شمارشِ زمان‌های متفاوت معیار تشخیص محصول تکراری نیست؛ رکوردهای واقعی احتمالی قبلی خودکار پاک نشدند.
- انتشار نهاییd5e3df5: بررسی112636471198 با۴۶آزمون ضروری/۱۴فایل و بررسی نوع/ساخت موفق؛ ریشه112637127235، کنترل احراز هویت و تأیید فایل‌ها موفق. فایل نهاییassets/index-CNzzzg2g.js در ریشه با هش کامل ساخت نهایی تطبیق داده شد؛ فهرست کارها به‌روز شد. بدون تغییر وابستگی، ساختار پایگاه داده یا نسخه‌های منجمد.
- تنها پذیرش بازِ همین اصلاح: ویرایش عملی با نشست معتبر مدیر در مرورگر تولید؛ نشست این محیط مهمان است؛ مسیر داده در مرورگر با ERR_BLOCKED_BY_CLIENT مسدود شد، پس پذیرش مدیریتی مرورگر تأیید نشده. این خطا محدودیت همین مرورگر است و با خطای سایت یا مقابله با ربات یکی نیست؛ دریافت مستقل عمومی۲۰۰بود. ورود دور زده نشده. مشتری پس از پایان کار جاری، صفحه را برای دریافت نسخه جدید تازه کند. سایر کارهای قبلی در فهرست یکتای زیر باقی‌اند.

## فوری — اصلاح رنگ بیضی‌ها و دکمه‌های مدیریت — ۲۰۲۶-۱۰-۰۶

- رنگ ثابت بسیار تیره #101A44 در بیضی کد و وضعیت حذف شد؛ هر دو از رنگ مشترک برند #0714C2 استفاده می‌کنند. عبارت فارسی قابل‌تولید نیم‌فاصله دارد. متن، ارتفاع و حاشیه داخلی وضعیت در هر حالت روشن/تیره نسبت به اندازه مشاهده‌شده قبلی۷٪ کاهش یافته است. انتشار77207cf/FTP37488716618 در تست۲۹ و ریشه موفق.
- مرحله دومِ خواسته مالک: دکمه‌های ردیف مشتری/کاربر فقط آیکون، نام دسترس‌پذیر و راهنمای کوتاه دارند؛ گروه مرتب با سطح لمس۴۴پیکسل. نام حذف در چهار زبان کوتاه شد؛ تأیید، هشدار، دریافت بکاپ و بازگردانی سروری حفظ شدند. هیچ حساب واقعی حذف نشد.
- ایراد پین: فراخوانی بازخوانی قبلی با قفل داخلی saving متوقف می‌شد. پس از ذخیره، همان صفحه از سرور بازخوانی می‌شود تا وضعیت و ترتیب فوراً به‌روز شود. یک آزمون مستقلِ همین رفتار موفق؛ بدون داده واقعی یا تغییر دسترسی.
- بررسی نوع/ساخت موفق؛ سه آزمون ضروری بکاپ، دریافت محصولات اشتراکی و نزدیک‌دید موفق. پذیرش تازه حساب مدیر هنوز نیازمند نشست معتبر است. عکس سروری خانه۲۰۰؛ نمونه مسیر واقعی محصول۴۹ در مرورگر بارگذاری شده. زمان شبکه محیط اجرا برای عکس خانه حدود۱۰ثانیه بود؛ هنوز علت میزبان/شبکه تفکیک نشده است.

پایان انتشار: مرحله دوم4a845bc و FTP37489641902، بررسی112358734784 و ریشه112359927750 موفق؛۳۱آزمون ضروری، بررسی نوع و ساخت موفق. مرورگر ریشه نسخه compact-admin-actions را دریافت کرد؛ رنگ هر دو بیضیrgb(7,20,194)، فونت وضعیت11.6064px در مقابل12.48px پیشین، ارتفاع25.4375px در مقابل27.328125px با گردکردن پیکسل. عکس نتیجه armaghan-brand-pills-1791301563547.jpg ذخیره شد. پذیرش مدیریتی مرورگر هنوز باز است؛ نشست مدیر موجود نبود.

بررسی واقعی جدید اشتراک: انتخاب دو محصول ثبت‌شده11110/11112 در نشست مستقل مهمان لینک۵۴کاراکتری ساخت؛ مرورگر گیرنده پس از یک دریافت مجدد دقیقاً همان دو کد را نشان داد. بار اول پیام خطای عمومی داشت، پس دریافت اولیه بی‌خطا هنوز تأیید نشده. انتخاب کدهای محلی/قدیمی11001/11003 پاسخ۴۲۲گرفت؛ هماهنگ‌کردن فهرست نمایشی با محصولات فعال سرور برای اشتراک در صف فوری است. هیچ محصول حذف‌شده‌ای بازسازی یا مجوزی کاهش داده نشد؛ نشانی/توکن اشتراک در اسناد ثبت نشده.

فهرست یکتای باقیمانده (موارد انجام‌شده در تاریخچه دوباره صف نشوند):
1. جداسازی تأخیر میزبان/شبکه؛ رفع خطای اولین ساخت نسخه سبک و بازیابی عکس پس از شکست موقت.
2. پذیرش عملی آپلود، انتشار و بازخوانی عکس تازه خانه و محصول با مدیر واقعی.
3. پذیرش عملی ثبت/ویرایش مشتری، پین، فعال/غیرفعال و ذخیره پس از ورود دوباره.
4. پذیرش عملی بکاپ، حذف، آندو و بارگذاری بکاپ با رکورد آزمایشی مجاز؛ نه حساب واقعی مشتری.
5. رفع دریافت اولیه نامطمئن لینک کوتاه؛ هماهنگی فهرست نمایشی با محصولات فعال سرور در ران ویرایش کارت انجام شد. مسیر گیرنده دو کد ثبت‌شده را پس از دریافت مجدد نشان داد.
6. بررسی گوشی/تبلت واقعی: سرعت اسکرول، کشیدن گالری، زوم با دو انگشت، دو زبان/دو حالت رنگ و ذخیره ویرایشگر.
7. ثبت تعاریف ویژگی‌های شش زیردسته در سرور برای ویرایش مدیریتی، فقط در زیردسته‌های فاقد تعریف؛ نمایش پیش‌فرض‌ها قبلاً بازگردانده شده.
8. تکمیل تنظیمات و پذیرش واقعی ورود گوگل، ارسال ایمیل و ورود پیامکی؛ وابسته به سرویس و اعتبار معتبر.
9. بازنشانی امن حساب دیگر، قطع نشست‌هایش و اتصال حساب موجود به مشتری با اثبات هویت؛ تغییر رمز شخصی موجود است.
10. مخاطبان واتساپ نام‌دار، شماره‌های محافظت‌شده و تنظیمات مشترک هر دستگاه؛ قابلیت‌های تکمیلیِ پیشین.
11. پشتیبان رمزگذاری‌شده فایل‌های خصوصی و تمرین بازیابی فایل‌ها؛ پشتیبان روزانه پایگاه داده موجود است.
12. بارگذاری خصوصی صورتحساب/رسید توسط مشتری و تکمیل ثبت و تطبیق پرداخت؛ پیش‌فاکتور و مراحل دستی موجودند.
13. اختیاری و خارج از بچ فوری: فهرست اشتراک دائمی در حساب و اعلان صوتی پایان کار؛ ابزار صوتی عملیاتی در این محیط تأیید نشده است.

بچ پیشنهادی ران بعد: «قابلیت اعتماد کاتالوگ و عکس‌ها»؛ هماهنگی فهرست نمایشی با محصولات فعال واقعی انجام شد؛ سپس دریافت اولیه لینک و اندازه‌گیری درخواست‌های عکس، تشخیص جداگانه اولین بار/بار بعد، بازیابی یک‌باره و محدود عکس ناموفق، پذیرش مدیر اگر نشست معتبر فراهم باشد، یک بررسی نهایی گوشی. تغییر مسیر ذخیره یا حذف فایل اصلی بدون شواهد ممنوع. سایر پذیرش‌های مدیر در ادامه همین فهرست و وابسته به نشست معتبر هستند.

## Run2 published; focused live acceptance passed — 2026-10-06

Final root promotion5f7995e: FTP37484320906/root112340528104 PASS; final root browser navy code pill verified in dark mode too (same white Vazirmatn label/digits), light/English browser preferences restored. Root Home browser successfully rendered published11/card and13/detail with responsive srcsets. Remaining acceptance: authenticated manager fresh-upload/CRM journey; follow-up: initial cold derivative500/host latency and real-phone timing.

- Backend97433de27249781ad1e1030b96aa42dabf5528a4 activated by Additive Deploy37482096711/deploy112332693848: success, guarded activation, one create-only migration, temporary helper cleanup PASS. Backend CI37482096705/job112332585625:78 focused tests/678 assertions PASS. No production customer rows changed or deleted.
- UIb4120b29146f40076909a6f8105ebcd099c2bc01 adds grouped persisted customer business fields/pin, filter URL persistence and delayed scroll restoration, responsive Home srcsets, unified navy code label/digits pill and bottom-right Home category copy. Follow-upa7b6909 preserves the navy pill in dark mode too. FTP37482923655 QA112335598032 passed: type-check,30 existing/changed release tests and build; frozen01..28 preserved. FTP37482923655 Test29/root activation PASS. Follow-upa7b6909 Test29 PASS (37483358361); root selector revision advanced explicitly to promote that tested dark-mode CSS.
- Live public Home variants verified200/WebP: production11/card800x533/81,682bytes vs original2,681,971bytes (97.0% smaller); baby13/detail1600x800/69,684bytes vs2,031,185bytes (96.6% smaller). Responsive index exposes correct actual-width srcsets. First cold conversion read returned500; one subsequent bounded read returned200 and the derivative is present. Cold-generation/hosting latency remains a follow-up, not a claim of measured real-phone speed.
- Live root browser: Baby/category1 + Babywear/subcategory11 survived reload with exactly819px scroll before/after;20 matching products remained. Code pill reads کد ۱۱۰۰۱, navy rgb(16,26,68), label and digits both white and Vazirmatn FD. Home category banners stay640px tall; each copy is22px from right/bottom. Screenshot armaghan-batch2-1791298919484.jpg saved separately.
- Canonical CRM profile revision/search/pin and backup/Undo coverage passed isolated backend acceptance. Authenticated manager upload/CRM browser acceptance still requires a legitimate session; guest Home/product acceptance is independent. No auth bypass, TinyFish, broad local suite, destructive account operation or original-image replacement.

## Run2 implementation ready for guarded backend activation — 2026-10-06

- One create-only customer_business_profiles table links uniquely to existing Customer. Canonical admin create/update/search/revision expose bounded optional contact/address/commercial fields and pinned; company/country/status remain existing columns. Pin ordering usesCOALESCE to keep ordinary unpinned rows in newest-first order. Contact email/name never mutate login credentials.
- Parent-locked transactional profile writes; readable/encrypted archive snapshots include an existing business profile; legacy snapshots omit the new null key so old backup fingerprints remain compatible. Existing archived rows retain their profiles through Undo/import. Security/roundtrip acceptance added to the existing focused customer/account files.
- Home public API now selects existing/new uploaded thumb/card/detail WebP variants and supplies actual-width responsive srcsets. Existing original URLs remain compatible; originals unchanged. Shared bounded nonblocking/atomic derivative repair is reused, without updating Home editor revisions during delivery housekeeping. Draft/private preview remains authenticated and no-store; published-channel checks precede generation. Home legacy repair test verifies original SHA, no upscale and stable revision.
- Frontend implementation ready but held until backend activation: grouped customer form/pin, category/subcategory/search/status in query, safe route-scoped scroll saved on leave/background and restored after Products readiness with cancellation on user scroll. Private token/admin routes excluded. Owner additions: code label and digits in one navy pill with same inherited face/color; Home category copy bottom-right with readable bottom gradient.
- One local targeted unit pass:5tests/3files PASS; type-check/Vite build PASS; required frozen/editor/customer/root/FTP contracts PASS. Backend PHP unavailable locally; focused CI/additive checks and private pre-migration snapshot are required before activation. No production business/customer data edited or account deleted. Backend/publication/live single-pass acceptance PENDING.

## Additional owner steering in run2 — 2026-10-06

- Include localized کد and its number INSIDE one navy pill, same inherited Persian font and number color; visible owner screenshot shows the label outside the pill. No new separate badge or displaced number.
- Home bottom three category banners: copy goes in the physical bottom-right corner. Keep doubled height/green borders; bottom gradient keeps the white copy readable. Preserve English text direction.

## Run2 execution — customer business data, browsing position and lightweight Home media — 2026-10-06

Owner explicitly starts run2 and requests one final focused verification pass. Current main ad84884 confirmed; no prior run2 implementation exists. Selected ranked method: create-only one-to-one customer business profile; canonical existing Customer controller/form; product filters in hash query; safe route-scoped persisted scroll with DOM-ready restoration and user-interaction cancellation; responsive Home WebP variants using existing canonical Media Library/shared bounded derivative generator. Alternatives rank2 direct alteration of customers (8), rank3 unstructured notes/JSON-only business profile (6), rank4 parallel admin (4), rank5 browser-only customer persistence (2); selected approach10 preserves old columns and migration protections.

Execution checklist:
- [ ] Canonical contact name, store name, contact phone/email/language, country/city/district/shop number, product group, purchase volume, cooperation/sales type; persisted pin sorted first; existing active/inactive and backup-first delete reused.
- [ ] Profile included in customer revision/search and readable/encrypted backup/fingerprint; old archives without profile remain compatible; parent-lock transactional writes.
- [ ] Category/subcategory/search/status restored from URL; refresh scroll stored only for safe non-token routes, bounded restoration after Products ready; user scroll cancels pending jump.
- [ ] Existing/new Home uploads serve aspect-preserving thumb/card/detail WebP; originals untouched; same-channel public selection and draft authorization maintained; no repeated expensive conversion or blocked worker.
- [ ] One focused final pass for changed behavior + type-check/build and necessary guarded publication checks. Backend additive lane snapshots private database before create-only migration; activate backend first, then guarded Test29/root UI release.
- [ ] Verify public legacy Home image byte reduction and one live category/subcategory/scroll reload. Real authenticated manager upload/CRM acceptance only if a valid session is available; never weaken auth to manufacture it.

## Verified batch1 publication and cold Home-file recovery — 2026-10-06

- UI81ecff30e3bb6263e88a70a7eb459efea67af45c published at Test29/root: FTP37477293280 PASS (QA112315982989, Test29upload112316265609; root promotion PASS). Live revision2026-10-06-home-customer-batch1. Browser: category banners now640px vs observed prior320px at the same desktop size; all3 capability borders2px; Why outer radius17.6px,3px transparent gaps, four square internal rows with0px border. No external placehold.co images. Persian product cards show کد ۱۱۰۰۱ and قابل تولید on rgb(16,26,68).
- Backendb1be912bdcd19361c119c7e4309bf94fba08207f activated: Backend CI37478016618/job112318474941 PASS (76focused tests/640assertions in5.12s, including separate-process cold-media regression); Code Deploy37478016953/job112318550913 PASS (16media/auth tests/125assertions, guarded activation/temp cleanup). No migration, private data update or fresh product/customer creation.
- Production Home index already contained published production11 and baby13. Before fix both standalone public image URLs404/21bytes. After startup registration of HomePathGenerator both200, PNG1536x1024/2681971bytes andPNG1774x887/2031185bytes. Fresh Home browser renders these actual Backend URLs with their decoded natural widths1536/1774, rather than local fallback. Fresh authenticated manager upload remains unexercised; existing persisted-public-image delivery is verified separately.
- Batch1 complete: Why unified, category height doubled, matching green capability borders, localized code prefix/fa digits, قابل تولید/navy pill, green white-icon WhatsApp contact, external English fallback removed, existing Home-file404 recovered. Desktop proof armaghan-home-batch1-1791296460943.jpg saved separately. No TinyFish or broad suite.
- Batch2 OPEN and authorized: full categorized canonical customer data + persistent pin (including backup/Undo schema coverage); persist category/subcategory/filters and scroll after refresh when content is ready; authenticated upload/publish/refresh round-trip. Add home lightweight derivatives: current uploaded PNGs remain2.68/2.03MB despite successful delivery; preserve originals, use bounded responsive derivatives and do not claim phone speed improvement without measurement. ProductsView currently keeps subcategory only in memory and router scrollBehavior unconditionally returns top0.

## Home published-file 404 diagnosis and bounded fix — 2026-10-06

Production public Home index returns capability.production/media11 and banner.1/media13, but both standalone public file reads return404/21bytes. Their existing uploaded records must not be replaced/re-uploaded merely to conceal delivery failure. StyleProfile registered its custom media/home path only on model boot; standalone Media file requests do not instantiate that model. AppServiceProvider now registers HomePathGenerator at every application startup, preserving canonical paths, publication checks and private admin access. One separate-process regression exercises a cold path read before any StyleProfile instance. Backend CI/code-only guarded deployment pending; no migration or production data mutation. Batch1 UI deployment still transferring. This bounded delivery fix is included in run1; full authenticated upload acceptance remains run2.

## فوریت‌های مالک — دو ران منسجم، 2026-10-06

روش منتخب: اصلاح اجزای موجود و حفظ منبع رسمی داده. ران اول فقط نمایش و متن؛ ران دوم ذخیره‌سازی و بازیابی موقعیت. اصلاحات رسانه main در 32504df حفظ می‌شوند؛ انتشار قبلی FTP37449247225 موفق است، پذیرش آپلود واقعی مدیر هنوز باز است.

| اولویت | کار | ران | وضعیت / معیار پایان |
|---|---|---|---|
| فوری | چرا ارمغان: یک حباب گرد مینت، شکاف‌های جداکننده با زمینه صفحه | ۱ | منتشر و بررسی زنده موفق؛ چهار ردیف در یک قاب، بدون بوردر رنگی داخلی |
| فوری | ارتفاع سه کارت نوزادی، بچگانه، زنانه دو برابر؛ بدون سرریز عرض | ۱ | منتشر و بررسی زنده موفق؛ ارتفاع واکنش‌گرا دو برابر مقدار فعلی |
| فوری | بوردر سبز ۲ پیکسل دور پنل‌های تصویری بالایی با متن مینت | ۱ | منتشر و بررسی زنده موفق؛ هم‌رنگ کارت‌های دسته |
| فوری | قابل تولید به جای تولیدپذیر؛ کد پیش از شماره با ارقام فارسی | ۱ | منتشر و بررسی زنده موفق؛ سورمه‌ای موجود حفظ و صریح تثبیت شود |
| فوری | تماس مشتری: دکمه واتساپ سبز با آیکون سفید در مدیریت | ۱ | منتشر و بررسی زنده موفق؛ فقط بازکردن مسیر گفتگو، بدون ارسال خودکار |
| فوری | حذف جایگزین IMAGE REQUIRED؛ بازیابی تصویر محلی مصوب | ۱ | منتشر و بررسی زنده موفق؛ بدون سرویس بیرونی جایگزین عکس |
| فوری | آپلود و نمایش خانه: بررسی عملی اصلاحات چت دیگر و رفع ایراد باقیمانده | ۲ | دریافت تصاویر منتشرشده اصلاح و در مرورگر تأیید شد؛ آپلود تازه با مدیر هنوز باز: انتخاب فایل، ذخیره، انتشار در کانال درست، نمایش بعد رفرش |
| فوری | ثبت مشتری و فیلدهای کامل تجاری در پایگاه داده + پین | ۲ | پیاده‌سازی و انتشار انجام شد؛ آزمون سرور موفق؛ پذیرش مرورگری مدیر باز؛ ایجاد/ویرایش/بازخوانی، فعال/غیرفعال، پین پایدار؛ حذف با بکاپ و آندو موجود |
| فوری | بازگشت پس از رفرش به مسیر، زیردسته و میزان اسکرول | ۲ | منتشر و بررسی زنده موفق (زیردسته۱۱، اسکرول۸۱۹)؛ انتظار برای محتوای لیزی (بارگذاری هنگام نزدیک‌شدن)، بدون ذخیره توکن‌های خصوصی |

ترتیب فرم مشتری: هویت (نام مشتری، نام فروشگاه)؛ تماس (شماره تماس، واتساپ، ایمیل ارتباطی، زبان)؛ آدرس (کشور، شهر، منطقه، پلاک فروشگاه)؛ همکاری (گروه محصولات فروش، حجم خرید، نوع همکاری، نوع فروش)؛ مدیریت (فعال/غیرفعال، پین). ایمیل ارتباطی از ایمیل ورود تفکیک شود. فیلدهای جدید باید سمت سرور مجاز و اعتبارسنجی شوند و در نسخه پشتیبان/آندو نیز پوشش داده شوند؛ هر تغییر ساختار داده فقط با مهاجرت افزودنی و بکاپ خصوصی قبل انتشار. ثبت محلی مرورگر منبع رسمی مشتری نیست.

مشکلات باقی‌مانده: پذیرش مرورگری اطلاعات تجاری با حساب مدیر؛ آپلود تازه خانه با حساب مدیر اثبات نشده؛ نسخه سبک تصاویر خانه حدود۹۷٪ کم‌حجم‌تر شد؛ پاسخ۵۰۰ نخستین تولید سرد و تأخیر میزبان نیازمند پیگیری؛ سرعت روی گوشی واقعی هنوز تأیید نشده. جایگزین خارجی انگلیسی حذف و دریافت تصاویر قبلی اصلاح شد. TinyFish و آزمون گسترده ممنوع؛ بررسی نوع/ساخت و کنترل ضروری انتشار حفظ می‌شود.

## P0 — manager media reliability + direct home-image editing — 2026-10-06

Owner reports continued client complaints while the business manager signs in through the `amirau` alias. Main was checked before changing code: product media already has `thumb/card/detail` variants, card swipe + PhotoSwipe zoom, backgroundless 2px-shadow gallery controls, the 10% smaller producible pill, landscape-to-portrait server normalization, and active-admin authorization. These completed pieces are preserved rather than rebuilt. The existing alias test already proves a non-owner time-boxed admin mapped to `amirmashti1378@gmail.com` receives active-admin access; product and Home-media routes are `active.admin`, not owner-only.

| Rank | Method | Score | Why |
|---:|---|---:|---|
| 1 | Harden the existing media pipeline + direct inline Home edit affordances | 10 | Lowest regression risk; fixes mobile MIME, stale-revision and rate-limit friction while removing the ambiguous separate Home-image workflow |
| 2 | Replace media management with a new uploader | 7 | Could simplify UX but duplicates stable server/media-library behavior |
| 3 | Move all editing into Filament | 5 | Mature but violates the approved custom UI and is less friendly on phones |
| 4 | Client-only upload fixes | 4 | Browser-dependent and cannot be authoritative for upload safety |
| 5 | Accept raw originals and rely on CSS | 2 | Reintroduces slow media, inconsistent geometry and unsafe metadata assumptions |

Selected automatically: **#1**. Batch implementation: admin identity hydrates immediately after alias/email login; safe JPG/JPEG/PNG/WebP mobile inputs tolerate `image/jpg` or missing browser MIME while the server remains authoritative; an image-only 409 refreshes the exact product and retries once (never retries uncertain network/5xx outcomes); authenticated media allowance is 30/minute instead of 10; Home uploads are client-optimized and server-bounded; every managed Home image has an inline **Edit image** control after real admin login; quick uploads publish only to the current production/staging channel and refresh the visible Home image immediately.

Security keeps extension + MIME + decoded-content + size/dimension + authorization checks and application-generated filenames. Upload validation follows OWASP defense-in-depth guidance. The inline edit action follows direct-manipulation/visible-action usability guidance. Only focused media/login regressions should be run for this batch; do not expand visual test scope.

## P0 — rescue product-image upload: normalize landscape safely — 2026-10-06

Evidence from the owner screenshot: product fields persisted but the two selected camera images remained pending and the UI reported that image upload failed. The existing stack already has real multi-size media (thumb 320, card 800, detail 1600), sequential multi-image submission, public application-controlled media routes, swipe/lightbox/zoom, backgroundless gallery controls with 2px icon shadow, and the 10% smaller producible pill. Those completed pieces are not rebuilt.

Root cause: the backend controller rejected every landscape/square image before the sanitizing/media pipeline could run. This contradicted the current UI, which intentionally accepts ordinary camera images and optimizes them before upload. It also leaves an EXIF-orientation edge case for phone photos.

| Rank | Method | Score | Why |
|---:|---|---:|---|
| 1 | Accept safe image input, then normalize landscape/square to a non-destructive 3:4 portrait master on the server | 10 | Fixes the current screenshot, keeps the whole photo visible, preserves one canonical portrait contract, and works even when client preprocessing is unavailable |
| 2 | Client-only portrait framing before upload | 8 | Fast and reduces payload, but can be bypassed or unavailable on older/problem browsers |
| 3 | Reject landscape immediately in the browser | 6 | Clearer UX but does not satisfy the current selected images and remains client-only |
| 4 | Allow landscape permanently and depend on CSS contain | 5 | Upload works but breaks the requested portrait-media contract and keeps variable card geometry |
| 5 | Center-crop landscape to portrait | 2 | Simple but destroys user content and can cut garments/people |

Selected automatically: **#1**. The backend keeps MIME/content/dimension/size checks, adds an explicit extension allowlist, re-encodes metadata away, bounds the canonical master to 1920px, and frames landscape/square images into a 3:4 portrait with a softened same-image backdrop plus a full uncropped foreground. A focused regression test covers landscape upload and all three derivatives.
## Verified release closeout — shared lists, footer and account recovery, 2026-10-05

- Final UI sourcea0d0f4f40eb8260adc764a483aa40934ec719676 is deployed at Test29/root. FTP37351155272 PASS: QA111902081100 (26 focused frontend tests/type-check/build), upload111902623548, root111903658015. Backend6da413a PASS in CI37349066060 and Additive Deploy37349065618:72 focused tests/603 assertions, dependency checks, consistent private database snapshot and one create-only migration before guarded activation. No production customer/user deletion performed.
- Live browser: creating/copying a two-product short share reports Link copied. Browser clipboard read was empty, so a short-token recipient browser round-trip is NOT claimed. Independent production cookie sessions issue a54-character root link, resolve ordered22003/11001 and fetch both exact selected public products. Shared view was exercised using supported legacy v1 selector: it shows22003 then11001 after the recipient's own favorites are empty, independent of local wishlist. Test-only local selections were removed; original observed English locale restored. No raw share token saved in project files/docs/log summaries.
- Desktop1363x936 Persian/RTL proof confirms full footer brand in its own right-side column, outside Help and aligned with the four link columns; English layout was also observed. Legacy timestamp entry is normalized before hash history and links have no timestamp; final observed normal route is https://armaghantrading.com/#/favorites. Default images/real gallery behavior from the prior release remain preserved. Screenshot saved separately as armaghan-desktop-footer-1791222734534.jpg.
- Custom Users/Customers now expose backup-first delete, explicit saved-file acknowledgment, full-file checksum check, actor-bound expiring single-use receipt, encrypted database recovery, Undo and exact-original JSON import. Linked login/customer are archived together. Owner/self protected; other admins/reserved identities owner-only. Order history survives; sessions, reset tokens and Magic Links never revive after Undo. File import is same-database account recovery, not full disaster recovery. Isolated checks include forged backup rejection, stale linked-record detection, permission denial, preserved orders and revoked credentials.
- OPEN: real authenticated manager browser backup-download/delete/Undo/import acceptance (no real production row touched); actual phone acceptance and network/host latency diagnosis from the earlier media backlog. Guest/customer share TTL stays7/30days; permanent owner share-library UI was not selected. No TinyFish or broad suite. All requested capabilities are implemented and published; open items describe acceptance/optional scope, not missing code.

## Hash-history normalization — 2026-10-05

- Root/Test29 source9bea9dd passed FTP37350293553: QA111899206966, Test29 upload111899503287, root promotion111900412296. Live DOM confirms the shared-list recovery build, two selected cards and brand outside Help, aligned at the same desktop top as the link columns.
- Live verification exposed hash history capturing an old timestamp query before main cleans it. Normalize legacy entry timestamps in router/index.ts before creating hash history, so URLs/links stay clean and do not restore the query. Type-check/build PASS; no business/auth/schema change. This tiny final patch is queued for guarded release. Manager backup/download/delete/restore browser acceptance remains OPEN;72 focused backend tests and26 frontend tests passed. Independent production sessions resolve ordered22003/11001 and fetch both exact active product DTOs; link length54.

## Final startup correction for shared-list release — 2026-10-05

- Live cold navigation exposed a second forced full-page load on every ordinary entry until October12, with a timestamp query captured by navigation. Removed the repeated location.replace path; old timestamp URLs are still cleaned without reload and hash routes remain supported. UI revision now identifies the shared-lists/account-recovery build.
- Type-check/build and26 focused tests PASS after this source change. Main da95f8e UI release is still transferring; this final source/config release is serialized behind it. Backend remains successfully activated at6da413a. No extra schema or account-data mutation. Final root/browser verification remains PENDING.

## Shared lists, footer and account recovery — 2026-10-05 release

- Backend 6da413a38dea9a7952694fba14b3296648fcdb8c deployed successfully: Backend CI37349066060/job111895001567 PASS; Additive Deploy37349065618/job111895045557 PASS, 72 focused tests/603 assertions, dependency validation/audit, one create-only migration, consistent private database backup created, guarded activation/smoke and temporary cleanup PASS. No real account/customer was deleted. First candidate failed only on a transient newly-created test row's revision; the test now rereads persisted defaults.
- Existing production share issue/resolve already returned two ordered codes in independent sessions with a 54-character link. Recipient UI now retrieves selected product codes directly from the public catalog, preserving order and waiting for the actual products; it does not rely on device-local seeds/cache. Short 22-character tokens and old links remain compatible. Shared request helper coalesces first CSRF fetch and uses refreshed token after419; errors offer retry. Expiry stays guest7days/customer30days.
- Footer brand is outside the Help section. Desktop >=64rem uses a separate brand column aligned with the four link columns; smaller screens use a compact bottom row. No adjacent brand label.
- Custom Users/Customers adds backup-first delete, checksum verification, saved-file confirmation, database Undo and exact original JSON import. Customer/login delete together; order/history/credentials remain retained for recovery. Owner/self protected; other admin deletion/restore owner-only. Private JSON contains encrypted credential recovery material. Browser download must be confirmed by the manager; OS disk completion cannot be asserted programmatically.
- Local frontend: type-check/build and26 focused tests/9files PASS; essential frozen/editor/auth/share/root/FTP contracts PASS. UI Test29/root publication is PENDING in this commit. Authenticated backup-download/delete/restore browser acceptance remains OPEN; security/round-trip semantics are covered by isolated tests. No TinyFish or broad suite.

## Account sharing and recoverable deletion — implementation gate, 2026-10-05

- Existing production share issue/resolve verified with two independent cookie sessions: 54-character URL, ordered product codes11001/22003 returned. No raw token logged or persisted in project files. Recipient UI still depends on the local catalog; targeted public product-code filtering is added for the upcoming direct-fetch UI fix.
- Backend account deletion adds one create-only account_archives table. Backup preparation returns an encrypted recovery envelope and one-time 15-minute actor-bound receipt; deletion requires receipt, exact backup digest and explicit download confirmation. Linked customer/login records are hidden and disabled together, while IDs/order relations are retained. Protected owner/self remain undeletable; only primary owner manages other admins. Encrypted DB snapshot supports Undo and exact original JSON import. Old sessions/reset/magic credentials are revoked, including customer-session generation across Undo.
- Backend PHP is unavailable locally. Publication is gated by focused account/share/catalog/auth/customer/order tests in Backend CI and Additive Deploy, dependency checks and a consistent private SQLite snapshot. Deployment and authenticated browser acceptance are PENDING. No real customer/user deletion performed. UI work remains local until backend activation passes. No TinyFish or broad suite.

## P0 loading, subcategory specifications and home image editing — 2026-10-05

## Verified publication and focused live acceptance — 2026-10-05

- Source 9bfadb4566a9e14f6a0d65917f1ebd98bfa95b63 is published at root/Test29. FTP Deploy 37336127360 PASS: QA 111851295020, upload/HTTP checks 111851616862, guarded root promotion 111853272793. Seventeen focused release tests/type-check/build passed in CI; three additional product-card tests passed locally.
- Fresh live DOM revision is 2026-10-05-loading-specs-home-media. At viewport height 936px, first eight card images (rows starting at 410/896px) exist and ten farther cards have no image src; all 18 product cards have zero visible SmartImage SVG placeholders. Moving to card32003 activates its sub-32 fallback. Real media2 and media3 thumbnails decoded at 320px in this desktop session.
- Live product11001 specification sheet shows 2 fixed + 5 negotiable definitions; product32003 shows 2 fixed + 7 negotiable definitions, matching Test28. Screenshot of product11001 with real media and default-image neighboring cards saved separately. Loading fallback is additionally covered by the focused SmartImage test.
- Curl timing samples from this environment: TLS connection ~6.18s for root/static/media; root first byte 7.74s, static8.01s, media8.45s. Most measured delay occurs before TLS finishes; remaining response+network ~1.56–2.27s. This does not prove a host-only bottleneck or phone scroll speed. Initial entry+modulepreload JS is 338338 bytes (additional route chunks load on demand).
- OPEN: owner's real-mobile speed/pinch acceptance; isolated connection/host latency diagnosis; server taxonomy-definition provisioning if admin editing of restored default specifications is required; authenticated replacement-photo upload/publication acceptance for the new home shortcuts. No backend/schema change, no new supplied photo upload, no TinyFish, no broad suite.


- [x] Show the selected no-photo product image during real-photo loading and failure.
- [x] Restore Test28 specification groups for all six subcategories when server definitions are empty; preserve nonempty server definitions and values.
- [x] Load product images only near the viewport with 30% viewport-height preload, using one shared observer; avoid eager blurred backgrounds.
- [x] Reduce redundant catalog hydration/persistence and measure remaining latency without claiming real-device speed.
- [x] Put all eight home-image targets at the top of the visual editor, with direct file selection and visual selection of stored images.
- [x] Essential media/specification checks, type-check/build, guarded Test29/root publication; no TinyFish or broad test suite.

| Rank | Loading/fallback | Specifications | Performance | Home image editing |
|---:|---|---|---|---|
| 1 | Shared near-viewport observer + same local fallback | Test28 defaults only when definitions missing | Remove eager image work and redundant refresh | Top image shortcut strip + existing media panel |
| 2 | Native lazy only | Show defaults everywhere | Reduce all thumbnail dimensions | Existing nested target browser |
| 3 | Preload all cards | Duplicate server templates | Paginated listing | Separate admin screen |
| 4 | New loader library | Rebuild all records | New rendering framework | Parallel editor/storage |
| 5 | SVG-only waiting | Hide empty specifications | Longer network timeout | Unvalidated external image URLs |

Selected rank 1: directly fixes observed empty specifications and eager background requests while reusing canonical media editing and keeping originals/frozen snapshots unchanged. Actual latency and phone scrolling acceptance remain open until measured.



## Loading, specifications and home media batch — 2026-10-05

- Implemented the owner's loading-image request: product cards use the chosen no-photo subcategory asset during real-photo loading and failure, with no visible generic SVG.
- Verified snapshot/test28-final supplies the same six subMeta specification groups. Live catalog returned specifications=[] for sampled products; empty remote arrays had erased the defaults. Empty/missing definitions now restore the Test28 group and remove stale empty cached values; nonempty server definitions/values remain authoritative. ProductDetailSheet also handles empty values safely. These are specification labels/classification, not invented product-specific values; server taxonomy-definition provisioning remains a follow-up if admin editing of defaults is needed.
- Product images share one IntersectionObserver and start only within 30% of viewport height above/below the screen. Margin is calculated in pixels because percentage rootMargin uses width. Product cards use full-image contain without an eager blurred background. The same local default displays until the real image load event. No new image-size change or original modification was needed.
- Catalog refreshes share in-flight work and reuse a successful result for 30 seconds. Admin saves bypass freshness and wait/refetch even during an existing request. Duplicate local-storage snapshots are skipped and storage errors cannot break browsing.
- All non-admin routes load on demand. VisualEditor loads only when enabled; product admin editor/manager load only when opened. Built main JS entry reduced from 593.69 kB/176.05 kB gzip to 261.76 kB/77.39 kB gzip; shared/route chunks are additional and this is not a total-transfer or phone-speed claim.
- Home visual editor now begins with eight localized image shortcuts. Selecting a panel or its text also exposes its image control. Existing HomeMediaPanel provides a prominent file picker and stored-image thumbnails, retains revision/auth validation and follows the editor's production/staging channel. No real authenticated image upload/publication was performed without a supplied replacement photo.
- Local essential validation PASS: type-check/build, 17 focused tests in 6 files, current editor/auth and guarded publication contracts, frozen snapshot guard and diff hygiene. No TinyFish or broad suite. Publication PASS in FTP workflow 37336127360; real mobile scrolling/pinch and actual host/network bottleneck acceptance remain OPEN.

## P0 fast media + owner alias batch — 2026-10-05

## Lightweight card release closeout — 2026-10-05

- Owner requested no TinyFish, essential tests only, and verification before repeating work from another chat. These are now durable PROJECT-RULES 185–187 and root AGENTS instructions.
- Existing source commit 4c3950f52aff1091b4b0d507a69f6c63344eb865 already implements transparent previous/next/zoom controls with 2px icon shadow and 44px touch targets; smaller placeholders; and a roughly 10% smaller Producible pill. No duplicate UI implementation was needed in this run.
- Publication was the missing item. FTP runs 37315056108 and the first attempt of 37316797792 failed on host transfer timeouts. Re-ran only failed jobs of 37316797792: second attempt PASS, scoped Test29 upload and HTTP verification PASS, guarded root promotion PASS (root job 111820366059). QA already passed type-check/build, 9 focused frontend tests in 2 files, and essential publication contracts. No broad suite was rerun.
- Reproducible placeholders: 18 portrait WebP files at 480x720 total 165044 bytes; 18 landscape WebP files at 480x360 total 161220 bytes. Immutable source originals total 1541490 bytes and remain preserved. Representative old portrait paper-cut/11 was 59236 bytes; its new derivative is 7914 bytes.
- Remaining P0 is measured request latency and failed-thumbnail recovery, followed by owner real-mobile acceptance of all uploaded cards. This run sampled root/static-placeholder/media/catalog requests at about 8.9–10.3 seconds from the execution environment. That includes network/environment overhead and does not isolate host latency or prove user-device scrolling speed. Avoid a broad backend delivery refactor until the bottleneck is isolated.
- No TinyFish or visual/device test was used in this run. Existing owner-alias login/backend changes from the other chat were not modified; their CI and code deployment are already successful.


| موضوع | 1 (انتخاب‌شده) | 2 | 3 | 4 | 5 |
|---|---|---|---|---|---|
| بارگذاری کارت | thumb 320 فقط در کارت + detail فقط در lightbox | srcset thumb/card | card 800 همیشه | JS resize بعد از load | original |
| fallback | مشتق 480×720 WebP q64 + landscape max-480 | فقط AVIF | همان 960×1440 | CSS-only | فایل اصلی |
| عکس افقی | full image با contain-blur؛ ورودی جدید portrait-only | crop ثابت | letterbox سفید | حذف legacy | stretch |
| کنترل گالری | hit-area 44px شفاف + shadow 2px روی آیکون | نیمه‌شفاف | solid | فقط swipe | بدون کنترل |
| mot/mot | alias محدود به primary owner موجود + rate-limit + audit، بدون تغییر رمز واقعی | تغییر رمز DB مالک | حساب دوم admin | bypass auth | رمز خام |
| تست سریع | type-check/build + focused current-domain/safety | کل unit | کل regression تاریخی | بدون تست | تست دستی |

انتخاب رتبه 1 بر مبنای responsive/lazy media، PhotoSwipe lazy loading، و authentication محدود و قابل audit است. Full test suite حذف نشده؛ فقط gate انتشار موقتاً محدود شده است.

- [x] کارت اولیه فقط thumb سبک؛ detail فقط هنگام lightbox.
- [x] قبلی/بعدی/zoom بدون زمینه، با سایه 2px و hit target لمسی 44px.
- [x] fallbackهای portrait به 480×720/q64 و landscape fallback به max-480 کاهش یافتند.
- [x] badge «تولیدپذیر» و بیضی آن حدود 10% کوچک‌تر شد.
- [x] Filament login identifier فعال: mot/mot فقط به motealle@gmail.com primary owner نگاشت می‌شود، رمز واقعی overwrite نمی‌شود و backend advanced access به‌صورت 15 دقیقه‌ای/audited باز می‌شود.
- [x] آپلود جدید محصول portrait-only؛ landscapeهای legacy با contain-blur باقی می‌مانند.
- [x] fast release gate موقت: frontend cardMedia/catalog + type-check/build؛ backend auth/admin tests + syntax/dependency audit.
- [ ] P0 بعدی: اگر latency route رسانه بعد از این انتشار هنوز چندثانیه‌ای بود، static/offload delivery را به‌عنوان bottleneck مستقل حل کن.

# Armaghan — Product Backlog

## Card gallery release evidence — 2026-10-05

- Standing owner authorization and lock retirement are merged in main (b48b647 / PR26). No shared repository lease is required. Keep ordinary Git conflict checks and guarded deployment serialization.
- Backend release b48b647: Backend CI 37309303415 and 37309471663 PASS, 130 tests / 1193 assertions; Backend Code Deploy 37309471757 PASS.
- Final UI release 1a11534018687e0fa367be6ea694a8aab5037442: FTP Deploy 37310838574 PASS, 31 publication contracts, 91 permanent frontend tests, type-check/build, Test29 upload/checksum and root promotion PASS. Prior FTP 37309471774 stopped before publication on obsolete no-PhotoSwipe contract; updated contracts require lazy loading, cleanup and contained responsive media. Intermediate publication 37309814913 PASS.
- Live root DOM confirms fresh /thumb?v=20261005-card-gallery-3 delivery candidates, zero image padding and contain fit. Card11001 Next changes media2 to media1. PhotoSwipe opens 2/6, real detail images decode (media1 1086x1448, media2 1023x1537 and landscape media6 1600x800), zoom toggle and close work. Screenshot evidence saved separately; actual mobile pinch/swipe acceptance remains OPEN.
- The browser test exposed a repeat-hydration failure not covered by the previous single-pass Proxy test. JSON-only catalog DTO cloning now handles nested proxies. Cached server URLs are immediately normalized to lightweight variants before network hydration, with server-only deduplication. Added permanent repeated-hydration and cached-gallery regression tests; an isolated real-public-catalog probe also passed and was removed rather than committed.
- Public thumbnail sample media2: WebP 320x481 / 11838 bytes / HTTP200. Public media10 thumbnail: HTTP200 /1220 bytes. This is transfer-size evidence, not a measured few-second user acceptance. Public requests showed 7.9–10.9-second latency and one 12-second timeout; some cloud-browser thumbnail attempts fell back. P0 OPEN: investigate host/media latency and failed-thumbnail recovery, verify all four uploaded-product cards from a cold real mobile session. Do not mark global speed/image acceptance complete.
- Completion sound: no task-completion sound control is exposed to this agent; plugin searches found no relevant notifier. No plugin installed and no audible alert claimed. User-facing notification configuration is Settings > Notifications; device sound control remains outside this execution surface.


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


## P0 — رفع قطعی نمایش عکس کارت محصول — ۲۰۲۶-۱۰-۰۵

شاهد production: API زنده برای محصول 11001 شش رسانه واقعی با مسیر `/backend/api/catalog/media/.../card` برمی‌گرداند، اما مرورگر عمومی همان کارت را با placeholder دسته نمایش می‌دهد. ریشه در frontend hydration است: Pinia داده‌های fallback را به Vue Proxy تبدیل می‌کند و `structuredClone(proxy)` می‌تواند `DataCloneError` بدهد؛ catch فعلی خطا را به `syncState=error` تبدیل می‌کند و seed/placeholder باقی می‌ماند.

| رتبه | روش | امتیاز | دلیل |
|---:|---|---:|---|
| 1 | بازکردن Proxy با `toRaw` دقیقاً در مرز clone + تست reactive | 10 | علت واقعی را در همان لایه می‌زند؛ localStorage و backend دست‌نخورده می‌مانند؛ قابل تست و کم‌دامنه است |
| 2 | `toRaw` فقط داخل Store پیش از merge | 8.5 | مؤثر است ولی merge در برابر callerهای reactive دیگر همچنان شکننده می‌ماند |
| 3 | clone با JSON serialize/parse | 6 | Proxy را دور می‌زند اما semantics داده را ضعیف‌تر و خطاهای آینده را پنهان می‌کند |
| 4 | پاک‌کردن اجباری localStorage و seed | 3 | symptom را کم می‌کند ولی علت DataCloneError را رفع نمی‌کند |
| 5 | fetch مستقیم عکس داخل ProductCard | 1 | معماری canonical catalog را دور می‌زند و دو منبع حقیقت می‌سازد |

انتخاب خودکار: گزینه 1. همچنین revision رسانه از `card-hotfix-1` به `card-hotfix-2` می‌رود تا بعد از merge صحیح، مرورگر عکس تازه را بدون cache قدیمی دریافت کند. مرجع فنی: Vue `toRaw` برای بازگشت به object اصلی Proxy و MDN برای `structuredClone`/DataCloneError.

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

## P0 — Admin UX and editor reliability — 2026-10-05

Implemented the current admin-login reliability batch and the route-scoped editing flow. The home appearance editor is limited to the home page and now supports home-media selection plus fit/crop/focal controls. The products-page manager is limited to the products page, exposes edit beside the product code, supports adding products, and reuses the same canonical product editor/gallery used by the admin panel. Nested editing sheets were avoided.

The product gallery contract remains server-authoritative: zero persisted media means zero fake slots; persisted media supports real-image rendering, explicit unavailable state, selection, deletion, reordering, and primary ordering. Home/product media delivery no longer depends on the shared-host public-storage symlink.

Session persistence, one-time setup state, stable Tracking copy, reduced duplicate exit controls, and owner-controlled administrative revocation are included. Backend CI run 37277405193 passed with 129 tests / 1169 assertions. Visual acceptance is intentionally deferred to owner screenshots.

## P0 — ورود ماندگار مدیر + ویرایشگرهای محدود به صفحه — ۲۰۲۶-۱۰-۰۵

- ورود مدیر با identifier (ایمیل یا نام کاربری) انجام می‌شود. دو alias موقت اپراتوری بدون ذخیره رمز خام فعال‌اند: `mot` برای مالک فنی تا ۲۰۲۶-۱۰-۱۹ و `amirau` برای مدیر کسب‌وکار تا ۲۰۲۶-۱۱-۰۵. هر دو به حساب‌های مجاز از پیش تعریف‌شده نگاشت می‌شوند؛ ورود جدید پس از تاریخ انقضا رد می‌شود، اما Remember Cookie لاراول که قبلاً صادر شده تا logout یا revoke مالک معتبر می‌ماند.
- login مدیر با Laravel remember token ماندگار است؛ خروج اجباری مدیر دیگر فقط از مسیر مالک اصلی انجام می‌شود و deactivation هم sessionهای سرور و هم remember_token را revoke می‌کند.
- prompt تنظیم رمز بعد از هر Google login رفع شد: وضعیت تکمیل setup در دیتابیس ثبت می‌شود و Google login بعدی مستقیم به ادمین برمی‌گردد. مدیر می‌تواند رمز ساده حداقل ۸ نویسه تعیین کند؛ مشتری عادی همان سیاست ۱۲ نویسه + حرف/عدد را نگه می‌دارد.
- عنوان «پیگیری» بعد از ورود تغییر نمی‌کند و logout تکراری داخل نمای ادمین حذف شد؛ تنها کنترل خروج عمومی هدر باقی مانده است.
- دکمه Google به نشان رنگی شناخته‌شده مجهز شد. خطای provider مثل Unusual traffic قابل حذف تضمینی از سمت سایت نیست؛ سیاست اصلی کاهش مراجعه مجدد به Google با remember cookie است.
- «ویرایش ظاهر» فقط در صفحه اصلی نمایش داده می‌شود. انتخاب تصویر hero/about/capabilities/banners از همان editor به HomeMediaPanel متصل است و برای media کنترل contain/cover و focal point افقی/عمودی ذخیره‌پذیر اضافه شد. home media نیز از Laravel route سرو می‌شود تا به symlink هاست وابسته نباشد.
- «مدیریت محصولات» فقط در صفحه محصولات برای admin session واقعی ظاهر می‌شود. دکمه مداد کنار کد هر کارت همان BackendProductEditor را برای همان محصول باز می‌کند؛ دکمه مدیریت کل محصولات یک Bottom Sheet شامل فهرست و Add Product دارد، ولی برای جلوگیری از sheet داخل sheet، انتخاب محصول Sheet فهرست را می‌بندد و همان editor واحد را باز می‌کند. پنل ادمین نیز همین editor/gallery را reuse می‌کند.
- Gallery محصول همچنان source-of-truth سرور است: کارت فقط برای media persisted ساخته می‌شود، عکس واقعی/خطای صریح، انتخاب چندتایی، حذف، reorder و primary image دارد؛ zero media یعنی zero placeholder.
- Backend CI روی head قبل از closeout: run `37277405193` PASS؛ 129 تست و 1169 assertion. تست بصری طبق دستور مالک انجام نشد و مرجع بصری اسکرین‌شات‌های مالک است.

## P0 — Gallery Manager و ویرایش از صفحه محصولات — ۲۰۲۶-۱۰-۰۵

ریشه شکست نمایش مشخص شد: رکوردهای رسانه واقعی در API موجود بودند (مثلاً محصول 11001 پنج رسانه داشت) اما URLهای مستقیم `/backend/storage/media/...` روی هاست 404 می‌دادند؛ SmartImage این شکست را با آیکون لباس می‌پوشاند و کارت‌های گمراه‌کننده می‌ساخت. راه منتخب: سرو رسانه از route کنترل‌شده Laravel `/api/catalog/media/{id}/{thumb|card|detail}` با fallback فایل واقعی، cache immutable و nosniff؛ عدم وابستگی به symlink عمومی هاست.

Gallery Manager: صفر رسانه = صفر کارت؛ رسانه موجود عکس واقعی یا خطای صریح «تصویر در دسترس نیست» نشان می‌دهد؛ انتخاب چندتایی، حذف گروهی مالکیت‌سنجی‌شده، جابه‌جایی، برچسب تصویر اصلی و پیش‌نمایش فایل‌های pending حفظ شدند. حذف با revision انجام می‌شود، رسانه خارجی رد می‌شود و stale revision conflict می‌دهد.

صفحه محصولات: فقط `admin.identity` حاصل از session واقعی سرور دکمه ویرایش روی کارت‌ها را فعال می‌کند؛ همان BackendProductEditor به‌صورت Adaptive/Bottom Sheet باز می‌شود. رکورد backend دقیق با code ویرایش می‌شود؛ seed-only آینده با همان کد/دسته/نام/مشخصات prefill و اولین save به رکورد واقعی تبدیل می‌شود. مشتری/مهمان هیچ کنترل مدیریتی نمی‌بیند.

آزمون شاخه پس از دو اصلاح تستی/کش relation: Gallery Admin Slice CI `37256170956` PASS؛ backend 125 تست / 1132 assertion + Composer audit پاک؛ frontend 84 تست / 19 فایل، type-check و production build PASS. workflow موقت قبل از merge حذف شده است. انتشار production و پذیرش موبایل واقعی پس از merge باید تأیید شود.

## تأیید انتشار نجات عکس محصول — ۲۰۲۶-۱۰-۰۴

انتشار نهایی commit `e939d62ee3dae57f34dd94082dffa3b9d5244cde` تأیید شد. Backend CI `37230976144`: 124 تست و 1112 assertion موفق و audit وابستگی‌ها بدون هشدار امنیتی. Backend Code Deploy `37230976112`: فعال‌سازی با پشتیبان، بدون تغییر dependency/migration و smoke عمومی موفق. FTP Deploy `37230976156`: QA/build رابط، انتشار Test29، تطبیق HTTP فایل فعال `assets/index-CPd_QK39.js` و promotion ریشه version 29 همگی PASS. این تأیید غیرتصویری است؛ آزمون واقعی آپلود با حساب مدیر و گوشی مشتری همچنان acceptance باز است.

## P0 — نجات مسیر عکس محصول — ۲۰۲۶-۱۰-۰۴

- انتخاب چندعکس هم‌زمان، پیش‌نمایش، حذف انتخاب و ارسال ترتیبی با revision تازه بعد از هر عکس پیاده شد؛ خطای یک عکس، محصول یا عکس‌های تأییدشده قبلی را از بین نمی‌برد.
- عکس‌های دوربین تا ۲۰MB در رابط پذیرفته و قبل از ارسال در مرورگر تا حداکثر ضلع ۱۹۲۰px بهینه می‌شوند؛ سقف واقعی درخواست سرور ۸MB باقی مانده و اعتبارسنجی/بازنویسی امن سمت سرور حفظ است.
- سرور برای هر تصویر سه WebP مشتق می‌سازد: thumb 320/q72، card 800/q78 و detail 1600/q82، بدون crop یا upscale اجباری؛ master پاک‌سازی‌شده حفظ می‌شود.
- Backend CI 37230605259 موفق بود. رابط نیز در ران‌های 37230693451 و 37230789948 با type-check، 82 تست در 18 فایل و production build موفق شد. workflow آزمایشی موقت قبل از merge حذف شد.
- پذیرش واقعی با حساب مدیر و گوشی پس از انتشار هنوز لازم است؛ تا آن زمان «رفع قطعی روی دستگاه مشتری» ادعا نشود.

## P0 — زیرساخت ورود OTP موبایل — ۲۰۲۶-۱۰-۰۴
پکیج `fouladgar/laravel-otp ^6.2` با lock تولیدشده واقعی Composer نصب و در `eace74da4a58c58703b58a8f36a586a5aa8ccdba` ادغام شد. نصب اولیه روی PHP 8.3 با Composer validate، migrate:fresh، کل تست‌های بک‌اند و audit موفق بود. Backend CI `37223470009` موفق؛ Backend Additive Deploy `37223470049` نیز نصب وابستگی، تست، ساخت vendor و انتشار واقعی روی هاست را با موفقیت کامل کرد. Backend Code Deploy به‌درستی به علت dependency drift از مسیر code-only عبور نکرد و FTP Deploy تغییری در UI/ریشه نداد. ورود پیامکی برای مشتری هنوز عمداً فعال نشده است: خرید/تنظیم سرویس SMS، API key و pattern، تغییر محدود هویت موبایل/ایمیل و مسیرهای request/verify OTP با rate limit و تست لازم است. OTP نباید در لاگ یا کد/ریپو ذخیره شود.

## P0 — Short WhatsApp FavoriteShare links — 2026-10-04
Owner-authorized takeover of stopped run `codex-20261004-short-favorite-share`. Selected design: new 22-character cryptographically random token (>128-bit entropy) plus `/#/s/<token>`; raw token remains hash-only in storage and fixed POST resolution remains unchanged. Historical 64-character tokens and `/#/favorites/share/<token>` routes stay compatible. Stale numbered-test share-path configuration is normalized to root short links. No schema/dependency change. Source commit `9e1631820006884aea08a6c06b1d7385d18e9555` is verified and released. Backend CI run 37221086606 PASS: 124 tests / 1107 assertions. Backend Code Deploy 37221086686 PASS: code-only activation, backup, no dependency/migration drift, live health/admin/catalog smokes PASS. FTP Deploy 37221086643 PASS: 80 frontend tests across 18 files, type-check/build, Test29 deployment verification and root version 29 promotion PASS. New shares use `/#/s/<22-char-token>`; historical 64-character `/#/favorites/share/<token>` links remain compatible.

تأیید نهایی انتشار این بچ: کد e6d0d9a49f8fae970cba653d08c450fa18e6ff92؛ اجرای37218127601 در همه مراحل آزمون، اتصال هاست، انتقال نسخه۲۹ و فعال‌سازی ریشه موفق شد. خواندن تازه ریشه و /t/29/ و فایل index-CehMWVjX.js همگی۲۰۰؛ برابر بایت‌به‌بایت با خروجی محلی آزموده‌شده، SHA256=a9ddd88291015d079e28a75dd837797647cde7d2833289fcfe884adbb8edaa6e. فایل فعال حاوی پیام‌های ارتباط مستقیم، شماره فروش و «ذخیره و ارسال عکس» است. هیچ درخواست خصوصی، ارسال واقعی پیام، تغییر حساب یا پایگاه داده در این بچ انجام نشد؛ پذیرش واقعی آپلود/دستگاهی همچنان باز است.

## ارتباط مستقیم مشتری و ورود با موبایل — ۲۰۲۶-۱۰-۰۴

دکمه پیاده، آزموده و منتشر شده؛ بررسی عمومی فعال موفق است. مبنا58f6a034676b908a51240e62002ce246a7869b94؛ قفلcodex-20261004-customer-direct-contact. کارت «ارتباط با مسئولان ارمغان» در حساب/پنل مشتری مستقل از ورود/محصول/سفارش، گفت‌وگوی خالی با شماره فروش موجود989933509793 باز می‌کند. لینک از سرویس مشترک ساخته می‌شود؛ متن، شناسه، مشخصات حساب/محصول/سفارش به واتساپ اضافه نمی‌شود. چهار زبان، عنوان و نام دسترس‌پذیر و حفاظت مرورگر مقصد رعایت شدند؛ پیام واقعی ارسال نشد. ۷۹ آزمون رابط در۱۸فایل، بررسی نوع‌ها، ساخت برنامه و۳۱بررسی انتشار موفق‌اند. پشتیبانی ابزار Vue (سازنده رابط) موجود به تنظیمات آزمون افزوده شد تا نمایش واقعی کارت در چهار زبان بررسی شود؛ وابستگی تازه‌ای نصب نشد.

آپلود عکس محصول قبلاً با انتشار4fad101/اجرای37184885632 تحویل شده است؛ انتخاب عکس سپس «ذخیره و ارسال عکس» برای مدیر کسب‌وکار فعال است. مشتری عادی اختیار ویرایش عکس کاتالوگ ندارد. آزمون خصوصی واقعی همچنان باز است.

ورود/ثبت‌نام با موبایل یا ایمیل هنوز فعال نیست. نقشه اجرایی docs/PHONE-OR-EMAIL-LOGIN.md اولویت بچ مستقل بعدی است: حساب اصلی با شماره یکتا/ایمیل اختیاری و رمز، بدون ایمیل صوری و وابستگی پیامک. users.email اکنون اجباری است؛ تغییر محدود ساختار و مسیر انتشار با پشتیبان و آزمون لازم است. شماره تماس فعلی به‌تنهایی شناسه ورود نیست. بازنشانی رمز/قطع نشست و سایر موارد پیشین باز می‌مانند. نسخه‌های۰۱–۲۸ و سرور/حساب‌ها در این بچ تغییر نکرده‌اند.


| رتبه | ارتباط | امتیاز | دلیل |
|---:|---|---:|---|
| ۱ | لینک مستقیم در پنل | ۱۰ | همان خواسته؛ مستقل از انتخاب |
| ۲ | لینک صفحه تماس | ۸ | دورتر از پنل |
| ۳ | فرم و سپس واتساپ | ۶ | مرحله اضافه |
| ۴ | وابستگی به محصول | ۳ | برای انتقاد نامناسب |
| ۵ | شماره متنی | ۱ | نیازمند کپی شماره |

- [x] دکمه مستقیم چهارزبانه بدون متن/داده خودکار؛ مستقل از حساب و سفارش.
- [x] ۷۹ آزمون رابط، ساخت و۳۱بررسی انتشار.
- [x] تأیید انتشار و فایل فعال ریشه/۲۹ برای این بچ؛ اجرای37218127601 و تطبیق بایت‌به‌بایت موفق.
- [ ] ورود/ثبت‌نام موبایل یا ایمیل طبق نقشه مستقل؛ تغییر ظاهری کافی نیست.
- [ ] آزمون واقعی آپلود مدیر/گوشی مشتری؛ همچنان باز.

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

## انتخاب و اجرای بچ فرم محصول — ۲۰۲۶-۱۰-۰۴

| رتبه | روش | امتیاز | دلیل |
|---:|---|---:|---|
| ۱ | حفظ ورودی، خطای مشخص و تأیید بستن | ۱۰ | انتخاب و اجرا؛ دامنه محدود و رفتار آزمون‌پذیر |
| ۲ | پیش‌نویس مشترک روی سرور | ۸ | نیازمند ذخیره‌سازی و سیاست تازه |
| ۳ | پیش‌نویس مرورگر | ۶ | انتقال بین دستگاه‌ها ندارد |
| ۴ | تکرار خودکار درخواست | ۳ | پاسخ گمشده می‌تواند به ارسال تکراری منجر شود |
| ۵ | خطای عمومی قبلی | ۱ | علت و راه اصلاح روشن نیست |

- [x] حفاظت ورودی محصول موجود/تازه هنگام بستن، مسیر جدید و خروج از صفحه؛ جلوگیری از بستن هنگام ذخیره.
- [x] پیام چهارزبانه خطای کد/زیردسته/مشخصات/عکس و درخواست زیاد؛ حفظ ورودی در اعتبارسنجی ناموفق.
- [x] مسدودکردن تکرار درخواست با نتیجه نامشخص یا نسخه قدیمی؛ راهنمای دریافت تازه فهرست.
- [x] آزمون محلی: ۶۸ آزمون رابط + بررسی نوع‌ها/ساخت + ۳۰ بررسی انتشار.
- [x] تأیید انتشار این بچ و فایل فعال ریشه/۲۹؛ اجرای 37173258992 موفق و فایل جدید هر دو مسیر یکسان است.
- [ ] پذیرش واقعی با حساب مدیر و دستگاه مشتری؛ آزمون خودکار جای آن را نمی‌گیرد.

درس قابل تکرار: پس از خطای شبکه، «ذخیره نشد» را فرض نکن؛ ممکن است سرور ذخیره کرده و پاسخ گم شده باشد. نخست وضعیت واقعی را بخوان، سپس اقدام تازه را آغاز کن. آزمون پایدار این رفتار در `productEditorSafety.spec.ts` ثبت شد.

## Urgent presentation and offline customer login — 2026-10-03

DONE and live. Baseline6d67ffca26a70a55e3834942ef1908c7b8b4d83b; lease codex-20261003-hero-fit-customer-account. Customer supersedes earlier bare-gold/shadow/outline choice: gold text on centered navy #101a44 oval, no stroke/shadow, same typography as category titles. Main single-hero uses actual loaded image aspect ratio with auto height/min-height0 and contain; removes fixed-height/blur letterboxing for approved1672×941 image while preserving composition and caption/carousel. Intrinsic geometry is opt-in to SmartImage; other images unchanged. Production wizard category numbers01–03 and subcategory codes now obey existing default-false appearance flags; unnumbered grid uses one column. Explicit admin re-enabling remains possible. Saved published appearance overrides and device/visual acceptance must be assessed separately; no screenshot was supplied this run.

Rollout guard: dedicated POST /api/admin/customers/{customer}/account rejects absent old route rather than silently creating a new CRM record. UI shows action only for explicit server has_account=false; role/active/customer_id forged fields prohibited on dedicated route. Initial Backend CI37148102656 failed two new-test assertions (singular activity_log table and database-default refresh in test revision); no code deployment accepted. Tests corrected to use canonical fresh snapshot; dedicated route guest/customer/disabled-admin/forgery coverage added.

New canonical capability: custom Customers directory offers Create login account on active CRM-only rows. Explicit name/email/per-account initial password+confirmation and identity/access acknowledgement; one atomic server transaction creates a customer-role login and links the same customer ID, preserving company/contact/priority/notes/direct-link settings, orders, tags and history. Locked customer row must still be active/unlinked and match canonical revision; fresh active admin, reserved Google mailboxes, customer-only role and active-account requirements enforced. Existing linked identities can never be reassigned, even by technical manager; existing-login merging/relinking stays OPEN. No new schema/dependency or duplicate Customer. Audit stores IDs/initialized-field names only, not password/email payload; password is hashed, no invented verification. Admin directory adds private has_account field; no customer/public response expansion. Modal clears secrets on close/success, reloads actual records after success. Existing direct-link/session policies unchanged; this is not existing-account password reset or session-revocation completion.

Local Vue61tests/15files/type-check/build and30 current release/source/frozen/media/root checks PASS. Verified release: Backend CI37148369640 SUCCESS,123tests/1104assertions, strict Composer validation/dependency audit/secret hygiene PASS. Backend CodeDeploy37148369841 SUCCESS with guarded code-only activation/health/cleanup; no migrations/dependency changes. First FTP37148102694 and final FTP37148369664 SUCCESS (QA, smoke, scoped Test29/checksums and guarded root promotion). Fresh root and /t/29/ both200 reference index-DkHodfID.js / index-CjtaQLXW.css; fetched assets200 contain intrinsic image ratio, wizard appearance flags, navy status pill and dedicated account route/strict has_account=false rollout guard. Default #ffb514 on #101a44 contrast9.47:1. Live backend/up200; CSRF-valid guest POST to new customer account route401. Actual authenticated owner/device/visual acceptance stays OPEN. New focused tests cover no duplicate/history loss, actual password login to same customer and own orders, private-note omission, stale/inactive/missing/already-linked target rollback, reserved identity/role/password/missing revision/email duplication and secret-free audit. No live customer/account/password/email/order records mutated for testing. Root29 selector revision refreshed; frozen01–28 preserved. Backend guarded code-only lane; no deployed migration changes.

Run checklist:
- [x] Intrinsic full-composition hero fit deployed.
- [x] Production wizard category/subcategory numbering hidden by default, recoverable flags retained.
- [x] Centered gold/navy status oval without shadow/stroke deployed.
- [x] Create NEW login for existing offline customer, preserve same ID/history and reject reassignment; tests/code/runtime publication verified.
- [ ] Actual authenticated owner/customer and mobile/desktop visual acceptance.
- [ ] Secure existing-account reset/session revocation and safe bulk removal.
- [ ] Existing-login merge/association with explicit identity proof; titled WhatsApp/shared settings; invoice/receipt self-service, configured mail and private-media recovery.

Backlog progress: offline CRM→NEW login creation DONE; persistent tags/specifications/manual timeline/product photos/codes already DONE. Existing account merge/association needs explicit safe identity proof and dependency policy, remains OPEN. Next: secure existing-account reset/session revocation, dependency-safe bulk deletion; titled WhatsApp/shared settings; customer invoice/receipt self-service/confirmed-quote corrections; configured mail, encrypted private-media backup/recovery and authenticated owner/customer/mobile acceptance. Do not claim complete project delivery or visual acceptance from automated/source checks.

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


## Root Test29 release — current

- [x] Owner-authorized guarded root selector; preserve /t and frozen 01–28.
- [x] Root 29 and /t/29 deployment + exact HTML/initial asset verification; eight rollback/version tests.
- [x] Hide four Home eyebrows by default; editor shows them again with one action.
- [x] Concise producible badge: smaller, weight 400, centered; verified at root.
- [x] Keep root navigation/skip links at root; initial Home imagery loads.
- [x] Direct real Filament product/photo/customer navigation; retain backend authentication boundaries.
- [x] Official Google Socialite dependency, stable identity table, signup/sign-in, no admin/email-only linking; 49 backend tests + guarded additive deployment PASS.
- [x] Root only accepts real customer sessions; 49 frontend tests PASS.
- [x] Owner configures Google Cloud client + exact callback and private host credentials. Live status is enabled:true (2026-10-02).
- [ ] Actual Google signup/repeat login, authenticated product upload/customer edit; automation does not prove these.
- [ ] Verify/update customer Magic Link/FavoriteShare canonical root fragment paths against private environment; source historical defaults still /t/27.
- [ ] Complete deferred mobile/tablet and broad multilingual/theme visual acceptance.

# Armaghan — Product Backlog

## Test29 nonvisual release — current

- [x] Read actual state; preserve completed backend work.
- [x] Acquire shared lease and create snapshot/test28-final.
- [x] Freeze 01–28; promote build/deploy/launcher/runtime markers to Test29.
- [x] Isolate browser writes under armaghan:test29:*, including manual locale.
- [x] Localize Why Armaghan defaults for fa/ar/ku; retain en and override priority.
- [x] Add automated locale isolation/reload/default tests; carry dark contrast check.
- [x] FTP #328 / run 37003816665: 44 Vue tests, build, scoped deploy and published index/JS/CSS + first launcher link verified PASS; no visual acceptance inferred.
- [ ] Visual/device and authenticated-admin acceptance deferred by owner instruction; never infer PASS from automation.


## Test28 live QA follow-up — 2026-10-02

- [x] Localize the Persian Home eyebrow in Test29 source; frozen Test28 retains its historical text. Broad visual Home language acceptance remains open.

- [x] Confirm completed core capabilities from canonical repository state; no reimplementation.
- [x] Live desktop guest checks: 18 products, search 1 result, category filter 6 results, detail open/close, four locale directions, guest favorites reload, invalid-share state.
- [x] Measure dark-title defect: 4.279:1 on card surface; select approved mint semantic default; numeric check fails before and passes after repair.
- [x] Confirm this run's guarded build/deploy and fresh live dark-title color; see CURRENT-STATUS/HANDOFF section 45 evidence.
- [ ] Complete mobile/tablet acceptance and broader language/theme matrix; existing broad final QA tasks remain open.
- [ ] Authenticated editor save/reload/publish/restore, real product upload and customer Magic Link remain untested in this browser (login form, no session).


## Completed — bounded operational-memory reconciliation — 2026-10-02

- [x] Reconcile current main, latest handoff, backlog, Test28 hardening audit and shared lock.
- [x] Correct CURRENT-STATUS next-run instructions: Tests 01–27 frozen; Test28 active; backup/restore hardening already complete.
- [x] Correct HANDOFF opening phase pointer and record ranked method + exact remaining acceptance evidence (section 44).
- [x] Keep all final live QA tasks open; no inferred browser acceptance and no UI/backend/deployment changes.


## P0 — Final end-to-end QA + delivery handoff — next

- [ ] Validate Test29 mobile/tablet/desktop layout and interaction when visual testing is resumed.
- [ ] Validate Persian/Arabic/Sorani RTL and English LTR.
- [ ] Validate light/dark contrast and customer navigation.
- [ ] Validate catalog/product media fallback, customer session/Magic Link, FavoriteShare/WhatsApp and admin permission boundaries.
- [ ] Validate deployment lanes and backup/restore evidence in final delivery notes.
- [ ] Perform opportunistic authenticated-admin browser acceptance only if a valid real Filament session is available; never weaken auth.
- [ ] Produce final delivery/handoff status and close nonessential prototype/demo paths or clearly label them.

## P0 — Test 28 + off-host SQLite backup hardening — 2026-10-02

- [x] Select backup architecture: production `VACUUM INTO` snapshot → AES-256-GCM encryption before transfer → encrypted GitHub Actions artifact → isolated artifact-download restore drill.
- [x] Refuse plaintext SQLite artifacts because the repository is public.
- [x] Prefer dedicated `ARMAGHAN_BACKUP_PASSPHRASE`; until it exists, derive a domain-separated key from the existing GitHub-held FTP secret without logging/persisting the raw secret.
- [x] Add daily scheduled backup at 01:23 UTC with 14-day artifact retention and no remote plaintext export.
- [x] Add restore drill from the uploaded/downloaded artifact: ciphertext hash, plaintext hash, `PRAGMA integrity_check`, `foreign_key_check`, table inventory/counts and write-lock rollback.
- [x] Freeze Test 27 and promote source/runtime namespace to Test 28.
- [x] Set active build/deploy lane to `/t/28`; make both Vite and FTP deployment reject frozen Test 27.
- [x] Add Test28 visual-editor/customer-auth/FavoriteShare source contracts and a Test27 freeze/promotion contract.
- [x] Backend CI #53/#54 pre-merge and #55 post-merge PASS.
- [x] First real SQLite Off-host Backup #1 PASS; encrypted artifact `armaghan-sqlite-backup-36974268704`, artifact ID `11212796186`, size 337,663 bytes, retention through 2026-10-16.
- [x] Artifact round-trip restore drill PASS: ciphertext/plaintext checksums, SQLite integrity, foreign keys, table inventory/counts and write-transaction rollback verified; plaintext restore not retained.
- [x] FTP #318 stopped before deployment on stale historical Test26 assertion; corrected contract only.
- [x] FTP #319 stopped before deployment on two stale Test27 session-test fixtures; corrected fixtures only.
- [x] FTP #320 PASS: all frozen/Test28/backup contracts + TypeScript + 41 unit tests + build + FTP smoke; 112 Test28 files uploaded; root skipped.
- [x] Independently verify Test28 live, Test27 preserved, launcher Test28-first.
- [x] Reconcile `CURRENT-STATUS`, `HANDOFF`, `BACKLOG`, rules and release shared lock.
- [ ] P1 operational improvement: add dedicated `ARMAGHAN_BACKUP_PASSPHRASE`; retain recovery ability for fallback-encrypted artifacts until their 14-day expiry.

## P0 — Persisted FavoriteShare + WhatsApp — 2026-10-02

- [x] Select architecture: Backend FavoriteShare record + SHA-256 token hash + ordered product pivot + fragment-held share token + fixed POST resolve + WhatsApp handoff.
- [x] Add bounded issue/resolve/revoke service with active-product/taxonomy validation, ordered products, guest/customer origin, expiry and audit logging.
- [x] Use shorter guest TTL (7 days) than authenticated-customer TTL (30 days); guest shares intentionally rely on expiry while customer-owned shares have authenticated revoke.
- [x] Add CSRF-protected/rate-limited fixed POST issue + resolve endpoints and authenticated customer revoke endpoint.
- [x] Keep raw share token out of SQLite/logs/query strings/server paths; only the browser fragment carries it.
- [x] Replace Test27 product-code-in-URL generation with Backend persisted shares and `/favorites/share/:token`; retain old `?shared=v1:` links read-only for compatibility only.
- [x] Add one-tap WhatsApp handoff using the server-backed share URL and existing seller WhatsApp path.
- [x] Fix Favorites customer attribution to use the real `currentCustomerId`, never hard-coded Customer #1.
- [x] Add Backend lifecycle tests + frontend unit/source contracts for ordering, expiry, revoke, ownership, malformed tokens and shared-page resolution.
- [x] PR #9 Backend CI #51 PASS; squash merge `76f80179292f743cb54443e540602bba47c4d8bb`; Backend CI #52 PASS.
- [x] Backend Code Deploy #11 PASS with pre-swap snapshot/no drift/all existing smokes + FavoriteShare resolve GET 405; independent Production probe reconfirmed issue/resolve GET 405.
- [x] FTP Deploy #316 PASS: Test27 FavoriteShare contract + TypeScript/Vue/build/smoke/deploy PASS; root/Test26 untouched.
- [x] Live read-only invalid-token browser acceptance PASS: persisted share route rendered invalid/unavailable state without crash or legacy code-sharing.
- [x] Reconcile CURRENT-STATUS/HANDOFF/BACKLOG/rules and release shared lock.

## P0 — Customer session + Magic Link — 2026-10-02

- [x] Select architecture: same-origin Laravel session + database-backed high-entropy hashed one-time Magic Link; no customer password dependency and no new auth package.
- [x] Add bounded issue/revoke/consume service with expiry, scope, one-time atomic consume and audit logging.
- [x] Add customer-session middleware + `GET/PATCH /api/customer/session` + scoped logout.
- [x] Add active-admin issue/revoke API and native Filament Customer actions for 24h/72h/7d links.
- [x] Rate-limit public consume/admin issue endpoints; store only SHA-256 token hashes and never persist plaintext tokens.
- [x] Wire Test 27 to hydrate real Backend customer sessions while retaining demo/local fallback only when no server session exists.
- [x] Persist the Backend-supported customer profile subset from the customer dashboard; internal notes/user ownership stay server-internal.
- [x] Remove browser-issued Magic Link UI and explain that secure links are issued from the Backend.
- [x] Add feature/unit/source-contract coverage for issue, expiry, revoke, replay prevention, scoped session behavior, authorization and Test 27 hydration.
- [x] Backend Code Deploy #8 PASS for the initial live core.
- [x] Backend Code Deploy #9 detected production token-in-path 404 and automatically rolled back; rollback/health/cleanup PASS.
- [x] Repair the production route with browser-fragment token + fixed CSRF-protected POST consume endpoint; PR #8 merge `10d040c7...`.
- [x] Backend CI #49/#50 PASS; Backend Code Deploy #10 PASS with guest session 401 and fixed consume endpoint GET 405.
- [x] FTP Deploy #314 PASS; Test 27 auth/session regression contract + TypeScript/Vue/build/smoke/deploy PASS; root skipped.
- [ ] Opportunistic browser acceptance when a valid real admin session is available: issue one real customer Magic Link in Filament, open it in a fresh browser, verify session/profile update/replay rejection/logout.
- [x] Reconcile `CURRENT-STATUS`, `HANDOFF`, `BACKLOG` and release the shared lock.

Priority: **P0 current**, P1 next, P2 later.  
Current implementation targets: **Backend MVP productionization** + **Test 27 visual-editor/UI lane**. Test 26 is frozen.
Detailed ranked UX decisions: `docs/TEST26-UX-AUDIT.md`.

## P0 — Production product media + additive backend deploy — 2026-10-02

- [x] Select official Spatie Media Library + official Filament integration; lock dependencies through Composer, not hand-written package versions.
- [x] Add guarded additive Backend deployment lane: existing migrations byte-identical, new migration create-only, pre-migration SQLite snapshot, restore-on-migration-failure, code/public rollback and HTTP smoke.
- [x] Route Composer/migration drift away from the code-only updater; verify ordinary code-only lane remains green.
- [x] Add Product `product-gallery` ownership on public disk with paths under `media/products/<media-id>/`.
- [x] Limit uploads to JPEG/PNG/WebP, 8 MiB, 5000×5000, maximum 6 ordered files.
- [x] Re-decode and re-encode stored originals to strip metadata; normalize JPEG orientation.
- [x] Generate synchronous `card`/`thumb` conversions without destructive crop or upscaling.
- [x] Add official Filament multi-upload/reorder UI and product-list thumbnail.
- [x] Eager-load media in Public Catalog API and expose ordered `media` URLs.
- [x] Make Test 27 prefer the server gallery when present and retain local fallback only when server media is empty/API unavailable.
- [x] Backend CI #41 pre-merge and #42 post-merge PASS.
- [x] Backend Code Deploy #7 correctly skipped deployment on dependency/migration drift.
- [x] Backend Additive Deploy #3 PASS: snapshot, dependency change, 1 additive migration, public storage link, all HTTP smokes and cleanup PASS.
- [x] FTP Deploy #307 stopped before deployment on an external-URL unit-test fixture; no customer UI mutation occurred.
- [x] Same-origin fixture fix `b3722d3f...`; FTP Deploy #308 PASS with full Test27 build/smoke/deploy; root skipped.
- [x] Live API independently verified: all 18 Products return a `media` field; current arrays are empty until real admin uploads occur, so Test 27 fallback remains intact.
- [ ] Opportunistic acceptance when a real admin session is available: upload/reorder one real product image in Filament and verify it appears in Test 27 after reload.
- [ ] Next P0: minimum customer API/session + Magic Link core.

## P0 — Public catalog API + production bootstrap — 2026-10-02

- [x] Add read-only Public Catalog API Resources/Controller for active categories and products.
- [x] Add bounded product filtering/search/pagination and API throttling.
- [x] Return managed-code metadata so an intentionally inactive Backend row suppresses stale local fallback data.
- [x] Add Test 27 Hybrid Sync: Backend-managed product/taxonomy data overlays local state; Backend failure keeps the existing customer page usable.
- [x] Preserve local product media/specs during staged sync; Backend domain data owns names/status/active state.
- [x] Backend CI #35 PASS; FTP Deploy #295 PASS with TypeScript/unit/Test 27 production build and `/t/27` deployment; root skipped.
- [x] Backend Code Deploy #4 PASS; public catalog category/product endpoints both HTTP 200.
- [x] Add guarded `armaghan:bootstrap-catalog` command with dry-run default, exact empty-table precondition, transaction and 3/6/18 count contract.
- [x] Backend CI #37 + Backend Code Deploy #5 PASS.
- [x] Catalog Bootstrap Production #1 PASS: before 0/0/0 → consistent SQLite snapshot → after 3/6/18; representative category/subcategory/product codes verified; temporary helper cleanup PASS.
- [x] Independently re-read live API: 3 managed categories, 6 managed subcategories, 18 managed products.
- [x] Independently render live Test 27 Products page after bootstrap: 18 products displayed with customer-facing subcategory/unavailable presentation intact.
- [x] Remove one-shot production bootstrap workflow after successful initialization; reusable helper remains fail-closed because production catalog is no longer empty.
- [x] Production product-media ownership/upload capability completed and wired to Test 27; customer API/session is now the next P0.

## P0 — Test 27 customer visual corrections — 2026-10-01

- [x] Create rollback checkpoint `rollback/test27-pre-customer-ui-corrections`.
- [x] Make Footer brand area logo-only; remove customer-facing brand name/description beside the logo.
- [x] Centralize customer product labels: available → localized six-way subcategory; non-available/made-to-order → unified unavailable/producible label.
- [x] Keep canonical `Product.name` data intact for backend/admin ownership.
- [x] Restyle product codes as high-contrast Brand Blue/White capsules with restrained Brand Gold border.
- [x] Add persisted `showSubcategoryCodes` Feature Flag; defaults OFF on mobile/tablet/desktop; preserve schema-v4 choices during migration.
- [x] Default Products page to Brand White and product cards to Brand Mint in light mode; preserve dark-mode surfaces.
- [x] Register `products.page` and existing `product.card` as structured visual-editor surfaces.
- [x] Apply strong brand-derived green heading role on light content surfaces without reducing contrast on dark/image/brand-chrome surfaces.
- [x] Make Why Armaghan dividers 3px Brand Blue and number circles Brand Blue with Brand Gold numbers.
- [x] Add unit/source-contract coverage for presentation helper, schema migration, feature flag, editor targets and semantic CSS roles.
- [x] Confirm main CI/build/FTP deploy of Test 27 and record the workflow run — FTP Deploy #277 PASS; 112 files uploaded to `/public_html/t/27` + launcher; no remote delete; root deploy skipped.

### Test 27 customer visual corrections delivery record

- [x] Main implementation commit: `972d66bcd6e0e3ab72946d2fd14ffe8a6d329052`.
- [x] FTP Deploy #275 stopped before build/deploy because the historical Test 23 contract still asserted the old current-source title expression; no remote mutation occurred in that failed run.
- [x] Historical Test 23 contract was future-proofed in `fb7ee2a1c4200f8080c68e37e10edb238cda9833`; FTP Deploy #276 QA path PASS.
- [x] Validated release marker: `65b9f1158019aece79438fbc680af4070e0c0665`.
- [x] **FTP Deploy #277: SUCCESS** — immutable guard and Test 11–27 contracts PASS; TypeScript, Vue unit tests, generated portrait media and Test 27 production build PASS; FTP smoke PASS; `deploy-t` PASS; `deploy-root` skipped.
- [x] Deployment uploaded **112 files** to active Test 27 plus mutable launcher and explicitly deleted **0 remote files**.
- [x] Test 26 and earlier frozen snapshots remained untouched.
- [x] Final heading-green polish commit: `13b3dbb5abd32265f752139b9cf670127a8ce5c9`; **FTP Deploy #282 PASS** with the full Test 27 QA/build/smoke/deploy lane. Strong light-surface headings now resolve to a visibly dark-green semantic role rather than the earlier blue-leaning mix.

Image requirements: `docs/TEST26-IMAGE-REQUIREMENTS.md`.
Customer clarification script: `docs/TEST26-CUSTOMER-QUESTIONS.md`.
Rollback checkpoint: `rollback/test25-pre-test26` → `328f6c48ea9c4f1346702282ddb6d21ae1685a16`.


## P0 — Frozen Test 26 / CI release-lane repair

Detailed ranked analysis: `docs/CI-FREEZE-REPAIR.md`.

- [x] Add Test 26 to the immutable-test registry.
- [x] Convert the historical Test 22 contract from current-source assumptions to historical release invariants.
- [x] Future-proof historical Test 20/21/23/24/25 contracts so they no longer enumerate the current build target or stop at Test 26 namespaces.
- [x] Convert the Test 26 source contract into a frozen-handoff contract that no longer blocks Test 27+ source evolution.
- [x] Remove active CI build/deploy paths that can regenerate `/t/26` from main.
- [x] Set Test 27 as the next explicit CI UI release lane while keeping the launcher unchanged until a real Test 27 UI change is approved.
- [x] Make local Vite builds write to `.build/frontend` unless an unfrozen numbered target is explicitly supplied.
- [x] Exclude Vite-config-only maintenance from automatic numbered-test deployment; explicit workflow dispatch remains available.
- [x] Propagate terminology rules 75–77 into root `AGENTS.md`.
- [x] Confirm the repair commit's GitHub Actions run is green before starting Backend MVP mutations.

### CI/freeze repair delivery record

- [x] Final repair head: `8065ed94aee9f603217e4131ca0460b55aac414d`.
- [x] GitHub Actions **FTP Deploy Run #244: SUCCESS**.
- [x] All QA contracts passed, including future-proofed Test 20–25 historical contracts and the frozen Test 26 handoff contract.
- [x] FTP smoke test passed.
- [x] `deploy-t` and `deploy-root` were both skipped; Test 26 remained untouched and Test 27 was not published.
- [x] Runs #241–#243 exposed brittle historical assertions during the repair; all failed before deployment and were used only as feedback to harden the contracts.

## P0 — Backend hosting preflight

- [x] Verify PHP >= 8.3 and Laravel-required PHP extensions — PHP 8.3.33; all required extensions PASS.
- [x] Verify safe web/private layout — `public_html` is the active document root and PHP can read/write a private sibling outside it.
- [x] Verify HTTPS and deployment capabilities — HTTPS PASS; CI-built Composer/vendor path selected; web shell functions are disabled. Original 2026-09-30 probe had PDO SQLite unavailable and PDO MySQL available.
- [x] Owner reports PDO SQLite was enabled on 2026-10-01; architecture switched back to SQLite primary.
- [ ] Re-run production SQLite probe immediately before first production migration: confirm `pdo_sqlite`, private DB path write, foreign keys and backup path write.
- [ ] **Production SQLite Reprobe #1: INCONCLUSIVE** — FTP/private sibling/cleanup PASS, but the temporary PHP probe could not be reached through a canonical web URL. Do not treat this as SQLite FAIL or PASS. Retry later using the confirmed canonical application URL or deployed backend health route.

## P0 — Laravel 13 / Filament 5 bootstrap

- [x] Generate Laravel through Composer on an isolated bootstrap branch rather than hand-writing framework files.
- [x] Install Laravel Framework **13.34.0** under `platform/backend`.
- [x] Install Filament **5.9.0** Panel Builder and generate `AdminPanelProvider`.
- [x] Preserve SQLite as local/dev/test default and add a production MySQL/MariaDB environment template with no credentials.
- [x] Verify Laravel `/up` health route.
- [x] Run bootstrap Laravel tests: 2 passed / 2 assertions.
- [x] Run locked Composer security audit: no known vulnerability advisories.
- [x] Add permanent Backend CI for Composer validation, migrations, tests, version checks, audit and secret hygiene.
- [x] Replace generated nested agent instructions with Armaghan project rules; do not auto-install Laravel Boost.
- [x] Confirm no Test 26/Test 27/launcher mutation during bootstrap.
- [x] Build the Armaghan core domain migrations/models from the approved schema draft, adapted portably for SQLite dev/test + MySQL/MariaDB production.
- [x] Add production-safe Filament access gate: only active users with admin role can enter the admin panel; no repository credential is seeded.
- [x] Add secret-free-in-repo first-administrator provisioning: fail-closed Artisan command + encrypted one-shot production provisioner; no seeded/default password.



### Domain foundation delivery record

- [x] Customer, Category, Subcategory, Product, SpecDefinition, ProductSpecValue, FavoriteShare, MagicLink and ActivityLog foundations added.
- [x] Favorites-share tokens and magic-link tokens are hash-only database values.
- [x] Product media table intentionally deferred to Spatie Media Library to avoid duplicate media ownership.
- [x] Orders/timeline remain deferred unless delivery requires them.
- [x] DatabaseSeeder no longer creates a default fixed user.
- [x] Backend CI Run #3: **PASS** — 4 tests / 23 assertions; Composer audit clean.
- [x] No production MySQL migration and no Test 26/Test 27 change occurred.
- [x] Filament Product/Customer/Category/Subcategory Resources are implemented, CI-verified and live in production. First-admin provisioning remains complete.

- [x] First bounded Filament CRUD batch: Category + Subcategory Resources — commit `4281034656ead0c0538b4f1dc4e607ea5f161438`; Backend CI #31 PASS.
  - create/edit/list/search/sort/filter implemented;
  - parent Category relation uses Filament relationship select;
  - destructive delete actions intentionally omitted because Category → Subcategory cascades;
  - active-admin access tests and non-admin denial tests PASS.
- [x] Taxonomy Resources activated in production through guarded Backend Code Deploy #2; health/login/categories/subcategories HTTP smoke all 200.
- [x] Product + Customer Resources implemented in commit `de450a15c339cb8a3460c7c3f7f45d01a7074158`; Backend CI #33 PASS.
- [x] Product + Customer Resources activated through Backend Code Deploy #3; health/login/categories/subcategories/products/customers HTTP smoke all 200.
- [x] Guarded code-only backend updater production-proven: Composer/migration drift refusal, SQLite pre-swap snapshot, staged code swap, HTTP smoke, automatic rollback and temp cleanup.
- [x] Backend Code Deploy #1 exposed a public-directory permission regression and automatically rolled back successfully; fix `f6c997ae9496a0cc48ff8be6a1e190278d02e115` normalized public root to 0755 before successful Runs #2/#3.
- [ ] Add a migration-aware update variant only when a future release actually changes migrations or dependencies; the current code-only lane intentionally fails closed on such drift.




## P0 — SQLite-first persistence

Canonical architecture: `docs/SQLITE-FIRST-PERSISTENCE.md`.

- [x] Select SQLite as the primary Laravel database for local/dev/test/production after host SQLite enablement.
- [x] Remove the superseded custom JSON runtime-store layer before any production data used it.
- [x] Keep JSON as ordinary import/export/fixture interchange only.
- [x] Add `armaghan:backup-sqlite` using SQLite `VACUUM INTO` for consistent private snapshots.
- [x] Backend CI Run #17: PASS after SQLite-primary pivot and backup command tests.
- [x] FTP Deploy Run #258: PASS; only `/public_html/t/index.htm` uploaded; no remote files deleted; root deployment skipped.
- [x] Add backup command tests.
- [x] Make production example use an absolute private SQLite path and private backup path.
- [x] Re-probe production `pdo_sqlite` and private-path write access before first live migration — PDO SQLite, SQLite 3.53.4, private file R/W, foreign keys and `VACUUM INTO` all PASS.
- [x] Configure the real production SQLite file path in the host-only shared `.env`; the path remains private and is not stored in Git.
- [ ] Configure backup retention + at least one off-host rotated copy. Production code deploys and catalog bootstrap already create consistent on-host SQLite snapshots.
- [ ] Add optional MySQL logical mirror/export only after the SQLite production path is stable; never dual-write in live requests.
- [ ] Add mirror verification (row counts/checksums + restore drill) when MySQL mirror is implemented.

## Delivery roadmap — remaining ~6 core runs

Current estimate excludes open-ended new customer redesign requests. Filament CRUD/media may split into two bounded runs, making the practical range about 6–7 runs.

1. [x] **Urgent Test 27 editor access** — persistent admin quick-launch button, structured target browser, direct visibility toggles, live Test 27 deployment.
2. [x] **Visual Style Profile frontend + production service foundation** — local-first adapter, checksum conflict handling, staging Publish/history/Restore UI, production Laravel/SQLite deployment and live service-level persistence acceptance all PASS.
3. [x] **Production SQLite/Laravel + first admin** — PDO SQLite/SQLite 3.53.4 PASS, private DB/backup active, migrations/snapshot PASS, `/backend` healthy, one real active production admin provisioned securely.
4. [ ] **Browser-authenticated editor acceptance** — real Filament session + CSRF, Test 27 server autosave, reload/cross-device draft, staging Publish and Restore through the actual UI. This is the only remaining Style Profile persistence acceptance.
5. [~] **Filament CRUD + catalog/customer API/media** — Product/Category/Subcategory/Customer Resources and public catalog reads are complete/live; production product-media ownership/upload and customer API/session wiring remain.
6. [x] **Favorites/WhatsApp + Customer Magic Link** — Customer session/Magic Link and persisted FavoriteShare/WhatsApp handoff are live with hash-only tokens, expiry/revoke, ordered products and audited/rate-limited endpoints.
7. [ ] **Production hardening + final QA/handoff** — rotated off-host SQLite backup + restore drill, repeatable backend update workflow with pre-migration snapshot/rollback guard, responsive/RTL/LTR/light/dark/permissions QA, final customer handoff.

### Production backend activation delivery record

- [x] Fresh production hosting probe: PASS.
- [x] Build locked Laravel 13.34.0 + Filament 5 release in CI with production Composer dependencies.
- [x] Generate and preserve APP_KEY only on the host; no APP_KEY or production `.env` entered GitHub or logs.
- [x] Create private production SQLite database and private backup directory outside `public_html`.
- [x] Run production migrations successfully.
- [x] Create first consistent SQLite snapshot.
- [x] Verify core schema: users/products/customers/style profile tables.
- [x] Expose only Laravel `public` surface under `/backend`.
- [x] Repair public permissions to LiteSpeed-safe `0755/0644`; private state remains private.
- [x] HTTP smoke PASS: backend root, health, public Style Profile API and Filament login page.
- [x] Provision the first real active admin through a one-time secure bootstrap flow; no seeded/default password and no plaintext credential stored in Git/repo logs.
- [x] Point Test 27 Style Profile API base to `/backend`; production SQLite Style Profile save/publish/restore semantics verified transactionally on the live database with full rollback.
- [ ] Complete the final browser-authenticated acceptance cycle: real Filament session + CSRF, edit Test 27, reload/cross-device, staging Publish and Restore.
- [ ] Configure rotated off-host SQLite backup copy + restore drill.
- [x] Add safe repeatable **code-only** backend release/update workflow with SQLite pre-swap snapshot, fail-closed migration/dependency drift detection, HTTP smoke and automatic code rollback. Migration-aware releases remain intentionally separate.

## P0 — Test 27 color/saveability customer request

- [x] Derive the canonical blue from the actual repository logo rather than guessing. Pixel probe result: image-dominant logo background `#0714C2`.
- [x] Replace canonical Brand Blue `#151EDA` with logo-background blue `#0714C2` in Test 27 semantic tokens/default balanced palette.
- [x] Add semantic Home page background role; light-mode default = Brand White.
- [x] Make the whole Home page background a registered editor target (`home.page`) with background-color control.
- [x] Add `home.content` separately so Home content can be controlled without hiding the page root.
- [x] Protect `home.page` from Hide to prevent an unrecoverable/blank editing state.
- [x] Add semantic panel-background role; default primary Home panels = Brand Mint.
- [x] Apply Mint panel default to About copy, Why list and Capability cards while retaining token-based per-target overrides.
- [x] Expand nested editable targets for Why items and Capability card titles/texts.
- [x] Recover hidden dynamic/unregistered targets through the structured target browser.
- [x] Restrict Inspector controls per target; e.g. page root exposes only background color.
- [x] Default Test 27 Style Profile API base to same-origin `/backend`.
- [x] Add CSRF token bootstrap/retry for authenticated backend Style Profile writes.
- [x] Add sanitized Style Profile JSON export/import as a simple portable backup/transfer mechanism; do not revive JSON runtime persistence.
- [x] Branch CI: latest Test 27 Editor Color Saveability runs PASS.
- [x] Backend CI Run #19: **PASS** after adding the CSRF bootstrap endpoint and Style Profile API test coverage.
- [x] Historical Test 24/25 color contracts made token-value agnostic so a customer-approved Brand Blue value change does not regress frozen semantic contracts.
- [x] **FTP Deploy Run #268: PASS** — full QA/build/smoke passed; active Test 27 rebuilt and uploaded to `/public_html/t/27`; 112 files uploaded; no remote files deleted; root deploy skipped.
- [x] Provision the first real Laravel administrator without a seeded/default password; account `admin@armaghan.local` is active in production.
- [ ] Log into the real backend admin session and verify Test 27 draft autosave through the browser; **server-side production persistence itself is already acceptance-tested PASS**.
- [ ] Verify cross-device reload from shared draft, staging Publish and Restore end-to-end.
- [ ] After successful shared persistence QA, decide whether JSON export/import remains visible by default or moves under an advanced/backup disclosure.


### First-admin provisioning delivery record

- [x] Backend CI: secure provisioning command/tests, PHP helper syntax, dependency audit and secret hygiene PASS.
- [x] First bootstrap attempt created the intended admin but failed before credential handoff because the temporary RSA public key was malformed; no plaintext credential was logged.
- [x] Inspector run confirmed the only active admin exactly matched the bootstrap identity and timestamp: `admin@armaghan.local`, `Armaghan Administrator`, created at 2026-10-01T13:35:31Z.
- [x] Guarded recovery rotated only that exact account using email + name + creation-time proof.
- [x] Recovery generated the password on-host, encrypted it before mutation, stored only the Laravel hash, and returned only RSA ciphertext through CI.
- [x] Active-admin policy and password hash verification PASS; `/backend/admin/login` HTTP smoke PASS.
- [x] One-shot provisioning workflow removed after success.
- [x] Final main validation: **Backend CI #30 PASS**.
- [x] **FTP Deploy #270 PASS**; this backend/security batch did not modify or redeploy Test 26/27 UI.
- [ ] Acceptance remaining: authenticate in the real backend session from the browser and verify Test 27 shared Style Profile save/reload/publish/restore end-to-end.

### Production Style Profile acceptance delivery record

- [x] Add reusable token-protected FTP/HTTP acceptance helper: `platform/scripts/verify_style_profile_ftp.py`.
- [x] Helper never creates an authenticated browser session and contains no auth bypass.
- [x] Run the acceptance sequence against the **live production SQLite database** inside one outer transaction.
- [x] Draft save PASS.
- [x] Consecutive staging Publish sequence PASS.
- [x] Restore creates a newer immutable version PASS.
- [x] Staging publication pointer follows the restored version PASS.
- [x] Inside the transaction: **3 immutable versions + 3 ActivityLog records** created as expected.
- [x] Outer rollback PASS; logical database state after the test exactly matched the pre-test state.
- [x] Temporary public helper cleanup PASS.
- [x] Production Style Profile Acceptance Run `36878931948`: PASS.
- [x] Main post-merge validation: **FTP Deploy #272 PASS**; QA/smoke passed and both UI/root deploy jobs were skipped.
- [ ] Only remaining editor persistence acceptance: real browser Filament login/session + CSRF + cross-device UI cycle.


## P0 — Test 27 visual style editor foundation

Detailed architecture and ranked decisions: `docs/TEST27-VISUAL-EDITOR.md`.

- [x] Create rollback checkpoint `rollback/test26-pre-test27-visual-editor` before the first Test 27 UI change.
- [x] Select a non-modal resizable Bottom Sheet as the primary mobile inspector; reject a fully draggable floating inspector as the main mobile UI.
- [x] Add approved five-color token registry. Current canonical Test 27 palette: `#21946A`, logo-background Brand Blue `#0714C2`, `#C8E3DB`, `#FFFFFF`, `#FFB514`; the earlier `#151EDA` blue is retired.
- [x] Route shared navy/blue brand chrome for Header, Footer and Hero text bar through `--role-brand-chrome`.
- [x] Add admin-only Visual Editor launcher under Appearance; editor is off by default.
- [x] Keep the editor UI/listeners removable while persisted style/content overrides continue through a separate runtime layer.
- [x] Add touch-neighborhood selection with `document.elementsFromPoint()` and an explicit candidate chooser for nested/nearby elements.
- [x] Add text editing for explicitly registered text targets, stored per locale.
- [x] Add approved-token text/background/border controls, hide/show and per-element reset.
- [x] Keep hidden targets recoverable from a dedicated hidden-elements list.
- [x] Persist a structured style profile plus generated CSS; do not accept arbitrary CSS/HTML/JS input.
- [x] Isolate Test 27 browser state from Test 26 by moving mutable prototype keys to `armaghan:test27:*`.
- [x] Add permanent `tests/test_test27_visual_editor_contract.py`.
- [x] Staged branch validation PASS: source contract, TypeScript, 25 unit tests and Test 27 Vite build.
- [x] Expand stable editable-target coverage across Home/About/Why/Capabilities/product banners plus major card/panel surfaces; keep domain-owned product/customer values outside free-form visual text.
- [x] Add WCAG-normal-text contrast guard at 4.5:1 for explicit token text/background pairs, with blocked unsafe assignments and visible feedback.
- [x] Persist/version Style Profiles through Laravel with mutable draft, immutable versions, staging/production publication pointers, admin-only writes, server-side safe CSS compilation, activity log and checksum conflict protection.
- [x] Connect the Test 27 visual-editor store to the Laravel Style Profile API through a local-first adapter: conservative public staging baseline, 900ms debounced authenticated draft autosave, expected-checksum 409 conflict handling, staging Publish, history/Restore and local fallback.
- [x] Visual editor structured target controls foundation: grouped browser for header/hero/about/why/capabilities/product banners/product cards/footer, direct ON/OFF visibility, text/background/border/token editing through the existing inspector, and hidden targets recoverable.
- [x] Add sanitized profile JSON Export/Import plus backend immutable version-history/Restore UX; keep JSON as interchange/backup only, not runtime persistence.

### Style Profile frontend adapter delivery record

- [x] Add same-origin API client with cookie/session credentials and optional XSRF header support; no auth bypass.
- [x] Public staging profile is adopted only when the local browser has no edits or still matches its previous server baseline; local work is never silently overwritten.
- [x] Admin sync checks the real Laravel admin endpoint; 401/403 or API absence falls back to browser-local persistence without disabling the editor.
- [x] Draft changes debounce for 900ms and use `expected_checksum`; HTTP 409 becomes an explicit local-vs-server choice.
- [x] Add staging Publish and version-history Restore controls; restore remains create-new-version semantics from the backend.
- [x] Add sync status UI: checking / local / saving / synced / conflict / error.
- [x] Branch validation PASS: Test 27 contract, TypeScript, **30/30** Vue unit tests, numbered Test 27 build.
- [x] **FTP Deploy Run #262: PASS** — active Test 27 rebuilt and deployed to `/public_html/t/27`; 112 files uploaded; no remote files deleted; root deploy skipped.
- [ ] Complete the only remaining Style Profile acceptance: real browser Filament login/session + CSRF, edit/save in Test 27, reload, verify cross-device draft, Publish staging and Restore. Production server-side save/version/restore semantics are already acceptance-tested PASS.

- [x] Add Test 27 to the mutable test launcher after explicit owner approval on 2026-10-01; keep Test 26 frozen.
- [x] Add persistent admin-only «ویرایش ظاهر» quick launcher so a logged-in admin can open the Test 27 editor from any page and be routed to Home automatically.
- [x] Add grouped element browser inside the bottom sheet so nearby/nested elements do not need precise finger selection.
- [x] Branch-only urgent validation PASS: visual-editor contract, TypeScript, unit tests and Test 27 build.
- [x] **FTP Deploy Run #260 attempt 2: PASS** — Test 27 rebuilt and deployed to `/public_html/t/27`; mutable test index updated; 112 files uploaded; no remote files deleted; root deploy skipped.
- [x] Run #260 attempt 1 stopped before build/deploy only because the GitHub runner timed out downloading Pillow; failed jobs were retried and the complete pipeline then passed.

### Style Profile backend delivery record

- [x] Architecture/validation record: `docs/STYLE-PROFILE-BACKEND.md`.
- [x] Add `style_profiles`, `style_profile_versions`, `style_profile_publications` migrations/models.
- [x] Public read endpoint for staging/production publications.
- [x] Admin-only draft/save/publish/restore endpoints using the persisted active-admin gate.
- [x] Reject arbitrary CSS and unknown payload keys; compile CSS only from approved palette tokens.
- [x] Add optimistic checksum conflict response (HTTP 409) to prevent stale-editor overwrite.
- [x] Restore historical versions by creating a new immutable version; do not rewrite history.
- [x] Fix pre-existing ActivityLog model/table mismatch without rewriting historical migration.
- [x] Backend CI Run #10: **PASS** — 12 tests / 84 assertions; Composer audit clean; secret hygiene PASS.
- [ ] Next bounded batch: Vue/Test 27 persistence adapter + staging Publish/history/Restore UX.



### Test 26 Run 8 — footer scope + mobile language + terminology rule

- [x] Rollback checkpoint: `rollback/test26-pre-footer-language-terminology` → `a00666040922d248d3e3e5d68490d26b797ef339`.
- [x] Mobile Footer is shown only on Home; tablet/desktop retain the current Footer behavior on inner pages.
- [x] Existing `showFooter` Admin setting remains the top-level visibility control.
- [x] Mobile compact header now shows the language selector in the blue top bar when `showLanguage` is enabled.
- [x] Mobile drawer suppresses the duplicate language selector; tablet/desktop behavior remains unchanged.
- [x] Permanent communication rules 75–77 added: first use of each specialist term in user-facing project reports must include a short Persian explanation in parentheses.
- [x] Feature Flag vs hard-coded terminology distinction recorded in the audit and project rules.
- [x] Ranked five-option implementation analysis recorded in `docs/TEST26-UX-AUDIT.md` Run 8.
- [x] Final Test 26 QA/build, FTP smoke and scoped deploy passed.

### Test 26 Run 8 delivery record

- [x] Rollback checkpoint: `rollback/test26-pre-footer-language-terminology` → `a00666040922d248d3e3e5d68490d26b797ef339`.
- [x] Release head: `5a7ca47291b64bd70cf1fed2b4a32f94e9753798`.
- [x] **FTP Deploy Run #234: SUCCESS** — Test 11–26 contracts, TypeScript, unit tests, Vite Test 26 build and FTP smoke passed; scoped `deploy-t` passed; `deploy-root` skipped.
- [x] Mobile Footer is Home-only; non-mobile Footer behavior remains unchanged.
- [x] Mobile top bar exposes the language selector; mobile drawer does not duplicate it.
- [x] Permanent terminology-explanation rules 75–77 are active in `docs/PROJECT-RULES.md`.
- [x] Build marker / launcher query: `test26-footer-language-r8a`.
- [x] Test 25 and earlier snapshots remain unchanged.

### Test 26 Run 7 — mobile UX polish delivery

- [x] Rollback checkpoint: `rollback/test26-pre-mobile-ux-polish` → `54e2075b67ae1c21b0bac50fe32b9cd1e5144864`.
- [x] Header Mode and hamburger controls are editable again per viewport.
- [x] Customer default for mobile is compact header with hamburger **off**; schema v4 migrates the previously forced schema-v3 state while preserving unrelated Appearance choices.
- [x] BottomNav remains mobile-only and now has both live viewport-profile visibility and explicit CSS width fallback for responsive desktop emulation.
- [x] Mobile Test 26 footer is full-bleed / bottom-flush; tablet and desktop keep the existing inset white gutters.
- [x] Product title and code remain separate rows but are both centered.
- [x] Active subcategory/filter chips use readable foreground text on the pale mint surface; no white-on-pale active text.
- [x] Ranked alternatives and rationale recorded in `docs/TEST26-UX-AUDIT.md` Run 7.
- [x] Release commit: `633e7e1399db1e6cca84256bcfdd516004088476`.
- [x] **FTP Deploy Run #223: SUCCESS** — full QA, TypeScript, unit tests, Vite Test 26 build and FTP smoke passed; `deploy-t` passed; `deploy-root` skipped.
- [x] Test 25 and earlier snapshots remain unchanged.

## P0 live regression — Test 26 still showing expanded mobile header + IMAGE REQUIRED

- [x] Screenshot diagnosis: captured width is 653px, below the 768px mobile/tablet boundary, so an expanded primary header there is persisted-state regression, not a breakpoint interpretation.
- [x] Media diagnosis: `SmartImage.vue` was advertising guessed AVIF siblings for WebP files. Test 26 selected images are WebP-only, so browsers supporting AVIF could request a nonexistent file and fall through to the remote placeholder.
- [x] Restrict AVIF source generation to asset families that actually ship AVIF siblings.
- [x] Bump Appearance schema to v3 and enforce navigation placement during migration and subsequent Admin updates: mobile compact+BottomNav; tablet/desktop expanded top navigation.
- [x] Lock only the primary navigation placement in Admin while preserving the other per-device Appearance controls.
- [x] Add local Test 26 media existence + AVIF-safety regression assertions.
- [x] Add cache-busted launcher URL and Test 26 build marker/no-cache hints.
- [x] Final QA, build, FTP smoke and scoped Test 26 deployment passed.

### Live P0 screenshot-fix delivery

- [x] Rollback checkpoint: `rollback/test26-pre-live-p0-fix` → `a82a209901b70b4bbb2dbfb9f0cfad7669f7d101`.
- [x] SmartImage AVIF root-cause fix: `8da6847156242e84e072ac207e857bdb2ec408ce` plus Test 25 compatibility follow-up `596c86bf1009741a95d1c76ec168bc5c4519104e`.
- [x] Appearance navigation invariant / schema v3: `5773e92a78b89830910b74dfbe597f136288d0b8`.
- [x] Cache-busted launcher and fresh-document marker are included in the final build.
- [x] **FTP Deploy Run #208: SUCCESS** — Test 11–26 QA, TypeScript, unit tests and Vite Test 26 build passed; FTP smoke passed; `deploy-t` passed; `deploy-root` skipped.
- [x] Test 25 and older snapshots were not modified.

## P0 regression hotfix — mobile PWA bottom navigation

- [x] Diagnose the regression: the original `BottomNav.vue` and its mobile styling were still present; Test 26 accidentally kept them visible through the tablet range and kept tablet on compact-drawer navigation.
- [x] Preserve the exact existing mobile BottomNav component/visual treatment; change only its visibility boundary from `lg:hidden` to `md:hidden`.
- [x] Make the BottomNav mobile-only: visible below 48rem/768px, absent on tablet and desktop.
- [x] Remove tablet floating BottomNav geometry and bottom spacing from 48rem upward.
- [x] Change Test 26 customer-default tablet navigation to the expanded blue top header with no hamburger.
- [x] Add a one-time appearance schema migration so browsers that already opened Test 26 do not remain stuck on the mistaken tablet default.
- [x] Preserve Admin per-viewport overrides for the top header; do not make the BottomNav a decorative toggle.
- [x] Add unit and source-contract coverage at the 767/768/1024 boundaries.
- [x] Final CI, FTP smoke and scoped `/t/26` deployment passed.

### Mobile PWA BottomNav hotfix delivery

- [x] Rollback checkpoint: `rollback/test26-pre-mobile-bottomnav-fix` → `b89f52b92774a376a86925a82f72553cdb3235a3`.
- [x] Implementation commit: `453805ae591586d7e2d70f6ed4d82e69dece559d`.
- [x] Contract-alignment commits: `7b6deabaa8fb77bb45ffd30b2b3e8ba83ecea13b` and `0e64894d886ef9b2bf96b84b1dcf106d8213f7b9`.
- [x] Deployment-trigger / regression-selector commit: `c49a37c0aa0e56759e4a3418c8c798232f4e64b5`.
- [x] **FTP Deploy Run #195: SUCCESS** — full QA/build passed, FTP smoke passed, `deploy-t` passed, `deploy-root` skipped.
- [x] Mobile BottomNav remains the same five-item app-like navigation below 768px; tablet/desktop use the blue top navigation.
- [x] Existing Test 26 browsers migrate the old tablet compact default to expanded top navigation; other tablet appearance choices are preserved.
- [x] Test 25 and earlier frozen snapshots were not modified.

## P0 — Test 26: reversible homepage + device-aware appearance controls

### Planning / repository memory
- [x] Read and reconcile `PROJECT-RULES.md`, `HANDOFF.md`, `BACKLOG.md`, asset architecture and current Test 25 source structure before changing implementation.
- [x] Freeze Test 25 as the last delivered snapshot.
- [x] Create rollback branch `rollback/test25-pre-test26` at `328f6c48ea9c4f1346702282ddb6d21ae1685a16`.
- [x] Record five-option ranked architecture analysis in `docs/TEST26-UX-AUDIT.md`.
- [x] Record customer-ready ambiguity questions in `docs/TEST26-CUSTOMER-QUESTIONS.md`.
- [x] Record all unresolved marketing images, dimensions, target paths, formats and `placehold.co` fallbacks in `docs/TEST26-IMAGE-REQUIREMENTS.md`.
- [x] Codify repository-memory, reversible-choice and viewport-profile rules in `docs/PROJECT-RULES.md`.

### Batch 26A — typed reversible configuration foundation
- [x] Add typed appearance policy for mobile / tablet / desktop.
- [x] Add one viewport-profile resolver aligned with 48rem / 64rem breakpoints.
- [x] Add Test 26 appearance Pinia store under `armaghan:test26:appearance`.
- [x] Add validated persistence/migration and reset-to-customer-default behavior.
- [x] Add unit tests for defaults, per-device resolution, reset and invalid persisted state.
- [x] Make no visible page redesign in this batch.

### Batch 26B — Admin Appearance controls
- [x] Add a dedicated Admin → Appearance view, separate from content/translation editing.
- [x] Add Mobile / Tablet / Desktop tabs.
- [x] Add only high-value controls: header mode/actions, hero mode, Home product grid, category numbers and main Home section visibility.
- [x] Prevent impossible navigation states in control validation.
- [x] Add “Reset this device” and “Reset all to customer defaults”.
- [x] Keep all controls keyboard/focus accessible and localized.

### Batch 26C — modular Home
- [x] Refactor `HomeView.vue` into composition-only section assembly.
- [x] Add `HeroSection.vue`; customer default is one hero image while preserving `HeroCarousel.vue` as an alternate mode.
- [x] Default-hide the Home product grid while preserving the `recommended-6` alternate mode.
- [x] Add About Armaghan using the supplied final Persian copy.
- [x] Add Why Armaghan with the supplied four value propositions.
- [x] Add three capability cards with structured detail content.
- [x] Add three horizontal product-category banners that route into the relevant Products category.
- [x] Use only documented Test 26 local paths + `placehold.co` fallbacks for unresolved images.
- [x] Keep section visibility independently configurable per viewport.

### Batch 26D — Header + Products confirmed-safe changes
- [x] Desktop default: expanded header with direct language/help/account affordances.
- [x] Mobile/tablet default: preserve compact header + tested drawer until customer explicitly rejects it.
- [x] Hide adjacent brand/manufacturer text by default without deleting the capability to show it.
- [x] Hide category number labels 01/02/03 by default without removing category codes from domain data.
- [x] Add the pale/mint Products intro surface inspired by customer reference while retaining a no-image CSS fallback.
- [x] Polish colored subcategory/status surfaces so they visually separate from plain white/light surfaces.
- [ ] Verify Persian RTL, English LTR, Arabic/Sorani RTL across all three viewport profiles.

### Batch 26E — Favorites sharing prototype
- [x] Confirm whether shared list carries only products or also owner/list metadata.
- [x] Implement versioned share URL containing only compact product identifiers and no PII.
- [x] Add a read-only shared-list state that can still enter allowed inquiry/WhatsApp flows if approved.
- [x] Add payload validation and URL-length guard.
- [x] Document production migration to opaque/signed backend share tokens.


### Test 26 Run 2 customer-approved integration

- [x] Customer clarification gate closed; no additional customer questions are required for this test.
- [x] Rollback checkpoint created at `rollback/test26-foundation-pre-integration` → `152e20e5ec062f48319a45727c7448d4ee68843e`.
- [x] Connected Admin Appearance policy to live Header, Home, Products and Footer consumers.
- [x] Single Hero is the customer default while the prior carousel remains selectable.
- [x] Rebuilt Home as modular About → Why → Capabilities → Product Banners, with product grid hidden by default.
- [x] Capability details use the existing accessible AdaptivePanel rather than subpages.
- [x] Products category numbers are hidden by policy and the intro/filter surfaces use the requested soft-mint separation.
- [x] Favorites sharing uses a versioned anonymous list URL containing product codes only; no PII is serialized.
- [x] Customer-approved About image source and unresolved capability/banner asset slots are recorded in the image handoff contract.
- [x] Final Run 2 CI + scoped `/t/26` deployment passed.

### Test 26 foundation delivery record

- [x] Frontend foundation commit triggering the validated build: `1b92b0f69a3e2ab68a9e8ce21b93eb3e735f2a74`.
- [x] **FTP Deploy Run #131** completed successfully.
- [x] Test 11–26 regression/source contracts passed.
- [x] TypeScript type-check, unit tests, portrait generation and Vite production build for `/t/26` passed.
- [x] FTP smoke test passed.
- [x] `/public_html/t/26` and mutable `/public_html/t/index.htm` were deployed.
- [x] `deploy-root` was skipped; Test 25 remained frozen and untouched.
- [x] At the Run 1 checkpoint, customer-facing integration was intentionally deferred; Run 2 has now completed that integration without modifying Test 25.

### Test 26 Run 2 delivery record

- [x] Customer-approved integration rollback preserved at `rollback/test26-foundation-pre-integration` → `152e20e5ec062f48319a45727c7448d4ee68843e`.
- [x] Integration build/deploy head: `fafcb0d31e248e3c12ca4533ee85708f5ab08502`.
- [x] **FTP Deploy Run #185** completed successfully: Test 11–26 contracts, media policies, Python checks, portrait generation, TypeScript type-check, unit tests and Vite production build passed.
- [x] FTP smoke test passed and `deploy-t` uploaded the generated Test 26 build; `deploy-root` was skipped.
- [x] Mutable launcher description updated in `57d47e3919a9b87a3fb540723c832ca64fd23d4a`.
- [x] **FTP Deploy Run #186** completed successfully and published the updated `/t/index.htm`; `deploy-root` was skipped.
- [x] Test 25 and all earlier frozen snapshots remained untouched.
- [x] All eight Test 26 image slots now use selected local assets, including the automatically selected Women banner 8-2. See `docs/TEST26-SELECTED-IMAGES.md`.

### Batch 26F — final assets, QA and scoped release
- [x] Replace every required placeholder with an approved local asset or explicitly keep the row open.
- [x] Update Test 26 image-requirement statuses and provenance.
- [x] Add Test 26 source/UX contract; keep all previous regression contracts.
- [x] Isolate all Test 26 browser state under `armaghan:test26:*`.
- [x] Bump frontend version for Test 26.
- [x] Build only `/t/26`; do not modify `/t/25`.
- [x] Put Test 26 first in mutable `/t/index.htm`.
- [x] Run immutable guard, media policy, unit tests, type-check and Vite production build.
- [ ] Verify mobile/tablet/desktop, RTL/LTR, navigation accessibility, favorites, wizard and WhatsApp.
- [x] FTP deploy only `/public_html/t/26` plus mutable launcher; never root deploy and never remote-delete.
- [x] Record delivery commit and successful workflow run.


### Test 26 selected-media deployment — Run 3

- [x] Apply owner choices 1-2 through 7-3 to the corresponding Test 26 image slots.
- [x] Keep generated assets at their native 800×450 / 800×300 dimensions; do not upscale.
- [x] Preserve the previous hero image and all frozen snapshots.
- [x] Leave the unselected Women banner on its explicit placeholder.
- [x] Deploy through the Test 26 build and scoped `/public_html/t` workflow.
- [x] Delivery commit `e2d9931f9c5398de9320ef698051d0f9c56db991`; FTP Deploy Run #188 passed QA, FTP smoke and `deploy-t`; `deploy-root` was skipped.

### Test 26 Run 4 — final Women banner

- [x] Automatically select Women banner option 8-2 from the three numbered candidates.
- [x] Apply the prior modesty preference: loose full-coverage garments on headless mannequins, no human model.
- [x] Save the selected 800×300 WebP at the canonical Test 26 path without upscaling.
- [x] Archive selected source images and update the image requirements/selection record.
- [x] Complete QA-gated Test 26-only deployment. Commit `a35dfacde3140618899b3784db7275edea85dba2`; FTP Deploy Run #190 passed all QA and FTP checks, `deploy-t` passed, and `deploy-root` was skipped.

## P0 — Test 25: gray dark theme + reversible portrait placeholder pipeline

- [x] Freeze Test 24 and keep `/t/24` immutable.
- [x] Create rollback branch `rollback/test24-pre-test25` at the last Test 24 source commit.
- [x] Keep all original horizontal placeholder WebP/AVIF assets untouched.
- [x] Add a deterministic Pillow build pipeline that creates 960×1440 portrait WebP derivatives without stretching or cropping.
- [x] Extend portrait canvases from sampled top/bottom edge colors and apply only a very mild vignette.
- [x] Make portrait placeholders the default for tall product cards.
- [x] Keep landscape placeholders selectable and add Portrait / Landscape / Auto controls to Admin.
- [x] Keep an edge-extend rendering fallback when generated portrait media is unavailable.
- [x] Rebuild dark mode with neutral graphite/gray surfaces while preserving the Armaghan blue navbar and active states.
- [x] Reduce the visual prominence of category number badges 01/02/03.
- [x] Refine product media/body separation without adding copy over images.
- [x] Fix Production Request category-card wrapping, reset hierarchy and locale-aware Back arrow.
- [x] Isolate English footer direction/punctuation and tighten English trust-card alignment.
- [x] Isolate Test 25 browser state under `armaghan:test25:*` and migrate catalog data forward from Test 24.
- [x] Add a strict Test 25 contract and preserve all earlier regression contracts.
- [x] Build/deploy only `/public_html/t/25` plus the mutable launcher.
- [x] Confirm CI, generated portrait media, FTP smoke test and live deployment of `/public_html/t/25`.
- [x] Record successful delivery and mark Test 25 complete.

### Test 25 delivery record

- [x] Rollback checkpoint preserved at `rollback/test24-pre-test25` → `2f10bacfbd33219cf036f546cf91b6a9bfb76916`.
- [x] Main implementation commit: `370635cf6167fcae2212f07ef665516e7e50dd04`.
- [x] Regression-marker fix: `6e54533763033106c10cc53bfbf986faad2feda8`.
- [x] Final contract/build fix: `4a41f486b08cdceb56711fc06435c170f24a16bf`.
- [x] **FTP Deploy Run #86** completed successfully.
- [x] Immutable snapshot guard and Test 11–25 contracts passed.
- [x] CI generated **18 portrait placeholder derivatives** from untouched landscape originals.
- [x] TypeScript type-check, unit tests and Vite production build for `/t/25` passed.
- [x] FTP smoke test passed.
- [x] Portrait derivatives, `/public_html/t/25/index.html` and `/public_html/t/index.htm` were uploaded.
- [x] `deploy-root` was skipped.
- [x] Deployment log confirms: **104 files uploaded; no remote files were deleted.**

### Test 25 hotfix 2 — screenshot bugfix pass

Detailed decision record: `docs/TEST25-BUGFIX2-UX-AUDIT.md`.  
Rollback checkpoint: `rollback/test25-pre-bugfix-2` → `6dc0b9188d50cae156c19fac86f548ed3e3d16f0`.

- [x] Remove the literal `\\n` text node leaked by `SmartImage.vue` and clean the matching CSS escape.
- [x] Add touch/pen swipe navigation to Hero with horizontal-intent detection and preserved vertical page scrolling.
- [x] Replace low-contrast blue foreground accents in dark content/cards with light neutral gray while retaining branded navbar/filled states.
- [x] Make English main content inherit LTR/left alignment globally, including commerce layout, header layout, bottom navigation and mobile drawer.
- [x] Add a focused CI regression contract for these screenshot failures.
- [x] Confirm CI/build/FTP deployment after this hotfix.
- [x] Record the successful hotfix run.


Hotfix delivery:
- [x] Commit: `9ff6f571bd908a368f1e2b43207d4f57fb58d7ba`.
- [x] **FTP Deploy Run #88** completed successfully.
- [x] Test 11–25 regression contracts plus the new screenshot hotfix contract passed.
- [x] Portrait generation, TypeScript, unit tests and Vite build passed.
- [x] FTP smoke test passed.
- [x] `/public_html/t/25/index.html` and `/public_html/t/index.htm` were uploaded.
- [x] **104 files uploaded; no remote files deleted.**

## P0 — Test 24: responsive commerce polish + admin content control

- [x] Freeze Test 23 and leave `/t/23` untouched.
- [x] Show full product images with contain + same-image soft backdrop instead of destructive crop.
- [x] Rebuild dark mode around neutral near-black layered surfaces while preserving Armaghan brand tokens and primary navbar.
- [x] Split mobile hero into image + dedicated copy panel so copy never straddles the image/content seam.
- [x] Use a true split hero and wider editorial composition on laptop/desktop.
- [x] Restore Home main-category images using the same image-first card language as Products.
- [x] Fix English category/filter LTR structure and switch English UI typography to Inter with calmer weights.
- [x] Improve desktop spacing, category-card composition and home manufacturer/trust layout.
- [x] Add a dedicated Admin Home Content editor using existing per-language translation overrides.
- [x] Keep quick manual customer creation, allow email OR mobile, then open Customer 360.
- [x] Allow managed customers to sign in with either normalized mobile/WhatsApp or email.
- [x] Add an exact Laravel Socialite + Google Cloud activation guide without exposing secrets to Vue.
- [x] Isolate Test 24 browser state under `armaghan:test24:*` and migrate catalog data from Test 23.
- [x] Add a strict Test 24 source/UX contract and retain previous regression contracts.
- [x] Build/deploy only `/public_html/t/24` plus the mutable launcher.
- [x] Confirm CI, FTP smoke test and live deployment of `/public_html/t/24`.
- [x] Record successful delivery and mark Test 24 complete.

### Test 24 delivery record

- [x] Main implementation commit: `4a7158afbe594c231ef984d831a2b038416a67a0`.
- [x] Contract repair / final deploy commit: `74fa4e39d611959e5ac2eb6b82b8f8e5d2ea1a26`.
- [x] **FTP Deploy Run #82** completed successfully.
- [x] Immutable snapshot guard and Test 11–24 contracts passed.
- [x] TypeScript type-check and unit tests passed.
- [x] Vite production build for `/t/24` passed.
- [x] FTP smoke test passed.
- [x] `/public_html/t/24/index.html` and `/public_html/t/index.htm` were uploaded.
- [x] `deploy-root` was skipped.
- [x] Deployment log confirms: **85 files uploaded; no remote files were deleted.**

## P0 — Test 23: Image-first categories + low-copy product cards

- [x] Freeze Test 22 and leave `/t/22` untouched.
- [x] Add the three user-provided category images as optimized local WebP derivatives; no image generation.
- [x] Replace the three plain main-category buttons with image-first clickable cards and refined numeric badges.
- [x] Make tapping the active category return to all categories without adding another text control.
- [x] Remove duplicate main-category controls from the desktop filter rail.
- [x] Make product media dominant using a tall 2:3 media frame so typical cards are ~70% image.
- [x] Remove all visible text/badges from product images.
- [x] Make the metadata row contain only the product code.
- [x] Show the product title only when available; otherwise show only the localized unavailable label.
- [x] Replace the settings-like details icon with a Lucide Menu + down-right arrow composite.
- [x] Enforce icon-only card actions and remove the obsolete Compact/Labeled selector from the current admin overview.
- [x] Increase the WhatsApp glyph from 22px to 25.3px (15%).
- [x] Isolate Test 23 browser state under `armaghan:test23:*` while migrating catalog data forward from Test 22.
- [x] Add a Test 23 media/UI source contract and keep prior regression contracts active.
- [x] Build/deploy only `/public_html/t/23` plus the mutable launcher.
- [x] Confirm CI, FTP smoke test and live deployment of `/public_html/t/23`.
- [x] Record the successful deployment run and mark Test 23 delivered.

### Test 23 delivery record

- [x] **FTP Deploy Run #79** completed successfully.
- [x] Immutable snapshot guard and Test 11–23 contracts passed.
- [x] TypeScript type-check and unit tests passed.
- [x] Vite production build for `/t/23` passed.
- [x] FTP smoke test passed.
- [x] User-provided category thumbnails and `/public_html/t/23/index.html` were uploaded.
- [x] `/public_html/t/index.htm` was updated with Test 23 first.
- [x] `deploy-root` was skipped.
- [x] Deployment log confirms: **85 files uploaded; no remote files were deleted.**

## P0 — Test 22: Release hardening + immutable snapshot handoff

- [x] Freeze Test 21 and leave `/t/21` untouched.
- [x] Bump frontend package version to `0.22.0`.
- [x] Isolate Test 22 browser storage under `armaghan:test22:*`.
- [x] Preserve Test 21 product data as a catalog migration source.
- [x] Build only `/t/22` from the Vue source.
- [x] Add a strict Test 22 release contract while keeping Test 20/21 regression contracts.
- [x] Put Test 22 first in the mutable launcher.
- [x] Update CI artifact/deploy targeting from Test 21 to Test 22.
- [x] Confirm CI, FTP smoke test and live deployment of `/public_html/t/22`.
- [x] Record the successful deployment run and mark Test 22 delivered.

### Test 22 delivery record

- [x] **FTP Deploy Run #74** completed successfully.
- [x] Immutable snapshot guard and Test 11–22 contracts passed.
- [x] TypeScript type-check and unit tests passed.
- [x] Vite production build for `/t/22` passed.
- [x] FTP smoke test passed.
- [x] `/public_html/t/22/index.html` and `/public_html/t/index.htm` were uploaded.
- [x] `deploy-root` was skipped.
- [x] Deployment log confirms: **No remote files were deleted.**

## P0 — Test 21: Selectable product placeholders

- [x] Freeze Test 20 and leave `/t/20` untouched.
- [x] Generate three complete visual sets for all six subcategories.
- [x] Make set B (`paper-cut`) the default.
- [x] Preserve sets A and C as administrator-selectable alternatives.
- [x] Add an accessible preview selector to the admin overview.
- [x] Keep product-specific media above placeholders in the fallback chain.
- [x] Recover from broken product media with the active subcategory placeholder.
- [x] Isolate Test 21 browser state from released snapshots.
- [x] Add AVIF/WebP media validation, unit coverage and a Test 21 source contract.
- [x] Build and deploy only `/public_html/t/21` plus the mutable launcher.

## P0 — Final generated image set

Decision record: `docs/IMAGE-GENERATION-AUDIT.md`.

- [x] Inventory and triangulate all image prompt/list/manifest files in the repository.
- [x] Rank five implementation options and select the current 10-asset contract from `pics.md`.
- [x] Generate 10 canonical source images with one coherent visual direction.
- [x] Derive optimized AVIF and WebP variants in the paths defined by `pics.md`.
- [x] Record prompt provenance, dimensions, sizes and checksums in a generated-media manifest.
- [x] Wire final media into the current Vue application without deleting stock fallbacks.
- [x] Run image-policy tests, type-check, unit tests and production build.

## P0 — Subcategory product placeholders

- [x] Generate three coherent six-image sets for subcategories 11, 12, 21, 22, 31 and 32.
- [x] Select paper-cut set B as the safe default for products without media.
- [x] Preserve sets A and C as administrator-selectable alternatives.
- [x] Keep product-specific media above placeholders in the fallback priority.
- [x] Fall back to the selected subcategory image when product media fails to load.
- [x] Persist the administrator selection and expose an accessible preview selector.
- [x] Store optimized AVIF and WebP derivatives with a versioned manifest.
- [x] Add registry unit coverage and a repository media contract.

## P0 — Test 20: Customer 360 + Product Administration

Detailed ranked UX decisions: docs/TEST20-UX-AUDIT.md.

### Snapshot and delivery
- [x] Freeze Test 19 and leave /t/19 untouched.
- [x] Build only /t/20.
- [x] Put Test 20 first in /t/index.htm.
- [x] Add Test 20 QA contract and keep previous contracts regression-safe.
- [x] FTP deploy only /public_html/t/20 plus launcher; no remote deletion and no root deployment.

### Customer 360
- [x] Fix reactive Pinia Proxy clone path with toRaw + structuredClone.
- [x] Add adaptive bottom-sheet (<1024px) / centered modal (>=1024px) primitive.
- [x] Add explicit not-found state so customer management cannot render as an unexplained blank panel.
- [x] Add customer KPI summary: priority, previous orders and current order.
- [x] Add 0–5 star internal Customer priority.
- [x] Keep editable name, email, WhatsApp, country/flag, address, location and notes.
- [x] Keep profile photo upload/optimization.
- [x] Add current-order and timeline controls with a visible progress summary.
- [x] Add editable password state.
- [x] Make manager-set customer email/password usable by the Test 20 sign-in adapter.
- [x] Keep direct-access modes Permanent / Expiring, generation, expiry and revoke.
- [x] Resolve valid direct-access customer tokens into the correct customer session.

### Product-card micro-interaction
- [x] Add subtle hover scale/lift using transform only.
- [x] Restrict hover effect to fine pointers with hover support.
- [x] Keep reduced-motion protection.

### Product administration at ~500 items
- [x] Add search across code and all four localized product names.
- [x] Add category filter.
- [x] Add subcategory filter.
- [x] Add pagination with 50 rows per page by default.
- [x] Add 25 / 50 / 100 page-size controls.
- [x] Make select-all target the visible page.
- [x] Keep batch delete.
- [x] Add persistent three-dot overflow per product row.
- [x] Disable row overflow while batch mode is active.
- [x] Keep pagination controls at least 44×44 CSS px.
- [ ] In Laravel production, move filtering/search/pagination to server-side queries.

### Product Add/Edit
- [x] Add adaptive Add/Edit Product panel using the same sheet/modal rule.
- [x] Add four product-name fields: fa / ar / en / ku.
- [x] Persist localized names on the product record with registry fallback.
- [x] Infer category/subcategory/spec schema from first two product-code digits.
- [x] Show inferred category/subcategory immediately.
- [x] Auto-fill locked/negotiable specs when prefix changes.
- [x] Add Reset from code to restore the subcategory schema.
- [x] Keep specs editable after inference.

### Repository memory
- [x] Add five-option ranked analysis and self-detected issues to docs/TEST20-UX-AUDIT.md.
- [x] Mark delivery/QA items complete after successful CI and live FTP deployment.


### Test 20 delivery record
- [x] **FTP Deploy Run #68** completed successfully.
- [x] Test 11–20 regression contracts passed.
- [x] TypeScript type-check passed.
- [x] Unit tests passed.
- [x] Vite production build for /t/20 passed.
- [x] FTP smoke test passed.
- [x] /public_html/t/20/index.html and /public_html/t/index.htm were uploaded.
- [x] deploy-root was skipped.
- [x] Deployment log confirms: **No remote files were deleted.**
- [x] Test 19 remains frozen and was not rebuilt into /t/19.


## P0 — Test 19

### Snapshot + delivery
- [x] Freeze Tests 01–18.
- [x] Build a new immutable `/t/19` artifact.
- [x] Keep `/t/index.htm` mutable and newest test first.
- [x] Add Test 19 contract before deployment.
- [x] Keep root untouched and FTP non-destructive.

### Mobile/tablet header + drawer
- [x] Header contains only brand + theme + hamburger below desktop breakpoint.
- [x] Remove mobile/tablet header login.
- [x] Drawer language controls become 44px rounded-square buttons: Fa / En / ع / ک.
- [x] Drawer width becomes `clamp(260px,70vw,340px)`; tablet max 340px.
- [x] Drawer scrim stays 40% dark + blur.
- [x] Drawer login button moves above divider in fixed footer.
- [x] Show small readable `تولیدی ارمغان` / localized manufacturer label below login.
- [x] Add Help/Guide entry in drawer in all four languages.
- [x] Animate drawer enter/leave from the side in 200ms; honor reduced motion.

### Product copy / typography / visual consistency
- [x] Remove public-facing prototype/test words: SVG, آزمایشی, demo/test wording, technical media labels.
- [x] Replace ad-hoc typography with semantic scale.
- [x] Use Vazirmatn FD for fa/ar/ku and Roboto for English.
- [x] Hide horizontal scrollbar chrome in category chips while preserving scrolling.
- [x] Reserve layout space for bottom nav so content is never obscured.
- [x] Keep header z-index above all normal scrolling content.
- [x] Replace harsh dark gradients with neutral surfaces + restrained brand glow.
- [x] Remove hardcoded light surfaces from primary production/admin/customer components.
- [x] Use truthful generic garment-manufacturer copy only; no invented certifications/prices/claims.

### Sheet/modal motion
- [x] BaseSheet enters from bottom and exits to bottom over 200ms.
- [x] Backdrop fades in/out with the sheet.
- [x] Exit animation must complete before DOM removal.
- [x] Preserve focus trap, Escape, outside-tap close and reduced-motion behavior.

### Product card action modes
- [x] Keep admin-selectable Compact / Labeled modes.
- [x] Compact remains default.
- [x] Labeled mode uses localized `Order / Favorite / Specs`.
- [x] Keep WhatsApp brand treatment, neutral favorite container and centered icon geometry.

### Admin IA
- [x] Add top-level admin views: Overview / Customers / Products / Languages.
- [x] Overview can show customers + products together.
- [x] Customers and Products have dedicated focused views.
- [x] Add selectable data tables with row checkboxes, select-all and contextual batch action bar.
- [x] Add bulk delete with confirmation for customers and products.
- [x] Add customer create/delete.
- [x] Add vertical overflow menu per customer row.
- [x] Replace text-heavy impersonation action with icon + accessible label.
- [x] Add dedicated customer-management panel/detail view.

### Customer 360 editor
- [x] Editable name, email, WhatsApp, country/flag, address, location text, notes.
- [x] Editable current order state and timeline stage.
- [x] Show previous-order count and current-order summary.
- [x] Profile-photo upload preview in prototype.
- [x] Password set/reset control in prototype.
- [x] Direct-access link control with concise modes: `Permanent` / `Expiring`.
- [x] Expiring link exposes expiry/revoke controls.
- [x] Separate `Manage customer` from `Impersonate customer`.

### Wishlist lead notifications
- [x] Add admin alert metric for customers/visitors whose favorites changed.
- [x] Model anonymous visitors with generated visitor token, not IP as identity.
- [x] IP is documented as optional backend metadata only.
- [x] Logged-in lead actions expose contact CTA.
- [x] Guest lead actions expose in-app message path for their shared favorites page.
- [x] Add `Invite to create account` action for guest leads.

### Authentication surfaces
- [x] Keep working prototype username/password sign-in.
- [x] Add prototype registration form and persisted prototype account.
- [x] Add magic-link prototype with generated/revocable token and Permanent/Expiring mode.
- [x] Add Google sign-in button wired to a backend endpoint contract; do not fake OAuth success.
- [x] Document Laravel Socialite credentials/backend requirement as the only blocker for live Google OAuth.
- [x] Keep login modal centered with blurred backdrop.

### i18n completeness
- [x] Move visible application copy into a single translation registry.
- [x] Add persisted per-language translation overrides.
- [x] Eliminate mixed-language UI in Home / Products / Production / Favorites / Tracking / Login / Drawer / Admin.
- [x] Keep `html lang` and semantic `dir` correct for each locale.
- [x] Keep overall shell geometry stable across languages with CSS; English content and controls are LTR/left-aligned.
- [x] Translate product category/subcategory/product/spec labels needed by current UI.

### Translation manager
- [x] Add Admin → Languages.
- [x] Language selector + section/group selector + search.
- [x] Every translatable key has its own editable field.
- [x] Single-item save/reset.
- [x] Multi-select + bulk reset.
- [x] Show base value and overridden value clearly.
- [x] Persist overrides locally in prototype; design API shape for later DB persistence.

### Repository memory
- [x] Add detailed 5-option ranked analysis for each requested item and self-detected visual/product bugs in `docs/TEST19-UX-AUDIT.md`.
- [x] Update backlog checkboxes as each implementation block lands.
- [x] Reviewed `pics.md`; image requirements did not change in Test 19, so no update was required.

## P0 — Backend MVP delivery

- [x] Freeze Test 26 and create `snapshot/test26-final`.
- [x] Promote backend work from deferred P1 to active P0 scope.
- [ ] Verify production hosting supports PHP >= 8.3 and a safe Laravel document-root layout before installation.
- [ ] Install Laravel 13 under `platform/backend` with SQLite development defaults.
- [ ] Convert the approved SQL draft into Laravel migrations/models/seeders.
- [ ] Install Filament 5 and create administrator authentication.
- [ ] Product CRUD: add/edit/archive, category/subcategory, availability, sort order and image management.
- [ ] Customer CRUD: identity/contact/notes/status plus generate/revoke one-tap access links.
- [ ] Public read endpoints for categories/products/site settings; replace browser-local catalog persistence in Test 27+ only.
- [x] Backend favorites-share records with compact high-entropy public token and WhatsApp share action.
- [x] Customer magic-link login: hashed token, expiry/revoke and secure session. Trusted-device persistence remains optional and was not required for MVP.
- [ ] Persist high-value site settings including semantic color-role mappings and safe contrast preview.
- [ ] Add production backup, health check, audit log and minimal recovery procedure.
- [ ] Deploy production backend without modifying `/t/26`; any required frontend wiring lands in Test 27+.
- [ ] End-to-end delivery QA: admin product/customer changes visible publicly, favorites link opens correctly, WhatsApp handoff works, login link works, color settings render safely.

## P1 — Backend productionization
- [x] Laravel 13 backend scaffold installed: SQLite local/dev/test + MySQL/MariaDB production contract.
- [x] Filament 5 Panel Builder installed; domain Resources and production admin provisioning remain P0.
- [ ] Persist production data to MySQL/MariaDB; keep SQLite for local/dev/test fixtures.
- [ ] Laravel Socialite Google OAuth with real client credentials.
- [ ] Signed/hashed magic links with expiry, scope, revoke and audit.
- [ ] Server-side media library and queued image conversions.

## Definition of Done — Test 19
- [x] Tests 01–18 immutable.
- [x] Test 19 first in launcher.
- [x] No public prototype/test wording.
- [x] No mixed-language major flow for fa/ar/en/ku.
- [x] Mobile drawer, bottom sheets and theme transitions visually coherent.
- [x] Admin supports focused sections, multi-select, batch delete, customer CRUD and Customer 360 edit.
- [x] Translation editor works with per-key and bulk reset.
- [x] Auth surfaces clearly distinguish functional prototype flows from backend-required Google OAuth.
- [x] Test 11–19 contracts, type-check, unit tests, build, FTP smoke and deploy all pass.
- [x] Root deploy skipped; no remote file deletion.


### Delivery record
- [x] Main implementation landed in `398902cd1e2ed1ce600386656996aa427c5e1cfb`.
- [x] Regression fixes landed through `02150c18ba16cd29b46f611371ac6350bb3936a0`.
- [x] **FTP Deploy Run #63** completed successfully.
- [x] Test 11–19 contracts passed.
- [x] TypeScript type-check passed.
- [x] Unit tests passed.
- [x] Vite production build passed.
- [x] FTP smoke test passed.
- [x] `/public_html/t/19/index.html` and `/public_html/t/index.htm` were uploaded.
- [x] `deploy-root` was skipped.
- [x] Deployment log confirms: **No remote files were deleted.**

### Verified closeout for this bounded run
- Implementation commit: `88d4172e50413dec3d8951ba093535894b659db4`.
- FTP Deploy #325, run `37001349371`: QA, type-check, 41 Vue tests, active Test28 build, FTP smoke and deploy-t PASS; deploy-root SKIPPED. Frozen snapshot guard PASS for all 27 frozen tests.
- Fresh live browser read after deployment: heading/category/product title RGB(200,227,219), card RGB(30,32,36), no horizontal overflow at 1363 CSS px; repaired default is live (12.015:1 on card).
- Fresh guest request to `/backend/admin/products` redirected to `/backend/admin/login`; no authenticated-admin acceptance is inferred.
- Additional observed copy follow-up: Persian Home still displays the English eyebrow "Why Armaghan?". Keep the broad multilingual Home acceptance open and localize that label in the next bounded fix.
- Mobile/tablet and valid customer/admin/share/WhatsApp end-to-end acceptance remain open. This run advances partial final QA; it does not declare final project delivery.


## Owner-authorized root Test29 release — 2026-10-02
Owner explicitly authorizes root index replacement with selected Test29 while preserving /t. Independent selector: config/root-release.json. Tests 01–28 remain frozen; Test29 is mutable. Requested eyebrow/producible defaults and real Filament navigation are included. Root deployment verification pending. Google requires owner Cloud credentials; no real login acceptance is claimed.

Google customer signup/sign-in implemented using official Socialite with a create-only identity table and eleven backend security cases. Root hides local demo password/signup and ignores browser-only admin roles; real customer sessions remain authoritative. Backend additive deploy and real Google credential readiness pending; never claim live Google login before actual provider verification.

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
