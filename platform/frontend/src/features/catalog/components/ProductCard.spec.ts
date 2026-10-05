import { describe, expect, it } from 'vitest'
import { createSSRApp, h } from 'vue'
import { renderToString } from 'vue/server-renderer'
import { createPinia, setActivePinia } from 'pinia'
import ProductCard from './ProductCard.vue'
import { products } from '@/data/catalog'
import { useLocaleStore } from '@/stores/locale'

function memoryStorage(){
  const values=new Map<string,string>()
  return {
    getItem:(key:string)=>values.get(key)??null,
    setItem:(key:string,value:string)=>{values.set(key,String(value))},
    removeItem:(key:string)=>{values.delete(key)},
    clear:()=>values.clear(),
    key:(index:number)=>Array.from(values.keys())[index]??null,
    get length(){return values.size},
  }
}
Object.defineProperty(globalThis,'sessionStorage',{value:memoryStorage(),configurable:true})
Object.defineProperty(globalThis,'localStorage',{value:memoryStorage(),configurable:true})

async function render(adminEditable:boolean){
  const pinia=createPinia();setActivePinia(pinia)
  const locale=useLocaleStore();locale.locale='fa'
  const app=createSSRApp({render:()=>h(ProductCard,{product:products[0]!,adminEditable})})
  app.use(pinia)
  return {html:await renderToString(app),locale}
}

describe('product card manager edit affordance',()=>{
  it('is hidden for ordinary visitors',async()=>{
    const {html,locale}=await render(false)
    expect(html).not.toContain('aria-label="'+locale.t('editProduct')+'"')
  })
  it('is rendered only when the server-admin view enables it',async()=>{
    const {html,locale}=await render(true)
    expect(html).toContain('aria-label="'+locale.t('editProduct')+'"')
  })
})
