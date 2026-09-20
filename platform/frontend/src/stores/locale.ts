import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { detectInitialLocale, type Locale } from '@/services/localeDetection'

const MANUAL_KEY='armaghan:locale:manual'

const messages = {
  fa: {
    home:'خانه',products:'محصولات',production:'سفارش تولید',favorites:'مطلوب‌ها',tracking:'پیگیری',
    login:'ورود',logout:'خروج',manufacturer:'تولید و صادرات پوشاک',language:'زبان',
    productsTitle:'محصولات',productsHelp:'دسته و زیردسته را انتخاب کنید؛ سپس محصول را مقایسه کنید.',
    search:'جستجو با نام یا کد محصول',allStatuses:'همه وضعیت‌ها',allSubs:'همه زیردسته‌ها',
    available:'موجود',unavailable:'ناموجود',madeToOrder:'تولیدپذیر',
    whatsapp:'واتساپ',favorite:'مطلوب',details:'مشخصات',
    recommended:'محصولات پیشنهادی',allProducts:'همه محصولات',categories:'دسته‌بندی محصولات',
    categoryHelp:'سه دسته اصلی، شش زیردسته و مسیر روشن برای مشتری غیرمتخصص.',
    sourceNoteTitle:'تصاویر آزمایشی دیجی‌کالا',sourceNote:'تصاویر این نسخه از کاتالوگ دیجی‌کالا و بر مبنای مجوز اعلام‌شده توسط مالک پروژه برای نمونه‌سازی استفاده می‌شوند.',
    imageSources:'مشاهده منابع تصاویر',brandIntro:'معرفی تولیدکننده',
    productionTitle:'سفارش تولید',productionHelp:'مسیر مرحله‌ای برای انتخاب نوع تولید، دسته، زیردسته و مشخصات.',
    favoriteTitle:'مطلوب‌ها',favoriteHelp:'فهرست محصولات موردنظر شما.',emptyFavorites:'هنوز محصولی به مطلوب‌ها اضافه نشده است.',
    account:'پیگیری و حساب',accountHelp:'سفارشات، تایملاین، مطلوب‌ها و مدیریت مشتری.',
    tapImage:'برای بزرگ‌نمایی تصویر لمس کنید',loginTitle:'ورود به حساب',username:'نام کاربری',password:'رمز عبور',signIn:'ورود',demoHint:'آزمایشی: مدیر 1/1 · مشتری 2/2',invalidLogin:'نام کاربری یا رمز آزمایشی نادرست است.',google:'ورود با Google',magicLink:'ورود با لینک خصوصی',
  },
  ar: {
    home:'الرئيسية',products:'المنتجات',production:'طلب إنتاج',favorites:'المفضلة',tracking:'المتابعة',
    login:'دخول',logout:'خروج',manufacturer:'إنتاج وتصدير الملابس',language:'اللغة',
    productsTitle:'المنتجات',productsHelp:'اختر الفئة والفئة الفرعية ثم قارن المنتجات.',
    search:'ابحث بالاسم أو الكود',allStatuses:'كل الحالات',allSubs:'كل الفئات الفرعية',
    available:'متوفر',unavailable:'غير متوفر',madeToOrder:'قابل للإنتاج',
    whatsapp:'واتساب',favorite:'مفضلة',details:'المواصفات',
    recommended:'منتجات مقترحة',allProducts:'كل المنتجات',categories:'فئات المنتجات',
    categoryHelp:'ثلاث فئات رئيسية وست فئات فرعية بمسار واضح.',
    sourceNoteTitle:'صور تجريبية من ديجي‌كالا',sourceNote:'تُستخدم صور كتالوج ديجي‌كالا في هذا النموذج وفق التفويض الذي أفاد به مالك المشروع.',
    imageSources:'مصادر الصور',brandIntro:'تعريف المنتج',
    productionTitle:'طلب إنتاج',productionHelp:'مسار تدريجي لاختيار نوع الإنتاج والفئة والمواصفات.',
    favoriteTitle:'المفضلة',favoriteHelp:'قائمة المنتجات التي اخترتها.',emptyFavorites:'لم تضف أي منتج بعد.',
    account:'الحساب والمتابعة',accountHelp:'الطلبات والخط الزمني والمفضلة وإدارة العملاء.',
    tapImage:'اضغط لتكبير الصورة',loginTitle:'تسجيل الدخول',username:'اسم المستخدم',password:'كلمة المرور',signIn:'دخول',demoHint:'تجريبي: المدير 1/1 · العميل 2/2',invalidLogin:'اسم المستخدم أو كلمة المرور التجريبية غير صحيحة.',google:'الدخول عبر Google',magicLink:'الدخول برابط خاص',
  },
  en: {
    home:'Home',products:'Products',production:'Production',favorites:'Favorites',tracking:'Tracking',
    login:'Sign in',logout:'Sign out',manufacturer:'Garment production & export',language:'Language',
    productsTitle:'Products',productsHelp:'Choose a category and subcategory, then compare products.',
    search:'Search by product name or code',allStatuses:'All statuses',allSubs:'All subcategories',
    available:'Available',unavailable:'Unavailable',madeToOrder:'Made to order',
    whatsapp:'WhatsApp',favorite:'Favorite',details:'Specs',
    recommended:'Recommended products',allProducts:'All products',categories:'Product categories',
    categoryHelp:'Three main categories and six clear subcategories.',
    sourceNoteTitle:'Digikala prototype imagery',sourceNote:'This prototype uses Digikala catalog images under the authorization reported by the project owner.',
    imageSources:'Image sources',brandIntro:'Manufacturer profile',
    productionTitle:'Production request',productionHelp:'A guided flow for production type, category, subcategory and specifications.',
    favoriteTitle:'Favorites',favoriteHelp:'Products you want to keep for later.',emptyFavorites:'No favorite products yet.',
    account:'Account & tracking',accountHelp:'Orders, timeline, favorites and customer management.',
    tapImage:'Tap image to enlarge',loginTitle:'Sign in to your account',username:'Username',password:'Password',signIn:'Sign in',demoHint:'Demo: admin 1/1 · customer 2/2',invalidLogin:'The demo username or password is incorrect.',google:'Continue with Google',magicLink:'Sign in with private link',
  },
  ku: {
    home:'ماڵەوە',products:'بەرهەمەکان',production:'داواکاری بەرهەم',favorites:'دڵخوازەکان',tracking:'بەدواداچوون',
    login:'چوونەژوورەوە',logout:'چوونەدەرەوە',manufacturer:'بەرهەمهێنان و هەناردەی جل و بەرگ',language:'زمان',
    productsTitle:'بەرهەمەکان',productsHelp:'پۆل و ژێرپۆل هەڵبژێرە و بەرهەمەکان بەراورد بکە.',
    search:'گەڕان بە ناو یان کۆد',allStatuses:'هەموو دۆخەکان',allSubs:'هەموو ژێرپۆلەکان',
    available:'بەردەست',unavailable:'بەردەست نییە',madeToOrder:'بۆ بەرهەمهێنان',
    whatsapp:'واتساپ',favorite:'دڵخواز',details:'تایبەتمەندی',
    recommended:'بەرهەمی پێشنیارکراو',allProducts:'هەموو بەرهەمەکان',categories:'پۆلەکانی بەرهەم',
    categoryHelp:'سێ پۆلی سەرەکی و شەش ژێرپۆلی ڕوون.',
    sourceNoteTitle:'وێنەی نموونەی دیجی‌کالا',sourceNote:'لە نموونەکەدا وێنەکانی کاتەلۆگی دیجی‌کالا بە پشتبەستن بە مۆڵەتی ڕاگەیەنراوی خاوەنی پڕۆژە بەکاردێن.',
    imageSources:'سەرچاوەی وێنەکان',brandIntro:'ناساندنی بەرهەمهێنەر',
    productionTitle:'داواکاری بەرهەمهێنان',productionHelp:'ڕێگای هەنگاو بە هەنگاو بۆ جۆری بەرهەمهێنان و تایبەتمەندی.',
    favoriteTitle:'دڵخوازەکان',favoriteHelp:'لیستی بەرهەمە هەڵبژێردراوەکانت.',emptyFavorites:'هێشتا بەرهەمێکت زیاد نەکردووە.',
    account:'هەژمار و بەدواداچوون',accountHelp:'داواکاری، هێڵی کات، دڵخوازەکان و بەڕێوەبردنی کڕیار.',
    tapImage:'بۆ گەورەکردنەوە وێنەکە بکەوە',loginTitle:'چوونەژوورەوەی هەژمار',username:'ناوی بەکارهێنەر',password:'وشەی نهێنی',signIn:'چوونەژوورەوە',demoHint:'نموونە: بەڕێوەبەر 1/1 · کڕیار 2/2',invalidLogin:'ناوی بەکارهێنەر یان وشەی نهێنی هەڵەیە.',google:'چوونەژوورەوە بە Google',magicLink:'چوونەژوورەوە بە لینکی تایبەت',
  },
} as const

type MessageKey=keyof typeof messages.fa

export const useLocaleStore=defineStore('locale',()=>{
  const locale=ref<Locale>('fa')
  const initialized=ref(false)
  const direction=computed(()=>'en'===locale.value?'ltr':'rtl')
  const htmlLang=computed(()=>locale.value==='ku'?'ckb':locale.value)

  function apply(){
    document.documentElement.lang=htmlLang.value
    document.documentElement.dir=direction.value
  }
  function setManual(value:Locale){
    locale.value=value
    localStorage.setItem(MANUAL_KEY,value)
    apply()
  }
  async function initialize(){
    const manual=localStorage.getItem(MANUAL_KEY)
    if(manual==='fa'||manual==='ar'||manual==='en'||manual==='ku')locale.value=manual
    else locale.value=await detectInitialLocale()
    initialized.value=true
    apply()
  }
  function t(key:MessageKey):string{
    return messages[locale.value][key] ?? messages.fa[key]
  }
  return{locale,initialized,direction,htmlLang,t,setManual,initialize}
})
