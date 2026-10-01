export type BrandTokenId='green'|'blue'|'mint'|'white'|'gold'

export interface BrandToken{
  id:BrandTokenId
  label:string
  value:string
  cssVar:string
}

export const brandTokens:BrandToken[]=[
  {id:'green',label:'سبز برند',value:'#21946A',cssVar:'--brand-green'},
  {id:'blue',label:'آبی زمینه لوگو',value:'#0714C2',cssVar:'--brand-blue'},
  {id:'mint',label:'مینت روشن',value:'#C8E3DB',cssVar:'--brand-mint'},
  {id:'white',label:'سفید',value:'#FFFFFF',cssVar:'--brand-white'},
  {id:'gold',label:'طلایی',value:'#FFB514',cssVar:'--brand-gold'},
]

const tokenIds=new Set<BrandTokenId>(brandTokens.map(item=>item.id))

export function isBrandTokenId(value:unknown):value is BrandTokenId{
  return typeof value==='string'&&tokenIds.has(value as BrandTokenId)
}

export function tokenVar(id:BrandTokenId):string{
  return `var(--brand-${id})`
}
