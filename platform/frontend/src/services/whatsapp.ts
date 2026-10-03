import type { Product, RequestPath } from '@/types/domain'
import type { Locale } from '@/services/localeDetection'

export const SELLER_WHATSAPP='989933509793'
export const PREVIOUS_SELLER_WHATSAPP='989381009231'

const titles:Record<Locale,Record<RequestPath,string>>={
 fa:{simple:'خرید',available:'درخواست تغییر',unavailable:'درخواست تولید',custom:'تولید سفارشی',brand:'تولید با برند اختصاصی',packaging:'تولید با بسته‌بندی اختصاصی'},
 ar:{simple:'شراء',available:'طلب تعديل',unavailable:'طلب غير المتوفر',custom:'إنتاج مخصص',brand:'إنتاج بعلامتك التجارية',packaging:'إنتاج بتغليف مخصص'},
 en:{simple:'Purchase',available:'Request changes',unavailable:'Request unavailable product',custom:'Custom production',brand:'Production with buyer brand',packaging:'Production with custom packaging'},
 ku:{simple:'کڕین',available:'داواکاری گۆڕانکاری',unavailable:'داواکاری نابەردەست',custom:'بەرهەمهێنانی تایبەت',brand:'بەرهەمهێنان بە براند',packaging:'بەرهەمهێنان بە پاکەت'},
}
const labels={
 fa:{product:'محصول',code:'کد محصول',category:'دسته',status:'وضعیت',available:'موجود',unavailable:'ناموجود',made:'تولید سفارشی',main:'دسته اصلی',sub:'زیردسته',neg:'قابل مذاکره',locked:'ثابت',note:'توضیح'},
 ar:{product:'المنتج',code:'كود المنتج',category:'الفئة',status:'الحالة',available:'متوفر',unavailable:'غير متوفر',made:'إنتاج مخصص',main:'الفئة الرئيسية',sub:'الفئة الفرعية',neg:'قابل للتفاوض',locked:'ثابت',note:'ملاحظة'},
 en:{product:'Product',code:'Product code',category:'Category',status:'Status',available:'Available',unavailable:'Unavailable',made:'Made to order',main:'Main category',sub:'Subcategory',neg:'Negotiable',locked:'Fixed',note:'Note'},
 ku:{product:'بەرهەم',code:'کۆدی بەرهەم',category:'پۆل',status:'دۆخ',available:'بەردەست',unavailable:'بەردەست نییە',made:'بۆ بەرهەمهێنان',main:'پۆلی سەرەکی',sub:'ژێرپۆل',neg:'قابل گفتوگۆ',locked:'جێگیر',note:'تێبینی'},
} as const

export function requestPathTitle(path:RequestPath,locale:Locale='fa'):string{return titles[locale][path]}

export function buildProductMessage(product:Product,path:RequestPath,locale:Locale='fa'):string{
 const l=labels[locale]
 const status=product.availability==='available'?l.available:l.made
 return[
  titles[locale][path],
  `${l.product}: ${product.subcategoryName}`,
  `${l.code}: ${product.code}`,
  `${l.category}: ${product.categoryName} / ${product.subcategoryName}`,
  `${l.status}: ${status}`,
 ].join('\n')
}

export function buildProductionMessage(input:{
 path:Extract<RequestPath,'custom'|'brand'|'packaging'>
 category:string
 subcategory:string
 negotiable:string[]
 locked:string[]
 note?:string
 locale?:Locale
}):string{
 const locale=input.locale??'fa'
 const l=labels[locale]
 return[
  titles[locale][input.path],
  `${l.main}: ${input.category}`,
  `${l.sub}: ${input.subcategory}`,
  `${l.neg}: ${input.negotiable.length?input.negotiable.join('، '):'—'}`,
  `${l.locked}: ${input.locked.length?input.locked.join('، '):'—'}`,
  input.note?.trim()?`${l.note}: ${input.note.trim()}`:'',
 ].filter(Boolean).join('\n')
}

export function whatsappUrl(message:string):string{
 return `https://wa.me/${SELLER_WHATSAPP}?text=${encodeURIComponent(message)}`
}
