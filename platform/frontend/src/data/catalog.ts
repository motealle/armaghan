import type { Category, CustomerSummary, Product, ProductSpecGroup } from '@/types/domain'
import { digikalaMediaByProductCode } from '@/data/digikalaMedia.generated'

// Test 23 category navigation uses user-provided optimized thumbnails; product galleries keep canonical final media.
export const categories: Category[] = [
  {
    code: '1',
    name: 'نوزادی',
    subtitle: 'لباس و پتوی نوزادی',
    image: './images/category-navigation/category-baby.webp',
    subcategories: [
      { code: '11', name: 'لباس نوزادی' },
      { code: '12', name: 'پتوی نوزادی' },
    ],
  },
  {
    code: '2',
    name: 'بچگانه',
    subtitle: 'دخترانه و پسرانه',
    image: './images/category-navigation/category-kids.webp',
    subcategories: [
      { code: '21', name: 'دخترانه' },
      { code: '22', name: 'پسرانه' },
    ],
  },
  {
    code: '3',
    name: 'زنانه',
    subtitle: 'تونیک و لباس راحتی',
    image: './images/category-navigation/category-women.webp',
    subcategories: [
      { code: '31', name: 'زیرسارافون (تونیک)' },
      { code: '32', name: 'لباس راحتی (ورزشی)' },
    ],
  },
]

type SubMeta = {
  categoryCode: Product['categoryCode']
  categoryName: string
  subcategoryName: string
  image: string
  fallbackImage: string
  specs: ProductSpecGroup
}

export const subMeta: Record<Product['subcategoryCode'], SubMeta> = {
  '11': {
    categoryCode: '1',
    categoryName: 'نوزادی',
    subcategoryName: 'لباس نوزادی',
    image: './images/final/categories/category-baby.webp',
    fallbackImage: './images/web-stock/sub-11.webp',
    specs: { locked: ['رنگ', 'جنس'], negotiable: ['طرح ظاهری', 'مشخصات مدل', 'اقلام محصول', 'سایز', 'رده سنی'] },
  },
  '12': {
    categoryCode: '1',
    categoryName: 'نوزادی',
    subcategoryName: 'پتوی نوزادی',
    image: './images/final/categories/category-baby.webp',
    fallbackImage: './images/web-stock/sub-12.webp',
    specs: { locked: ['ابعاد'], negotiable: ['طرح ظاهری', 'فرم محصول', 'مشخصات مدل', 'ترکیب رنگ', 'جنس', 'تعداد در پک'] },
  },
  '21': {
    categoryCode: '2',
    categoryName: 'بچگانه',
    subcategoryName: 'دخترانه',
    image: './images/final/categories/category-kids.webp',
    fallbackImage: './images/web-stock/sub-21.webp',
    specs: { locked: [], negotiable: ['طرح ظاهری', 'اجزای محصول', 'فرم محصول', 'مشخصات مدل', 'نوع تن‌خور', 'سایز', 'رده سنی', 'رنگ', 'جنس', 'تعداد در پک'] },
  },
  '22': {
    categoryCode: '2',
    categoryName: 'بچگانه',
    subcategoryName: 'پسرانه',
    image: './images/final/categories/category-kids.webp',
    fallbackImage: './images/web-stock/sub-22.webp',
    specs: { locked: [], negotiable: ['طرح ظاهری', 'اجزای محصول', 'فرم محصول', 'مشخصات مدل', 'نوع تن‌خور', 'سایز', 'رده سنی', 'رنگ', 'جنس', 'تعداد در پک'] },
  },
  '31': {
    categoryCode: '3',
    categoryName: 'زنانه',
    subcategoryName: 'زیرسارافون (تونیک)',
    image: './images/final/categories/category-women-modest.webp',
    fallbackImage: './images/web-stock/sub-31.webp',
    specs: { locked: ['سایز', 'رنگ', 'جنس', 'تعداد در پک'], negotiable: ['طرح ظاهری', 'فرم محصول'] },
  },
  '32': {
    categoryCode: '3',
    categoryName: 'زنانه',
    subcategoryName: 'لباس راحتی (ورزشی)',
    image: './images/final/categories/category-women-modest.webp',
    fallbackImage: './images/web-stock/sub-32.webp',
    specs: { locked: ['سایز', 'تعداد در پک'], negotiable: ['طرح ظاهری', 'اجزای محصول', 'فرم محصول', 'مشخصات محصول', 'نوع تن‌خور', 'جنس', 'رنگ'] },
  },
}

