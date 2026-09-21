# Test 19 — UX/Product Audit, ranked alternatives, and chosen actions

هدف: حذف حس prototype/test، یکپارچه‌سازی theme و typography، تکمیل مدیریت مشتری/محصول، i18n قابل مدیریت، و آماده‌سازی مسیرهای احراز هویت برای backend واقعی.

منابع طراحی مبنا: WCAG 2.2 (target size/focus), W3C Internationalization (lang/dir), Material Navigation Drawer, Carbon Data Table (multi-select/batch actions/overflow), Apple HIG Sheets/Motion. اصل تصمیم‌گیری: **کمترین بار شناختی + بیشترین وضوح عملیاتی + کمترین ریسک destructive + رفتار responsive قابل پیش‌بینی**.

## 1) دکمه‌های انتخاب زبان

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | مربع 44×44 با radius 12px، active واضح، کد کوتاه Fa/En/ع/ک | touch-target پایدار، خوانا، با هویت app-like | کمی فضا بیشتر از circle |
| 2 | segmented control چهارقسمتی | فشرده و منظم | در RTL/LTR و متن‌های ناهمگون شلوغ می‌شود |
| 3 | pill جداگانه | دوستانه | عرض‌های نابرابر و ریتم ضعیف |
| 4 | circle | ساده | کدهای لاتین/عربی داخل circle هم‌وزن دیده نمی‌شوند |
| 5 | select | کم‌جا | discoverability ضعیف در drawer |

**انتخاب:** گزینه 1؛ با WCAG target sizing و ریتم بصری بهتر سازگار است.

## 2) جایگاه ورود در drawer

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | footer ثابت: دکمه آبی ورود بالای divider + «تولیدی ارمغان» زیر آن | CTA همیشه در دسترس و برندینگ روشن | نیازمند safe-area |
| 2 | ورود بالای drawer | سریع دیده می‌شود | با language و header رقابت می‌کند |
| 3 | کارت ورود وسط dashboard | context خوب | هنگام scroll ممکن است گم شود |
| 4 | icon-only footer | جمع‌وجور | برچسب عمل کم‌رنگ |
| 5 | ورود در bottom-nav | دسترسی سریع | navigation اصلی را آلوده می‌کند |

**انتخاب:** گزینه 1.

## 3) عرض drawer موبایل

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | 70vw با min 260px و max 340px؛ tablet cap 340px | مطابق درخواست و کنترل‌شده | روی موبایل خیلی کوچک ممکن است 70vw کافی نباشد؛ min حل می‌کند |
| 2 | 70vw خالص | ساده | روی 320px باریک می‌شود |
| 3 | 320px ثابت | پایدار | روی موبایل کوچک بیش از حد غالب |
| 4 | 80vw | فضای زیاد | زمینه اصلی کم دیده می‌شود |
| 5 | full-width | ساده | حس drawer از بین می‌رود |

**انتخاب:** گزینه 1.

## 4) حذف نشانه‌های تستی/غیرمحصولی

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | content scrub کامل + regression test برای واژه‌های ممنوع | جلوی برگشت regression را می‌گیرد | نیازمند نگه‌داری denylist |
| 2 | فقط حذف موارد دیده‌شده | سریع | موارد پنهان باقی می‌ماند |
| 3 | feature flag production-copy | انعطاف | complexity اضافی |
| 4 | CSS hide | سریع | متن همچنان در DOM/دسترس‌پذیری است |
| 5 | بی‌تغییر تا backend | کم‌کار | تجربه غیرمحصولی باقی می‌ماند |

**انتخاب:** گزینه 1؛ واژه‌هایی مثل SVG، آزمایشی، نمونه تست، demo hint از UI عمومی حذف می‌شوند.

## 5) یکدستی اندازه فونت

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | semantic type scale محدود: 12/14/16/20/28 با line-height ثابت | consistency بالا و responsive ساده | نیازمند refactor |
| 2 | clamp آزاد در هر component | responsive | کنترل مرکزی کمتر |
| 3 | Tailwind defaults | سریع | با فارسی همیشه مناسب نیست |
| 4 | یک سایز عمومی | ساده | hierarchy از بین می‌رود |
| 5 | تنظیم دستی screenshot-by-screenshot | دقیق موضعی | شکننده |

**انتخاب:** گزینه 1.

## 6) ناسازگاری light/dark بین componentها

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | semantic design tokens + حذف bg-white/text-slate hardcode در componentهای اصلی | ریشه‌ای و قابل نگهداری | refactor بیشتر |
| 2 | global !important dark overrides | سریع | cascade شکننده |
| 3 | duplicate templates light/dark | کنترل کامل | هزینه نگه‌داری بالا |
| 4 | dark فقط صفحه اصلی | محدود | ناسازگاری باقی |
| 5 | حذف dark mode | ساده | خلاف نیاز محصول |

**انتخاب:** گزینه 1.

