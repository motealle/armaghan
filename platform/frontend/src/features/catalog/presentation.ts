import type { Product } from '@/types/domain'

export function customerProductLabel(
  product:Pick<Product,'availability'>,
  subcategoryLabel:string,
  unavailableOrProducibleLabel:string,
):string{
  return product.availability==='available'?subcategoryLabel:unavailableOrProducibleLabel
}
