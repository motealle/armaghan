import { categories as seedCategories, products as seedProducts, subMeta } from '@/data/catalog'
import type { Category, Product, ProductNames } from '@/types/domain'

type LocalizedNames={
  fa:string|null
  ar:string|null
  en:string|null
  ku:string|null
}

export interface PublicCatalogCategory{
  code:string
  names:LocalizedNames
  subcategories:Array<{
    code:string
    names:LocalizedNames
  }>
}

export interface PublicCatalogMedia{
  id:string
  url:string
  thumb_url:string
  detail_url?:string
}

export interface PublicCatalogProduct{
  id:number
  code:string
  names:LocalizedNames
  availability:Product['availability']
  specifications?:Array<{key:string;locked:boolean;labels:LocalizedNames;value_text:string|null}>
  sort_order:number
  media?:PublicCatalogMedia[]
  category:null|{code:string;names:LocalizedNames}
  subcategory:null|{code:string;names:LocalizedNames}
}

interface CategoryResponse{
  data:PublicCatalogCategory[]
  catalog:{
    managed_category_codes:string[]
    managed_subcategory_codes:string[]
  }
}

interface ProductResponse{
  data:PublicCatalogProduct[]
  meta:{
    current_page:number
    last_page:number
  }
  catalog:{
    managed_codes:string[]
  }
}

export interface CatalogSnapshot{
  categories:PublicCatalogCategory[]
  products:PublicCatalogProduct[]
  managedCategoryCodes:string[]
  managedSubcategoryCodes:string[]
  managedProductCodes:string[]
}

export class CatalogApiError extends Error{
  constructor(public readonly status:number,message:string){
    super(message)
    this.name='CatalogApiError'
  }
}

const rawBase=(import.meta.env.VITE_ARMAGHAN_API_BASE as string|undefined)?.trim()??''
const API_BASE=(rawBase||'/backend').replace(/\/$/,'')
const maxProductPages=20
const PRODUCT_MEDIA_CACHE_REV='20261005-card-hotfix-1'
function freshProductMediaUrl(value?:string){
  const url=value?.trim()??''
  if(!url||!url.includes('/backend/api/catalog/media/'))return url
  return url+(url.includes('?')?'&':'?')+'v='+PRODUCT_MEDIA_CACHE_REV
}

function categoryCode(value:string):value is Category['code']{
  return value==='1'||value==='2'||value==='3'
}

function subcategoryCode(value:string):value is Product['subcategoryCode']{
  return value==='11'||value==='12'||value==='21'||value==='22'||value==='31'||value==='32'
}

function productNames(remote:LocalizedNames,fallback?:Product):ProductNames{
  return {
    fa:remote.fa?.trim()||fallback?.names?.fa||fallback?.name||undefined,
    ar:remote.ar?.trim()||fallback?.names?.ar||undefined,
    en:remote.en?.trim()||fallback?.names?.en||undefined,
    ku:remote.ku?.trim()||fallback?.names?.ku||undefined,
  }
}

async function requestJson<T>(path:string,signal?:AbortSignal):Promise<T>{
  const response=await fetch(API_BASE+path,{
    headers:{Accept:'application/json'},
    credentials:'same-origin',
    cache:'no-store',
    signal,
  })

  let payload:unknown=null
  try{payload=await response.json()}catch{}

  if(!response.ok){
    const message=(
      payload&&typeof payload==='object'&&'message' in payload&&typeof (payload as {message?:unknown}).message==='string'
    )?(payload as {message:string}).message:'Catalog request failed ('+response.status+').'
    throw new CatalogApiError(response.status,message)
  }

  return payload as T
}

async function fetchAllProducts(signal?:AbortSignal):Promise<{
  products:PublicCatalogProduct[]
  managedCodes:string[]
}>{
  const first=await requestJson<ProductResponse>('/api/catalog/products?per_page=100&page=1',signal)
  const lastPage=Math.max(1,Number(first.meta?.last_page??1))

  if(lastPage>maxProductPages)throw new CatalogApiError(413,'Catalog exceeds the staged client sync limit.')

  const products=[...first.data]
  for(let page=2;page<=lastPage;page+=1){
    const next=await requestJson<ProductResponse>('/api/catalog/products?per_page=100&page='+page,signal)
    products.push(...next.data)
  }

  return{
    products,
    managedCodes:Array.isArray(first.catalog?.managed_codes)?first.catalog.managed_codes:[],
  }
}

export async function fetchCatalogSnapshot(signal?:AbortSignal):Promise<CatalogSnapshot>{
  const [categoryPayload,productPayload]=await Promise.all([
    requestJson<CategoryResponse>('/api/catalog/categories',signal),
    fetchAllProducts(signal),
  ])

  return{
    categories:categoryPayload.data,
    products:productPayload.products,
    managedCategoryCodes:categoryPayload.catalog?.managed_category_codes??[],
    managedSubcategoryCodes:categoryPayload.catalog?.managed_subcategory_codes??[],
    managedProductCodes:productPayload.managedCodes,
  }
}

