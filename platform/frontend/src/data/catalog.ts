import type { Category, CustomerSummary, Product } from '@/types/domain'

export const categories: Category[] = [
  {
    code: '1',
    name: 'نوزادی',
    subtitle: 'لباس و پتوی نوزادی',
    image: './images/category-baby.webp',
    subcategories: [
      { code: '11', name: 'لباس نوزادی' },
      { code: '12', name: 'پتوی نوزادی' },
    ],
  },
  {
    code: '2',
    name: 'بچگانه',
    subtitle: 'دخترانه و پسرانه',
    image: './images/category-kids.webp',
    subcategories: [
      { code: '21', name: 'دخترانه' },
      { code: '22', name: 'پسرانه' },
    ],
  },
  {
    code: '3',
    name: 'زنانه',
    subtitle: 'تونیک و لباس راحتی',
    image: './images/category-women.webp',
    subcategories: [
      { code: '31', name: 'زیرسارافون (تونیک)' },
      { code: '32', name: 'لباس راحتی (ورزشی)' },
    ],
  },
]

export const products: Product[] = [
  {
    id: 1,
    code: '11001',
    name: 'ست نوزادی آرام',
    categoryCode: '1',
    subcategoryCode: '11',
    categoryName: 'نوزادی',
    subcategoryName: 'لباس نوزادی',
    availability: 'available',
    image: './images/product-11001.webp',
    specs: {
      locked: ['رنگ', 'جنس'],
      negotiable: ['طرح ظاهری', 'مشخصات مدل', 'اقلام محصول', 'سایز', 'رده سنی'],
    },
  },
  {
    id: 2,
    code: '12001',
    name: 'پتوی نوزادی نرم',
    categoryCode: '1',
    subcategoryCode: '12',
    categoryName: 'نوزادی',
    subcategoryName: 'پتوی نوزادی',
    availability: 'available',
    image: './images/product-12001.webp',
    specs: {
      locked: ['ابعاد'],
      negotiable: ['طرح ظاهری', 'فرم محصول', 'مشخصات مدل', 'ترکیب رنگ', 'جنس', 'تعداد در پک'],
    },
  },
  {
    id: 3,
    code: '21001',
    name: 'ست دخترانه روزمره',
    categoryCode: '2',
    subcategoryCode: '21',
    categoryName: 'بچگانه',
    subcategoryName: 'دخترانه',
    availability: 'available',
    image: './images/product-21001.webp',
    specs: {
      locked: [],
      negotiable: ['طرح ظاهری', 'اجزای محصول', 'فرم محصول', 'مشخصات مدل', 'نوع تن‌خور', 'سایز', 'رده سنی', 'رنگ', 'جنس', 'تعداد در پک'],
    },
  },
  {
    id: 4,
    code: '22001',
    name: 'ست پسرانه شهری',
    categoryCode: '2',
    subcategoryCode: '22',
    categoryName: 'بچگانه',
    subcategoryName: 'پسرانه',
    availability: 'unavailable',
    image: './images/product-22001.webp',
    specs: {
      locked: [],
      negotiable: ['طرح ظاهری', 'اجزای محصول', 'فرم محصول', 'مشخصات مدل', 'نوع تن‌خور', 'سایز', 'رده سنی', 'رنگ', 'جنس', 'تعداد در پک'],
    },
  },
  {
    id: 5,
    code: '31001',
    name: 'تونیک زنانه مودست',
    categoryCode: '3',
    subcategoryCode: '31',
    categoryName: 'زنانه',
    subcategoryName: 'زیرسارافون (تونیک)',
    availability: 'available',
    image: './images/product-31001.webp',
    specs: {
      locked: ['سایز', 'رنگ', 'جنس', 'تعداد در پک'],
      negotiable: ['طرح ظاهری', 'فرم محصول'],
    },
  },
  {
    id: 6,
    code: '32001',
    name: 'ست راحتی زنانه',
    categoryCode: '3',
    subcategoryCode: '32',
    categoryName: 'زنانه',
    subcategoryName: 'لباس راحتی (ورزشی)',
    availability: 'available',
    image: './images/product-32001.webp',
    specs: {
      locked: ['سایز', 'تعداد در پک'],
      negotiable: ['طرح ظاهری', 'اجزای محصول', 'فرم محصول', 'مشخصات محصول', 'نوع تن‌خور', 'جنس', 'رنگ'],
    },
  },
]

export const demoCustomers: CustomerSummary[] = [
  { id: 1, flag: '🇮🇶', name: 'نمونه مشتری بغداد', whatsapp: '+964 7XX XXX XXXX', email: 'buyer@example.test', activeOrder: 'در حال تولید', orderCount: 3 },
  { id: 2, flag: '🇦🇪', name: 'نمونه مشتری دبی', whatsapp: '+971 5X XXX XXXX', email: 'trade@example.test', activeOrder: 'بدون سفارش فعال', orderCount: 1 },
  { id: 3, flag: '🇹🇷', name: 'نمونه مشتری استانبول', whatsapp: '+90 5XX XXX XXXX', email: 'store@example.test', activeOrder: 'در انتظار پیش‌پرداخت', orderCount: 2 },
]
