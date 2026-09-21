export type UserRole = 'guest' | 'customer' | 'admin'

export type Availability = 'available' | 'unavailable' | 'made_to_order'

export type RequestPath =
  | 'simple'
  | 'available'
  | 'unavailable'
  | 'custom'
  | 'brand'
  | 'packaging'

export interface ProductSpecGroup {
  locked: string[]
  negotiable: string[]
}

export type ProductNames = Partial<Record<'fa'|'ar'|'en'|'ku',string>>

export interface Product {
  id: number
  code: string
  name: string
  names?: ProductNames
  categoryCode: '1' | '2' | '3'
  subcategoryCode: '11' | '12' | '21' | '22' | '31' | '32'
  categoryName: string
  subcategoryName: string
  availability: Availability
  image?: string
  gallery?: string[]
  specs: ProductSpecGroup
}

export interface Category {
  code: '1' | '2' | '3'
  name: string
  subtitle: string
  image?: string
  subcategories: Array<{ code: Product['subcategoryCode']; name: string }>
}

export interface CustomerSummary {
  id: number
  flag: string
  name: string
  whatsapp: string
  email: string
  activeOrder: string
  orderCount: number
}