export function productDefaultsFromCode(code:string){
  const prefix=code.trim().slice(0,2) as Product['subcategoryCode']
  const meta=subMeta[prefix]
  if(!meta)return null
  return {
    categoryCode:meta.categoryCode,
    categoryName:meta.categoryName,
    subcategoryCode:prefix,
    subcategoryName:meta.subcategoryName,
    specs:structuredClone(meta.specs),
  }
}

function product(
  id: number,
  code: string,
  name: string,
  subcategoryCode: Product['subcategoryCode'],
  availability: Product['availability'],
): Product {
  const meta = subMeta[subcategoryCode]
  const legacyFallback = digikalaMediaByProductCode[code]?.[0] ?? meta.fallbackImage
  const gallery = [meta.image, './images/final/details/fabric-detail.webp', legacyFallback]
  return {
    id,
    code,
    name,
    categoryCode: meta.categoryCode,
    subcategoryCode,
    categoryName: meta.categoryName,
    subcategoryName: meta.subcategoryName,
    availability,
    gallery,
    specs: structuredClone(meta.specs),
  }
}

export const products: Product[] = [
  product(1, '11001', 'ست نوزادی آرام', '11', 'available'),
  product(2, '11002', 'بادی نوزادی پایه', '11', 'available'),
  product(3, '11003', 'ست بیمارستانی نوزاد', '11', 'made_to_order'),
  product(4, '12001', 'پتوی نوزادی نرم', '12', 'available'),
  product(5, '12002', 'پتوی دورپیچ نوزاد', '12', 'available'),
  product(6, '12003', 'پتوی سبک چهارفصل', '12', 'made_to_order'),
  product(7, '21001', 'ست دخترانه روزمره', '21', 'available'),
  product(8, '21002', 'تونیک دخترانه', '21', 'made_to_order'),
  product(9, '21003', 'ست راحتی دخترانه', '21', 'available'),
  product(10, '22001', 'ست پسرانه شهری', '22', 'unavailable'),
  product(11, '22002', 'تی‌شرت و شلوارک پسرانه', '22', 'available'),
  product(12, '22003', 'ست راحتی پسرانه', '22', 'made_to_order'),
  product(13, '31001', 'تونیک زنانه مودست', '31', 'available'),
  product(14, '31002', 'زیرسارافون مینیمال', '31', 'made_to_order'),
  product(15, '31003', 'تونیک روزمره بلند', '31', 'available'),
  product(16, '32001', 'ست راحتی زنانه', '32', 'available'),
  product(17, '32002', 'ست ورزشی مودست', '32', 'made_to_order'),
  product(18, '32003', 'بلوز و شلوار راحتی', '32', 'available'),
]

export const demoCustomers: CustomerSummary[] = [
  { id: 1, flag: '🇮🇶', name: 'نمونه مشتری بغداد', whatsapp: '+964 7XX XXX XXXX', email: 'buyer@example.test', activeOrder: 'در حال تولید', orderCount: 3 },
  { id: 2, flag: '🇦🇪', name: 'نمونه مشتری دبی', whatsapp: '+971 5X XXX XXXX', email: 'trade@example.test', activeOrder: 'بدون سفارش فعال', orderCount: 1 },
  { id: 3, flag: '🇹🇷', name: 'نمونه مشتری استانبول', whatsapp: '+90 5XX XXX XXXX', email: 'store@example.test', activeOrder: 'در انتظار پیش‌پرداخت', orderCount: 2 },
  { id: 4, flag: '🇶🇦', name: 'نمونه مشتری دوحه', whatsapp: '+974 3XXX XXXX', email: 'doha@example.test', activeOrder: 'آماده ارسال', orderCount: 4 },
  { id: 5, flag: '🇩🇪', name: 'نمونه مشتری برلین', whatsapp: '+49 15X XXXXXXX', email: 'berlin@example.test', activeOrder: 'در حال مذاکره', orderCount: 2 },
  { id: 6, flag: '🇴🇲', name: 'نمونه مشتری مسقط', whatsapp: '+968 9XXX XXXX', email: 'muscat@example.test', activeOrder: 'بدون سفارش فعال', orderCount: 1 },
]
