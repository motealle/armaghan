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

async function render(adminEditable:boolean,product=products[0]!){
  const pinia=createPinia();setActivePinia(pinia)
  const locale=useLocaleStore();locale.locale='fa'
  const app=createSSRApp({render:()=>h(ProductCard,{product,adminEditable})})
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


describe('lightweight card images',()=>{
  it('keeps detail images out of initial page requests',async()=>{
    const {html}=await render(false,{...products[0]!,image:'/card/1',gallery:['/detail/1'],media:[{thumb:'/thumb/1',card:'/card/1',detail:'/detail/1',width:1920,height:1080}]})
    // No image request exists until the card reaches the 30% preload zone.
    expect(html).not.toContain('src="/thumb/1"')
    expect(html).not.toContain('/detail/1')
    expect(html).not.toContain('background-image')
    expect(html).not.toContain('class="smart-placeholder-svg"')
  })
})
