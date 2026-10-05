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