## 7) اطلاعات درست تولیدی لباس ارمغان

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | فقط facts عملیاتی عمومی و بی‌ادعا: تولید پوشاک، سفارش سفارشی، برند مشتری، بسته‌بندی، ارتباط فروش واتساپ | امن و محصولی | از claims بازاری پرهیز می‌کند |
| 2 | متن مارکتینگ قوی | جذاب | خطر ادعای بی‌پشتوانه |
| 3 | placeholder | امن | غیرمحصولی |
| 4 | حذف معرفی | ساده | اعتماد کمتر |
| 5 | اطلاعات ساختگی کامل | ظاهراً کامل | غیرقابل قبول |

**انتخاب:** گزینه 1.

## 8) gradient و نور مخصوصاً dark

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | neutral surfaces + accent glow محدود 4–8% + gradient فقط hero/media | premium و خوانا | کمتر «نمایشی» |
| 2 | gradient قوی برند در همه‌جا | پرانرژی | خستگی بصری |
| 3 | flat کامل | تمیز | هویت کمتر |
| 4 | glassmorphism زیاد | مدرن | contrast و performance |
| 5 | neon dark | چشمگیر | B2B نامتناسب |

**انتخاب:** گزینه 1.

## 9) ورود در navbar موبایل

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | حذف کامل از header موبایل/تبلت؛ فقط drawer footer | header خلوت | نیاز به hamburger |
| 2 | icon-only | سریع | باز هم شلوغ |
| 3 | متن «ورود» | واضح | اشغال فضا |
| 4 | bottom nav | دسترسی | primary nav را شلوغ می‌کند |
| 5 | floating | دیده می‌شود | مزاحم content |

**انتخاب:** گزینه 1.

## 10) animation sheet و drawer

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | Vue Transition؛ 200ms ease-out enter / ease-in exit؛ sheet از پایین، drawer از کنار؛ reduced-motion | کوتاه، قابل لغو، متناسب با منشأ حرکت | نیازمند leave state |
| 2 | 300ms spring | نرم | کند برای interaction پرتکرار |
| 3 | CSS بدون leave | ساده | خروج ناگهانی |
| 4 | JS animation | کنترل | complexity |
| 5 | بدون animation | بدون motion bug | context spatial ضعیف |

**انتخاب:** گزینه 1.

## 11) ساختار مدیر: مشتری‌ها و محصولات

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | دو tab سطح اول «مشتری‌ها / محصولات» + حالت «نمای کلی»؛ data table selectable؛ row overflow؛ detail drawer/page | واضح، scalable، batch-friendly | نیازمند state بیشتر |
| 2 | accordion دو بخش | ساده | جدول‌های بزرگ بد می‌شوند |
| 3 | هر دو جدول همیشه stacked | همه‌چیز دیده می‌شود | طول و بار شناختی زیاد |
| 4 | sidebar admin nav | scalable | برای این scope سنگین |
| 5 | cards-only | موبایل‌پسند | عملیات داده‌ای ضعیف |

**انتخاب:** گزینه 1؛ «نمای کلی» امکان دیدن هر دو را حفظ می‌کند.

## 12) آموزش و راهنما چهارزبانه

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | Help card در drawer با modal/sheet محتوای ترجمه‌شده و 3 مسیر اصلی | discoverable و کم‌مزاحمت | محتوا باید ترجمه شود |
| 2 | صفحه مستقل | کامل | دور از context |
| 3 | tooltipها | contextual | برای آموزش کلی کافی نیست |
| 4 | onboarding اجباری | آموزش قوی | مزاحم |
| 5 | FAQ footer | ساده | discoverability کم |

**انتخاب:** گزینه 1.

## 13) هشدار به مدیر برای update مطلوب‌ها و guest list

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | event/notification model با anonymous visitor token؛ IP فقط metadata نه identity؛ badge حساس + contact action | privacy بهتر، پایدارتر از IP | backend لازم |
| 2 | رکورد بر اساس IP | ساده | NAT/VPN/privacy؛ هویت ناپایدار |
| 3 | فقط logged-in events | ساده | guest lead از دست می‌رود |
| 4 | localStorage-only | prototype | مدیر واقعی نمی‌بیند |
| 5 | ایمیل batch | عملی | real-time نیست |

**انتخاب:** گزینه 1؛ در prototype event inbox محلی مدل می‌شود، backend contract ثبت می‌شود.

## 14) انتخاب دسته‌ای و عملیات batch

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | checkbox per row + select-all + contextual batch bar + confirm destructive | الگوی استاندارد data table | UI بیشتر |
| 2 | multi-select dropdown | فشرده | کمتر discoverable |
| 3 | long-press selection | موبایل‌محور | desktop ضعیف |
| 4 | shift-click فقط | power user | accessibility کم |
| 5 | بدون batch | ساده | inefficient |

**انتخاب:** گزینه 1.

