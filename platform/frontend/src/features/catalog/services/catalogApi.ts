import { toRaw } from 'vue'
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
  width?:number|null
  height?:number|null
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
let refreshSequence=0
const PRODUCT_MEDIA_CACHE_REV='20261007-photo-recovery-4'
function cloneCatalogValue<T extends object>(value:T):T{
  // Catalog DTOs are JSON-only; prior hydrations can leave nested Vue proxies.
  return JSON.parse(JSON.stringify(toRaw(value))) as T
}
export function freshProductMediaUrl(value?:string){
  const url=value?.trim()??''
  if(!url||!url.includes('/backend/api/catalog/media/'))return url
  if(/[?&]v=/.test(url))return url.replace(/([?&])v=[^&]*/, '$1v='+PRODUCT_MEDIA_CACHE_REV)
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

async function fetchAllProducts(signal?:AbortSignal,refresh=''):Promise<{
  products:PublicCatalogProduct[]
  managedCodes:string[]
}>{
  const first=await requestJson<ProductResponse>('/api/catalog/products?per_page=100&page=1'+refresh,signal)
  const lastPage=Math.max(1,Number(first.meta?.last_page??1))

  if(lastPage>maxProductPages)throw new CatalogApiError(413,'Catalog exceeds the staged client sync limit.')

  const products=[...first.data]
  // Fetch at most three pages together: newly uploaded photos should not wait
  // behind a long serial catalog read. Promise.all preserves page order.
  for(let start=2;start<=lastPage;start+=3){
    const pages=Array.from({length:Math.min(3,lastPage-start+1)},(_,i)=>start+i)
    const batch=await Promise.all(pages.map(page=>requestJson<ProductResponse>('/api/catalog/products?per_page=100&page='+page+refresh,signal)))
    for(const next of batch)products.push(...next.data)
  }

  return{
    products,
    managedCodes:Array.isArray(first.catalog?.managed_codes)?first.catalog.managed_codes:[],
  }
}

export async function fetchCatalogSnapshot(signal?:AbortSignal,fresh=false):Promise<CatalogSnapshot>{
  // Public responses permit stale-while-revalidate. After an admin write, use a
  // unique read URL so a shared cache cannot roll the saved card back on close.
  const refresh=fresh?'&refresh='+Date.now().toString(36)+'-'+(++refreshSequence):''
  const [categoryPayload,productPayload]=await Promise.all([
    requestJson<CategoryResponse>('/api/catalog/categories'+(refresh?'?'+refresh.slice(1):''),signal),
    fetchAllProducts(signal,refresh),
  ])

  return{
    categories:categoryPayload.data,
    products:productPayload.products,
    managedCategoryCodes:categoryPayload.catalog?.managed_category_codes??[],
    managedSubcategoryCodes:categoryPayload.catalog?.managed_subcategory_codes??[],
    managedProductCodes:productPayload.managedCodes,
  }
}

export function mergeProduct(remote:PublicCatalogProduct,fallback?:Product):Product|null{
  const remoteSubcategory=remote.subcategory?.code??remote.code.trim().slice(0,2)
  if(!subcategoryCode(remoteSubcategory))return null

  const meta=subMeta[remoteSubcategory]
  const specifications=remote.specifications?.length?remote.specifications:undefined
  const names=productNames(remote.names,fallback)
  const name=names.fa||fallback?.name||meta.subcategoryName
  const backendMedia=(remote.media??[])
    .map(item=>({card:freshProductMediaUrl(item.url),thumb:freshProductMediaUrl(item.thumb_url||item.url),detail:freshProductMediaUrl(item.detail_url||item.url),width:item.width??undefined,height:item.height??undefined}))
    .filter(item=>Boolean(item.card&&item.detail))

  return{
    ...(fallback?cloneCatalogValue(fallback):{}),
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
    media:backendMedia.length?backendMedia:undefined,
    gallery:backendMedia.length?backendMedia.map(item=>item.detail):(fallback?.gallery??[meta.image,'./images/final/details/fabric-detail.webp',meta.fallbackImage]),
    image:backendMedia[0]?.card??fallback?.image,
    specificationValues:specifications?.map(s=>({...s,labels:{fa:s.labels.fa||undefined,ar:s.labels.ar||undefined,en:s.labels.en||undefined,ku:s.labels.ku||undefined}})),
    specs:specifications?{
      locked:specifications.filter(s=>s.locked).map(s=>s.labels.fa||s.key),
      negotiable:specifications.filter(s=>!s.locked).map(s=>s.labels.fa||s.key),
    }:cloneCatalogValue(meta.specs),
  }
}

function mergeProducts(
  remoteProducts:PublicCatalogProduct[],
  managedCodes:string[],
  fallbackProducts:Product[],
  includeUnmanagedFallback:boolean,
):Product[]{
  const managed=new Set(managedCodes)
  const remoteByCode=new Map(remoteProducts.map(product=>[product.code,product]))
  const remoteById=new Map(remoteProducts.map(product=>[product.id,product]))
  const seen=new Set<string>()
  const result:Product[]=[]

  for(const fallback of fallbackProducts){
    const remote=fallback.backendId!==undefined?remoteById.get(fallback.backendId):remoteByCode.get(fallback.code)
    if(remote){
      if(seen.has(remote.code))continue
      const merged=mergeProduct(remote,fallback)
      if(merged)result.push(merged)
      seen.add(remote.code)
      continue
    }
    // A server-owned row absent from a successful full snapshot is archived/deleted,
    // not an unmanaged sample to resurrect. API failure never enters this merge.
    if(includeUnmanagedFallback&&fallback.backendId===undefined&&!managed.has(fallback.code))result.push(cloneCatalogValue(fallback))
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
      if(!managedCategories.has(fallback.code))result.push(cloneCatalogValue(fallback))
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
        subcategories.push(cloneCatalogValue(fallbackSub))
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
      ...cloneCatalogValue(fallback),
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
      ...cloneCatalogValue(fallback),
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
  includeUnmanagedFallback=true,
){
  return{
    products:mergeProducts(snapshot.products,snapshot.managedProductCodes,fallbackProducts,includeUnmanagedFallback),
    categories:mergeCategories(
      snapshot.categories,
      snapshot.managedCategoryCodes,
      snapshot.managedSubcategoryCodes,
      fallbackCategories,
    ),
  }
}

// Shared selections are fetched directly, independent of this device's local catalog.
export async function fetchSharedProducts(codes:string[]):Promise<Product[]>{
  if(!codes.length)return[]
  const query=new URLSearchParams({per_page:'100'})
  codes.forEach(code=>query.append('codes[]',code))
  const response=await requestJson<ProductResponse>('/api/catalog/products?'+query.toString())
  return response.data.map(remote=>mergeProduct(remote,seedProducts.find(p=>p.code===remote.code)))
    .filter((product):product is Product=>product!==null)
}
