# Test 20 — Customer 360 + Product Administration UX Audit

این سند حافظه تصمیم‌های Test 20 است. هدف: تکمیل پروفایل مشتری و مدیریت مقیاس‌پذیر محصول بدون دست‌زدن به snapshot آزادشده Test 19.

منابع مبنا:
- Laws of UX — Aesthetic-Usability, Hick, Fitts, Common Region, Proximity: https://lawsofux.com/
- W3C WCAG target sizing (44×44 CSS px): https://www.w3.org/WAI/WCAG21/Understanding/target-size
- Carbon Data Table — search, pagination, batch selection, overflow row actions: https://carbondesignsystem.com/components/data-table/usage/

## 1) پروفایل کامل مشتری و surface واکنش‌گرا

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | Adaptive surface: bottom sheet روی موبایل/تبلت و modal مرکزی روی دسکتاپ + dashboard summary + بخش‌بندی اطلاعات | context حفظ می‌شود، اطلاعات زیاد chunk می‌شود، مناسب touch و desktop | component پیچیده‌تر |
| 2 | Bottom sheet در همه اندازه‌ها | implementation واحد | روی دسکتاپ نامتناسب و کشیده |
| 3 | Modal در همه اندازه‌ها | دسکتاپ عالی | keyboard/viewport موبایل ضعیف |
| 4 | Route مستقل Customer 360 | deep-link و فضای زیاد | edit سریع از table را قطع می‌کند |
| 5 | Expanded row داخل table | سریع | برای اطلاعات کامل بسیار متراکم |

**انتخاب:** گزینه 1. در breakpoint زیر 1024px sheet از پایین، در >=1024px lightbox modal مرکزی. بخش‌های هویت، اولویت، تماس/آدرس، سفارش/تایملاین، رمز و لینک ورود، یادداشت و عکس پروفایل جدا می‌شوند.

### باگ ریشه‌ای sheet خالی
structuredClone(row) روی object واکنشی Pinia ممکن است DataCloneError بدهد.  
**اقدام:** structuredClone(toRaw(row)) + state صریح not-found؛ panel دیگر نباید خالی بی‌دلیل نمایش داده شود.

## 2) اولویت مشتری با ستاره

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | 1 تا 5 ستاره با عنوان «اولویت مشتری»، قابل پاک‌کردن | سریع، قابل اسکن، آشنا | باید به‌عنوان اولویت داخلی تعریف شود |
| 2 | High / Medium / Low | ساده | جزئیات کمتر |
| 3 | برچسب VIP | واضح | binary و قضاوتی |
| 4 | عدد 0–100 | granularity بالا | دقت کاذب |
| 5 | فقط رنگ ردیف | سریع | معنا و accessibility ضعیف |

**انتخاب:** گزینه 1؛ این score فقط اولویت عملیاتی رابطه تجاری است.

## 3) Hover کارت محصول مشابه Laws of UX

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | transform: scale(1.012) translateY(-2px) با 190ms و فقط hover:hover + pointer:fine | feedback نرم بدون layout shift | باید subtle بماند |
| 2 | فقط translateY | بسیار امن | enlargement ندارد |
| 3 | فقط shadow | کم‌تحرک | feedback هندسی ضعیف |
| 4 | scale 1.05 | چشمگیر | collision و jump بصری |
| 5 | تغییر width/height | محسوس | reflow/layout shift؛ نباید استفاده شود |

**انتخاب:** گزینه 1. نام این الگو **Subtle Hover Scale / Card Lift micro-interaction** است. prefers-reduced-motion باید آن را عملاً خنثی کند.

## 4) حدود 500 محصول: search + grouping + pagination + overflow

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | Data-table toolbar با search باز، category/subcategory filters، pagination پایین، پیش‌فرض 50، overflow سه‌نقطه هر ردیف | شناخته‌شده، مقیاس‌پذیر، مناسب batch action | production باید server-side شود |
| 2 | Virtualized list بدون pagination | performance خوب | URL/page state و انتخاب گروهی پیچیده‌تر |
| 3 | Infinite scroll | مرور روان | admin find/edit و برگشت سخت |
| 4 | Accordion دسته‌ها | grouping خوب | جستجو/مقایسه چند دسته ضعیف |
| 5 | نمایش همه 500 ردیف | ساده | DOM و scan بسیار ضعیف |

**انتخاب:** گزینه 1. Carbon search/filter را در toolbar، pagination را پایین table و overflow را برای row-specific actions توصیه می‌کند. در prototype pagination client-side و در Laravel server-side خواهد شد.

## 5) Add/Edit محصول چهارزبانه + inference از دو رقم اول کد

| رتبه | گزینه | مزیت | ضعف / ریسک |
|---:|---|---|---|
| 1 | یک Product Editor مشترک Add/Edit؛ چهار نام fa/ar/en/ku؛ prefix دو رقم اول source-of-truth برای category/subcategory/spec schema؛ preview و reset | کمترین drift و خطای ورود | helper مرکزی لازم دارد |
| 2 | انتخاب دستی subcategory مستقل از code | flexible | ناسازگاری code/category |
| 3 | چهار فرم جدا برای زبان‌ها | واضح | تکرار و نگه‌داری بد |
| 4 | فقط نام زبان جاری | کوتاه | داده ناقص چندزبانه |
| 5 | inference فقط هنگام Save | کم‌جا | رفتار پنهان و غیرقابل پیش‌بینی |

**انتخاب:** گزینه 1. مدیر تغییر کد را می‌بیند و schema همان لحظه ظاهر می‌شود؛ specs قابل مشاهده/ویرایش‌اند و با «بازنشانی از کد» به template زیردسته برمی‌گردند.

# خودتشخیص‌ها

## A) Proxy clone و panel خالی
1. **منتخب:** toRaw + structuredClone + explicit empty state
2. JSON clone
3. shallow spread
4. edit مستقیم store
5. catch-and-ignore

## B) نام چندزبانه جدا از رکورد محصول
1. **منتخب:** product.names.fa/ar/en/ku روی record + fallback registry
2. فقط registry مرکزی
3. چهار فیلد UI بدون persistence
4. ترجمه runtime
5. فقط فارسی

## C) select-all با pagination
1. **منتخب:** select-all فقط روی صفحه قابل مشاهده؛ batch bar تعداد انتخاب‌شده را واضح می‌گوید
2. select-all کل dataset بدون توضیح
3. فقط تک‌انتخاب
4. long-press
5. بدون batch

## D) تضاد row overflow با batch mode
1. **منتخب:** وقتی selection فعال است overflow ردیف disable شود
2. هر دو هم‌زمان فعال
3. overflow فقط hover
4. همه actions inline
5. حذف row actions

## E) pagination prototype در برابر production
1. **منتخب:** client-side اکنون با state مدل سازگار با server-side؛ Laravel بعداً query-pagination
2. fake API layer
3. infinite scroll
4. همه 500 رکورد
5. page size ثابت بدون state

## Definition of Done
- Customer profile هرگز blank بدون پیام نباشد.
- Customer 360 روی mobile/tablet sheet و desktop modal باشد.
- اولویت ستاره‌ای 0–5 ذخیره شود.
- Product card hover با transform-only و reduced-motion باشد.
- Product admin search/filter/category/pagination(50)/overflow/batch داشته باشد.
- Product Add/Edit چهار نام زبانی و inference schema از prefix کد داشته باشد.
- Test 19 immutable بماند؛ خروجی جدید فقط /t/20 باشد.
