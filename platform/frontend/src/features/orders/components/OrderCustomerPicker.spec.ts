import {createSSRApp} from 'vue'
import {renderToString} from 'vue/server-renderer'
import {expect,it,vi} from 'vitest'
const api=vi.hoisted(()=>({fetch:vi.fn()}))
vi.mock('@/features/admin/services/adminApi',()=>({fetchAdminCustomers:api.fetch}))
vi.mock('@/stores/locale',()=>({useLocaleStore:()=>({locale:'fa',t:(key:string)=>key})}))
import Picker from './OrderCustomerPicker.vue'
import type {AdminCustomer,CustomerPage} from '@/features/admin/services/adminApi'
type Bindings={query:{value:string};result:{value:CustomerPage};choices:{value:AdminCustomer[]};load:(page?:number)=>Promise<void>}
const row=(id:number)=>({id,active:true,company_name:'Shop '+id,whatsapp:'+9647701234567'}) as AdminCustomer
async function setup(selected:AdminCustomer|null=null){
 let panel!:Bindings
 await renderToString(createSSRApp({setup(){panel=(Picker as unknown as {setup:(props:object,context:{emit:()=>void;expose:()=>void})=>Bindings}).setup({modelValue:selected},{emit:()=>{},expose:()=>{}});return()=>null}}))
 return panel
}
it('keeps the selected customer across result pages and excludes inactive customers',async()=>{
 const panel=await setup(row(77))
 api.fetch.mockResolvedValue({customers:[row(2),{...row(3),active:false}],page:2,last_page:4,total:170})
 await panel.load(2)
 expect(api.fetch).toHaveBeenCalledWith(2,'')
 expect(panel.choices.value.map(c=>c.id)).toEqual([77,2])
})
it('normalizes localized phone search and ignores an older search response',async()=>{
 const panel=await setup()
 let resolve!:(value:CustomerPage)=>void
 api.fetch.mockReturnValueOnce(new Promise<CustomerPage>(r=>{resolve=r})).mockResolvedValueOnce({customers:[row(9)],page:1,last_page:1,total:1})
 panel.query.value='٠٧٧٠ ١٢٣-٤٥٦٧'
 const old=panel.load()
 expect(api.fetch).toHaveBeenNthCalledWith(1,1,'7701234567')
 panel.query.value='new name'
 await panel.load()
 resolve({customers:[row(1)],page:1,last_page:1,total:1});await old
 expect(panel.result.value.customers[0]?.id).toBe(9)
})
