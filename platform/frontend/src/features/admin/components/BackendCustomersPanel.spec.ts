import {createSSRApp} from 'vue'
import {renderToString} from 'vue/server-renderer'
import {expect,it,vi} from 'vitest'
const api=vi.hoisted(()=>({fetch:vi.fn(),update:vi.fn()}))
vi.mock('../services/adminApi',()=>({fetchAdminCustomers:api.fetch,updateAdminCustomer:api.update,createAdminCustomer:vi.fn(),customerBusinessDefaults:{pinned:false}}))
vi.mock('../store',()=>({useAdminStore:()=>({identity:{email:'manager@example.test'},clear:vi.fn()})}))
vi.mock('@/stores/locale',()=>({useLocaleStore:()=>({t:(key:string)=>key})}))
import BackendCustomersPanel from './BackendCustomersPanel.vue'
it('rereads the saved pin immediately while the save guard is active',async()=>{
 const row={id:7,company_name:'Test shop',name:'Test',email:'customer@example.test',revision:'revision',active:true,pinned:false,tags:[],whatsapp:null,priority:0}
 api.fetch.mockResolvedValueOnce({customers:[row],page:1,last_page:1,total:1}).mockResolvedValueOnce({customers:[{...row,pinned:true}],page:1,last_page:1,total:1})
 api.update.mockResolvedValue({...row,pinned:true})
 type Bindings={load:()=>Promise<void>;togglePin:(customer:typeof row)=>Promise<void>;result:{value:{customers:(typeof row)[]}}}
 let panel:Bindings|undefined
 await renderToString(createSSRApp({setup(){
  panel=(BackendCustomersPanel as unknown as {setup:(props:object,context:{expose:()=>void})=>Bindings}).setup({},{expose:()=>{}})
  return()=>null
 }}))
 await panel!.load()
 await panel!.togglePin(row)
 expect(api.update).toHaveBeenCalledWith(7,{pinned:true},'revision')
 expect(api.fetch).toHaveBeenCalledTimes(2)
 expect(panel!.result.value.customers[0]?.pinned).toBe(true)
})