## 15) ثبت‌نام/ورود Google/user-pass/magic link

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | UI کامل + backend-ready adapter؛ user/pass prototype functional؛ magic-link prototype token؛ Google endpoint contract تا زمان client credentials | قابل ادامه بدون جعل OAuth | Google واقعی نیازمند credentials/backend |
| 2 | Google-only | ساده | وابستگی خارجی |
| 3 | user/pass-only | قابل کنترل | friction |
| 4 | magic-link-only | ساده | email delivery لازم |
| 5 | mock همه‌چیز و نمایش موفقیت جعلی | ظاهراً کامل | غیرقابل قبول |

**انتخاب:** گزینه 1؛ مواردی که بدون backend/credentials واقعاً قابل فعال‌سازی نیستند «mock success» نمی‌شوند.

## 16) یک‌زبانه بودن کامل UI و جهت انگلیسی

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | locale registry واحد + override store؛ html lang/dir صحیح؛ shell position با CSS physical ثابت؛ English content/control ltr | semantic صحیح + layout ثابت | refactor وسیع |
| 2 | html همیشه rtl و text-align انگلیسی | ظاهر ثابت | bidi semantic غلط |
| 3 | flip کامل English | استاندارد locale | خلاف خواسته shell ثابت |
| 4 | ترجمه فقط صفحات اصلی | سریع | mixed language |
| 5 | hardcode چهار template | کنترل | نگه‌داری ضعیف |

**انتخاب:** گزینه 1.

## 17) مدیریت ترجمه توسط مدیر

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | translation registry keyed by namespace + admin editor searchable/filterable + single/bulk reset + local persistence prototype | قابل مدیریت، مهاجرت‌پذیر به DB | refactor keys |
| 2 | JSON editor خام | سریع | خطاپذیر |
| 3 | textarea per language | ساده | پیدا کردن عبارت سخت |
| 4 | CMS خارجی | قدرتمند | dependency |
| 5 | hardcode | ساده | نیاز را حل نمی‌کند |

**انتخاب:** گزینه 1.

# باگ‌های خودتشخیص از تصاویر

## A) mixed-language و Persian specs در English

| رتبه | گزینه | دلیل |
|---:|---|---|
| 1 | ترجمه تمام UI shell + wizard labels از registry؛ data product names/specs به‌صورت locale-aware | mixed language واقعی را رفع می‌کند |
| 2 | پنهان کردن specs در English | اطلاعات کم می‌شود |
| 3 | machine translate runtime | dependency/کیفیت |
| 4 | فقط headings | کافی نیست |
| 5 | بی‌تغییر | unacceptable |

**انتخاب:** گزینه 1.

## B) scrollbar افقی category chips دیده می‌شود

| رتبه | گزینه | دلیل |
|---:|---|---|
| 1 | scroll container با scrollbar hidden + edge fade + snap اختیاری | قابلیت scroll حفظ، chrome حذف |
| 2 | wrap | ارتفاع متغیر زیاد |
| 3 | dropdown | discoverability کمتر |
| 4 | arrows | موبایل نامناسب |
| 5 | visible scrollbar | زشت |

**انتخاب:** گزینه 1.

## C) bottom-nav روی content می‌افتد

| رتبه | گزینه | دلیل |
|---:|---|---|
| 1 | layout reserve با CSS variable nav height + safe area | ریشه‌ای |
| 2 | margin دستی هر view | شکننده |
| 3 | nav static | app-like از بین می‌رود |
| 4 | hide on scroll | پیچیده |
| 5 | بی‌تغییر | content occlusion |

**انتخاب:** گزینه 1.

## D) white cards در dark screen

| رتبه | گزینه | دلیل |
|---:|---|---|
| 1 | semantic surface tokens در wizard/admin/customer cards | consistent |
| 2 | dark !important patch | شکننده |
| 3 | opacity overlay | contrast بد |
| 4 | force light wizard | theme شکسته |
| 5 | حذف dark | خلاف نیاز |

**انتخاب:** گزینه 1.

## E) اطلاعات کارت placeholder هنوز حس development دارد

| رتبه | گزینه | دلیل |
|---:|---|---|
| 1 | حذف badge فنی، استفاده از media placeholder بدون label فنی | محصولی |
| 2 | label «تصویر محصول» | بهتر ولی زائد |
| 3 | skeleton دائمی | misleading |
| 4 | blank block | ضعیف |
| 5 | SVG badge | تستی |

**انتخاب:** گزینه 1.

## F) header در موبایل هنوز CTA اضافه دارد

| رتبه | گزینه | دلیل |
|---:|---|---|
| 1 | brand + theme + hamburger فقط | مطابق اصل تمرکز |
| 2 | login icon-only | شلوغی |
| 3 | language inline | شلوغی |
| 4 | search inline | context-dependent |
| 5 | همه utilityها | شکست فعلی |

**انتخاب:** گزینه 1.

# ترتیب اجرا

1. Design token / typography / animation / drawer / copy scrub.
2. i18n registry + translation admin.
3. Admin information architecture + CRUD prototype + batch actions + customer detail.
4. Auth surface + magic-link prototype contract + Google backend contract.
5. Notification inbox model for wishlist updates.
6. QA contracts, freeze Test 18, build Test 19, deploy non-destructively.
