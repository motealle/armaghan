import type { Product, RequestPath } from '@/types/domain'

export const SELLER_WHATSAPP = '989381009231'

const titles: Record<RequestPath, string> = {
  simple: 'خرید ساده',
  available: 'خرید موجود',
  unavailable: 'خرید ناموجود',
  custom: 'تولید سفارشی',
  brand: 'سفارش تولید با برند',
  packaging: 'سفارش تولید با بسته‌بندی',
}

export function requestPathTitle(path: RequestPath): string {
  return titles[path]
}

export function buildProductMessage(product: Product, path: RequestPath): string {
  return [
    titles[path],
    `محصول: ${product.name}`,
    `کد محصول: ${product.code}`,
    `دسته: ${product.categoryName} / ${product.subcategoryName}`,
    `وضعیت: ${product.availability === 'available' ? 'موجود' : product.availability === 'unavailable' ? 'ناموجود' : 'تولید سفارشی'}`,
  ].join('\n')
}

export function buildProductionMessage(input: {
  path: Extract<RequestPath, 'custom' | 'brand' | 'packaging'>
  category: string
  subcategory: string
  negotiable: string[]
  locked: string[]
  note?: string
}): string {
  return [
    titles[input.path],
    `دسته اصلی: ${input.category}`,
    `زیردسته: ${input.subcategory}`,
    `قابل مذاکره: ${input.negotiable.length ? input.negotiable.join('، ') : '—'}`,
    `ثابت: ${input.locked.length ? input.locked.join('، ') : '—'}`,
    input.note?.trim() ? `توضیح: ${input.note.trim()}` : '',
  ].filter(Boolean).join('\n')
}

export function whatsappUrl(message: string): string {
  return `https://wa.me/${SELLER_WHATSAPP}?text=${encodeURIComponent(message)}`
}
