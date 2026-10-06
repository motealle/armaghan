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
