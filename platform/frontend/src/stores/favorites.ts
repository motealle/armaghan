import { defineStore } from 'pinia'
import { computed, ref, watch } from 'vue'
import { useCatalogStore } from '@/stores/catalog'
import { useCustomersStore } from '@/stores/customers'
import { useSessionStore } from '@/stores/session'

const KEY='armaghan:test24:favorites'

function load():number[]{
  try{return JSON.parse(localStorage.getItem(KEY)??'[]') as number[]}catch{return[]}
}

export const useFavoritesStore=defineStore('favorites',()=>{
  const ids=ref<number[]>(load())
  const catalog=useCatalogStore()
  const customers=useCustomersStore()
  const session=useSessionStore()
  const items=computed(()=>catalog.items.filter(product=>ids.value.includes(product.id)))

  function toggle(id:number){
    ids.value=ids.value.includes(id)?ids.value.filter(value=>value!==id):[...ids.value,id]
    if(session.isAdmin&&!session.impersonatedCustomerId)return
    const customerId=session.impersonatedCustomerId??(session.isCustomer?1:undefined)
    const customer=customerId?customers.items.find(item=>item.id===customerId):undefined
    customers.recordWishlistChange({customerId,label:customer?.name,favoritesCount:ids.value.length})
  }
  function has(id:number){return ids.value.includes(id)}

  watch(ids,value=>localStorage.setItem(KEY,JSON.stringify(value)),{deep:true})
  return{ids,items,toggle,has}
})
