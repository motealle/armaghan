import type { Product } from '@/types/domain'

export interface ProductFilters {category:string;subcategory:string;availability:string;query:string}
type Query=Record<string,unknown>
const text=(value:unknown)=>typeof value==='string'?value:''
const code=(value:unknown)=>/^\d{1,6}$/.test(text(value))?text(value):'all'
export function readProductFilters(query:Query):ProductFilters{
  return {category:code(query.category),subcategory:code(query.subcategory),
    availability:['available','made_to_order'].includes(text(query.availability))?text(query.availability):'all',
    query:text(query.q).slice(0,100)}
}
export function productFilterQuery(filters:ProductFilters):Record<string,string>{
  const query:Record<string,string>={}
  if(filters.category!=='all')query.category=filters.category
  if(filters.subcategory!=='all')query.subcategory=filters.subcategory
  if(filters.availability!=='all')query.availability=filters.availability
  if(filters.query)query.q=filters.query.slice(0,100)
  return query
}

function normalizeSearch(value:string):string{
  return value.normalize('NFKC').toLowerCase()
    .replace(/[۰-۹]/g,digit=>String('۰۱۲۳۴۵۶۷۸۹'.indexOf(digit)))
    .replace(/[٠-٩]/g,digit=>String('٠١٢٣٤٥٦٧٨٩'.indexOf(digit)))
    .replace(/[يى]/g,'ی').replace(/ك/g,'ک')
    .replace(/[\u064B-\u065F\u0670\u0640\u200E\u200F\u202A-\u202E\u2066-\u2069]/g,'')
    .replace(/[\s\u200C\u200D]+/g,' ').trim()
}

export function filterCatalogProducts(products:Product[],filters:ProductFilters,name:(product:Product)=>string,subcategoryName:(product:Product)=>string):Product[]{
  const needle=normalizeSearch(filters.query).replace(/^کد\s*(?=\d)/,'')
  const available:Product[]=[]
  const producible:Product[]=[]
  for(const product of products){
    if(filters.category!=='all'&&product.categoryCode!==filters.category)continue
    if(filters.subcategory!=='all'&&product.subcategoryCode!==filters.subcategory)continue
    if(filters.availability==='available'&&product.availability!=='available')continue
    if(filters.availability==='made_to_order'&&product.availability==='available')continue
    if(needle){
      const haystack=normalizeSearch([name(product),product.name,...Object.values(product.names??{}),product.code,subcategoryName(product),product.subcategoryName].join(' '))
      if(!haystack.includes(needle))continue
    }
    const group=product.availability==='available'?available:producible
    group.push(product)
  }
  return available.concat(producible)
}
