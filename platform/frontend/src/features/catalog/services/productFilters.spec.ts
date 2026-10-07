import {expect,it} from 'vitest'
import {readProductFilters,productFilterQuery,filterCatalogProducts} from './productFilters'
import type {Product} from '@/types/domain'

const filters={category:'all',subcategory:'all',availability:'all',query:''}
const product=(id:number,availability:Product['availability'],code=String(id)):Product=>({
  id,code,availability,name:'لباس کودک',names:{fa:'پیراهن جدید',en:'New shirt'},
  categoryCode:'1',subcategoryCode:'11',categoryName:'نوزادی',subcategoryName:'لباس نوزادی',specs:{locked:[],negotiable:[]},
})
const apply=(items:Product[],query='',extra:Partial<typeof filters>={})=>filterCatalogProducts(items,{...filters,query,...extra},p=>p.names?.fa??p.name,p=>p.subcategoryName)

it('puts available cards first while retaining order within both groups and leaving the source unchanged',()=>{
  const items=[product(1,'made_to_order'),product(2,'available'),product(3,'unavailable'),product(4,'available')]
  expect(apply(items).map(p=>p.id)).toEqual([2,4,1,3])
  expect(items.map(p=>p.id)).toEqual([1,2,3,4])
  expect(apply(items,'',{availability:'made_to_order'}).map(p=>p.id)).toEqual([1,3])
})

it('matches Persian/Arabic code digits, the code label, spelling variants and half-spaces',()=>{
  const items=[product(1,'available','1122')]
  for(const query of ['۱۱۲۲','١١٢٢','کد ۱۱۲۲','كد ١١٢٢','لباس كودك','پيراهن‌جديد'])expect(apply(items,query).map(p=>p.id)).toEqual([1])
  expect(apply(items,'9999')).toEqual([])
})

it('searches persisted names in every locale as well as the visible subcategory, retaining active filters',()=>{
  const items=[product(1,'made_to_order'),product(2,'available')]
  expect(apply(items,'New shirt').map(p=>p.id)).toEqual([2,1])
  expect(apply(items,'نوزادی',{availability:'available'}).map(p=>p.id)).toEqual([2])
  expect(apply(items,'پیراهن جدید',{subcategory:'12'})).toEqual([])
})

it('round-trips the selected subcategory, category, search and availability through reloadable query values',()=>{
  const selection={category:'1',subcategory:'11',availability:'made_to_order',query:'11001'}
  const query=productFilterQuery(selection)
  expect(readProductFilters(query)).toEqual(selection)
  expect(productFilterQuery({category:'all',subcategory:'all',availability:'all',query:''})).toEqual({})
  expect(readProductFilters({category:['1'],subcategory:'bad',availability:'bad',q:'x'.repeat(150)})).toEqual({category:'all',subcategory:'all',availability:'all',query:'x'.repeat(100)})
})