function mergeProduct(remote:PublicCatalogProduct,fallback?:Product):Product|null{
  const remoteSubcategory=remote.subcategory?.code??remote.code.trim().slice(0,2)
  if(!subcategoryCode(remoteSubcategory))return null

  const meta=subMeta[remoteSubcategory]
  const names=productNames(remote.names,fallback)
  const name=names.fa||fallback?.name||meta.subcategoryName
  const backendMedia=(remote.media??[])
    .map(item=>({card:freshProductMediaUrl(item.url),detail:freshProductMediaUrl(item.detail_url||item.url)}))
    .filter((item):item is {card:string;detail:string}=>Boolean(item.card&&item.detail))

  return{
    ...(fallback?structuredClone(fallback):{}),
    id:fallback?.id??1_000_000+remote.id,
    backendId:remote.id,
    code:remote.code,
    name,
    names,
    categoryCode:meta.categoryCode,
    subcategoryCode:remoteSubcategory,
    categoryName:remote.category?.names.fa?.trim()||fallback?.categoryName||meta.categoryName,
    subcategoryName:remote.subcategory?.names.fa?.trim()||fallback?.subcategoryName||meta.subcategoryName,
    availability:remote.availability,
    gallery:backendMedia.length?backendMedia.map(item=>item.detail):(fallback?.gallery??[meta.image,'./images/final/details/fabric-detail.webp',meta.fallbackImage]),
    image:backendMedia[0]?.card??fallback?.image,
    specificationValues:remote.specifications?.map(s=>({...s,labels:{fa:s.labels.fa||undefined,ar:s.labels.ar||undefined,en:s.labels.en||undefined,ku:s.labels.ku||undefined}})),
    specs:remote.specifications?{
      locked:remote.specifications.filter(s=>s.locked).map(s=>s.labels.fa||s.key),
      negotiable:remote.specifications.filter(s=>!s.locked).map(s=>s.labels.fa||s.key),
    }:fallback?.specs?structuredClone(fallback.specs):structuredClone(meta.specs),
  }
}

function mergeProducts(
  remoteProducts:PublicCatalogProduct[],
  managedCodes:string[],
  fallbackProducts:Product[],
):Product[]{
  const managed=new Set(managedCodes)
  const remoteByCode=new Map(remoteProducts.map(product=>[product.code,product]))
  const seen=new Set<string>()
  const result:Product[]=[]

  for(const fallback of fallbackProducts){
    const remote=remoteByCode.get(fallback.code)
    if(remote){
      const merged=mergeProduct(remote,fallback)
      if(merged)result.push(merged)
      seen.add(fallback.code)
      continue
    }
    if(!managed.has(fallback.code))result.push(structuredClone(fallback))
  }

  for(const remote of remoteProducts){
    if(seen.has(remote.code))continue
    const merged=mergeProduct(remote)
    if(merged)result.push(merged)
  }

  return result
}

function mergeCategories(
  remoteCategories:PublicCatalogCategory[],
  managedCategoryCodes:string[],
  managedSubcategoryCodes:string[],
  fallbackCategories:Category[],
):Category[]{
  const managedCategories=new Set(managedCategoryCodes)
  const managedSubcategories=new Set(managedSubcategoryCodes)
  const remoteByCode=new Map(remoteCategories.map(category=>[category.code,category]))
  const result:Category[]=[]
  const seen=new Set<string>()

  for(const fallback of fallbackCategories){
    const remote=remoteByCode.get(fallback.code)
    if(!remote){
      if(!managedCategories.has(fallback.code))result.push(structuredClone(fallback))
      continue
    }

    const remoteSubs=new Map(remote.subcategories.map(subcategory=>[subcategory.code,subcategory]))
    const subcategories:Category['subcategories']=[]

    for(const fallbackSub of fallback.subcategories){
      const remoteSub=remoteSubs.get(fallbackSub.code)
      if(remoteSub){
        subcategories.push({
          code:fallbackSub.code,
          name:remoteSub.names.fa?.trim()||fallbackSub.name,
        })
      }else if(!managedSubcategories.has(fallbackSub.code)){
        subcategories.push(structuredClone(fallbackSub))
      }
    }

    for(const remoteSub of remote.subcategories){
      if(!subcategoryCode(remoteSub.code)||subcategories.some(item=>item.code===remoteSub.code))continue
      subcategories.push({
        code:remoteSub.code,
        name:remoteSub.names.fa?.trim()||subMeta[remoteSub.code].subcategoryName,
      })
    }

    result.push({
      ...structuredClone(fallback),
      name:remote.names.fa?.trim()||fallback.name,
      subcategories,
    })
    seen.add(fallback.code)
  }

  for(const remote of remoteCategories){
    if(seen.has(remote.code)||!categoryCode(remote.code))continue
    const fallback=seedCategories.find(category=>category.code===remote.code)
    if(!fallback)continue
    result.push({
      ...structuredClone(fallback),
      name:remote.names.fa?.trim()||fallback.name,
      subcategories:remote.subcategories
        .filter(subcategory=>subcategoryCode(subcategory.code))
        .map(subcategory=>({
          code:subcategory.code as Product['subcategoryCode'],
          name:subcategory.names.fa?.trim()||subMeta[subcategory.code as Product['subcategoryCode']].subcategoryName,
        })),
    })
  }

  return result
}

export function mergeCatalogSnapshot(
  snapshot:CatalogSnapshot,
  fallbackProducts:Product[]=seedProducts,
  fallbackCategories:Category[]=seedCategories,
){
  return{
    products:mergeProducts(snapshot.products,snapshot.managedProductCodes,fallbackProducts),
    categories:mergeCategories(
      snapshot.categories,
      snapshot.managedCategoryCodes,
      snapshot.managedSubcategoryCodes,
      fallbackCategories,
    ),
  }
}
