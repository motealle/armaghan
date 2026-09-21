import { defineStore } from 'pinia'
import { ref, watch } from 'vue'
import { products as seedProducts } from '@/data/catalog'
import type { Product } from '@/types/domain'

const KEY='armaghan:test22:products-v1'
const LEGACY_KEYS=['armaghan:test21:products-v1','armaghan:test20:products-v4','armaghan:test20:products-v3']
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
  function reset(){items.value=structuredClone(seedProducts)}

  watch(items,(value)=>localStorage.setItem(KEY,JSON.stringify(value)),{deep:true})
  return{items,add,update,remove,removeMany,updateImage,reset}
})
