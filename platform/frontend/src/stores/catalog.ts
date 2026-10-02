import { defineStore } from 'pinia'
import { ref, watch } from 'vue'
import { categories as seedCategories, products as seedProducts } from '@/data/catalog'
import { fetchCatalogSnapshot, mergeCatalogSnapshot } from '@/features/catalog/services/catalogApi'
import type { Category, Product } from '@/types/domain'

const KEY='armaghan:test29:products-v1'
const LEGACY_KEYS=['armaghan:test25:products-v1','armaghan:test24:products-v1','armaghan:test23:products-v1','armaghan:test22:products-v1','armaghan:test21:products-v1','armaghan:test20:products-v4','armaghan:test20:products-v3']
const legacyCategoryImages=new Set([
  './images/final/categories/category-baby.webp',
  './images/final/categories/category-kids.webp',
  './images/final/categories/category-women-modest.webp',
])

function migrateProduct(product:Product):Product{
  const migrated=structuredClone(product)
  if(migrated.image&&legacyCategoryImages.has(migrated.image))delete migrated.image
  return migrated
}

function load(): Product[] {
  try {
    const raw=localStorage.getItem(KEY)??LEGACY_KEYS.map(key=>localStorage.getItem(key)).find(Boolean)??null
    return raw ? (JSON.parse(raw) as Product[]).map(migrateProduct) : structuredClone(seedProducts)
  } catch {
    return structuredClone(seedProducts)
  }
}

export const useCatalogStore=defineStore('catalog',()=>{
  const items=ref<Product[]>(load())
  const categories=ref<Category[]>(structuredClone(seedCategories))
  const syncState=ref<'local'|'loading'|'synced'|'error'>('local')
  const lastSyncAt=ref<string|null>(null)

  function add(product: Product){items.value=[...items.value,product]}
  function update(product: Product){
    const index=items.value.findIndex(item=>item.id===product.id)
    if(index>=0)items.value[index]=structuredClone(product)
  }
  function remove(id:number){items.value=items.value.filter(p=>p.id!==id)}
  function removeMany(ids:number[]){const set=new Set(ids);items.value=items.value.filter(p=>!set.has(p.id))}
  function updateImage(id:number,image:string){
    const product=items.value.find(p=>p.id===id)
    if(product)product.image=image
  }
  function reset(){
    items.value=structuredClone(seedProducts)
    categories.value=structuredClone(seedCategories)
    syncState.value='local'
    lastSyncAt.value=null
  }

  async function hydrateFromBackend(){
    if(syncState.value==='loading')return
    syncState.value='loading'
    try{
      const snapshot=await fetchCatalogSnapshot()
      const merged=mergeCatalogSnapshot(snapshot,items.value,categories.value)
      items.value=merged.products
      categories.value=merged.categories
      syncState.value='synced'
      lastSyncAt.value=new Date().toISOString()
    }catch{
      syncState.value='error'
    }
  }

  watch(items,(value)=>localStorage.setItem(KEY,JSON.stringify(value)),{deep:true})
  return{
    items,
    categories,
    syncState,
    lastSyncAt,
    add,
    update,
    remove,
    removeMany,
    updateImage,
    reset,
    hydrateFromBackend,
  }
})
