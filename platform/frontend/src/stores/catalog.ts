import { defineStore } from 'pinia'
import { ref, watch } from 'vue'
import { products as seedProducts } from '@/data/catalog'
import type { Product } from '@/types/domain'

const KEY='armaghan:test12:products'

function load(): Product[] {
  try {
    const raw=localStorage.getItem(KEY)
    return raw ? JSON.parse(raw) as Product[] : structuredClone(seedProducts)
  } catch {
    return structuredClone(seedProducts)
  }
}

export const useCatalogStore=defineStore('catalog',()=>{
  const items=ref<Product[]>(load())

  function add(product: Product){items.value=[...items.value,product]}
  function remove(id:number){items.value=items.value.filter(p=>p.id!==id)}
  function updateImage(id:number,image:string){
    const product=items.value.find(p=>p.id===id)
    if(product)product.image=image
  }
  function reset(){items.value=structuredClone(seedProducts)}

  watch(items,(value)=>localStorage.setItem(KEY,JSON.stringify(value)),{deep:true})
  return{items,add,remove,updateImage,reset}
})
