import { beforeEach, afterEach, expect, it, vi } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { fetchCatalogSnapshot } from '@/features/catalog/services/catalogApi'
import { subMeta } from '@/data/catalog'
import { useCatalogStore } from './catalog'

vi.mock('@/features/catalog/services/catalogApi',async importOriginal=>({
  ...await importOriginal<typeof import('@/features/catalog/services/catalogApi')>(),
  fetchCatalogSnapshot:vi.fn(),
}))
beforeEach(()=>{
  const rows=new Map<string,string>()
  vi.stubGlobal('localStorage',{getItem:(key:string)=>rows.get(key)??null,setItem:(key:string,value:string)=>rows.set(key,value)})
  setActivePinia(createPinia())
  vi.mocked(fetchCatalogSnapshot).mockResolvedValue({categories:[],products:[],managedCategoryCodes:[],managedSubcategoryCodes:[],managedProductCodes:[]})
})
afterEach(()=>vi.unstubAllGlobals())

it('skips a recent duplicate hydration but refreshes immediately after an admin save',async()=>{
  const store=useCatalogStore()
  await store.hydrateFromBackend();await store.hydrateFromBackend()
  expect(fetchCatalogSnapshot).toHaveBeenCalledTimes(1)
  expect(fetchCatalogSnapshot).toHaveBeenCalledWith(undefined,true)
  await store.hydrateFromBackend(true)
  expect(fetchCatalogSnapshot).toHaveBeenCalledTimes(2)
})
it('repairs previously emptied cached specifications before any network response',()=>{
  localStorage.setItem('armaghan:test29:products-v1',JSON.stringify([{id:1,code:'11001',subcategoryCode:'11',specs:{locked:[],negotiable:[]},specificationValues:[]}]))
  const product=useCatalogStore().items[0]!
  expect(product.specs).toEqual(subMeta['11'].specs)
  expect(product.specificationValues).toBeUndefined()
})
it('waits for an in-flight refresh and still fetches again after an admin save',async()=>{
  let release!:()=>void
  vi.mocked(fetchCatalogSnapshot).mockImplementationOnce(()=>new Promise(resolve=>{
    release=()=>resolve({categories:[],products:[],managedCategoryCodes:[],managedSubcategoryCodes:[],managedProductCodes:[]})
  }))
  const store=useCatalogStore()
  const first=store.hydrateFromBackend(),forced=store.hydrateFromBackend(true)
  expect(fetchCatalogSnapshot).toHaveBeenCalledTimes(1)
  release();await Promise.all([first,forced])
  expect(fetchCatalogSnapshot).toHaveBeenCalledTimes(2)
})

it('binds the materialized sample before refresh so even a changed first code replaces the original card',async()=>{
 const store=useCatalogStore();const original=store.items[0]!
 const localId=original.id;store.items=[original];const before=store.items.length
 const remote={id:77,code:'11199',names:{fa:'Edited sample',ar:null,en:null,ku:null},availability:'made_to_order' as const,sort_order:0,category:null,subcategory:null}
 store.acceptBackendProduct(localId,remote)
 expect(store.items.find(p=>p.id===localId)).toMatchObject({code:'11199',name:'Edited sample',backendId:77})
 vi.mocked(fetchCatalogSnapshot).mockResolvedValue({categories:[],products:[remote],managedCategoryCodes:[],managedSubcategoryCodes:[],managedProductCodes:['11199']})
 await store.hydrateFromBackend(true)
 expect(store.items).toHaveLength(before)
 expect(store.items.filter(p=>p.backendId===77)).toHaveLength(1)
 expect(store.items.find(p=>p.id===localId)).toMatchObject({code:'11199',name:'Edited sample',backendId:77})
 expect(store.items.some(p=>p.code==='11001')).toBe(false)
 await store.hydrateFromBackend(true)
 expect(store.items).toHaveLength(before)
})

it('shows only server products on a fresh device after a sample was renamed elsewhere',async()=>{
 const store=useCatalogStore()
 expect(store.items.some(p=>p.code==='11001')).toBe(true)
 vi.mocked(fetchCatalogSnapshot).mockResolvedValue({categories:[],products:[{id:77,code:'11199',names:{fa:'Edited on another device',ar:null,en:null,ku:null},availability:'available',sort_order:0,category:null,subcategory:null}],managedCategoryCodes:[],managedSubcategoryCodes:[],managedProductCodes:['11199']})
 await store.hydrateFromBackend(true)
 expect(store.items).toHaveLength(1)
 expect(store.items[0]).toMatchObject({backendId:77,code:'11199'})
 expect(store.items.some(p=>p.code==='11001')).toBe(false)
})
it('preserves the existing catalog when the server cannot be read',async()=>{
 const store=useCatalogStore();const before=JSON.stringify(store.items)
 vi.mocked(fetchCatalogSnapshot).mockRejectedValue(new Error('offline'))
 await store.hydrateFromBackend(true)
 expect(JSON.stringify(store.items)).toBe(before)
 expect(store.syncState).toBe('error')
})
