import {createSSRApp,reactive} from 'vue'
import {renderToString} from 'vue/server-renderer'
import {beforeEach,expect,it,vi} from 'vitest'
import type {AdminProduct,ProductFields} from '../services/adminApi'
const api=vi.hoisted(()=>({create:vi.fn(),update:vi.fn()}))
vi.mock('../services/adminApi',()=>({createAdminProduct:api.create,updateAdminProduct:api.update,fetchAdminProduct:vi.fn(),uploadAdminProductImage:vi.fn(),orderAdminProductImages:vi.fn(),deleteAdminProductImages:vi.fn()}))
vi.mock('../store',()=>({useAdminStore:()=>({clear:vi.fn()})}))
vi.mock('@/stores/locale',()=>({useLocaleStore:()=>({locale:'fa',t:(key:string)=>key})}))
vi.mock('vue-router',()=>({onBeforeRouteLeave:vi.fn()}))
import Editor from './BackendProductEditor.vue'
const fields:ProductFields={subcategory_id:1,code:'11001',name_fa:'Original',name_ar:null,name_en:'Original English',name_ku:null,availability:'available',active:true,sort_order:0,specifications:[]}
const row:AdminProduct={...fields,id:77,revision:'first',tags:[],category_code:'1',subcategory_code:'11',media:[],specifications:[]}
type Bindings={save:()=>Promise<void>;draft:{value:ProductFields};current:{value:AdminProduct|null};writesBlocked:{value:boolean}}
async function open(product:AdminProduct|null,mode:'edit'|'materialize',initial:ProductFields|null=null){
 let bindings:Bindings|undefined;const emit=vi.fn()
 await renderToString(createSSRApp({setup(){
  bindings=(Editor as unknown as {setup:(props:object,context:object)=>Bindings}).setup(reactive({open:true,product,taxonomy:[],initial,mode}),{expose:()=>{},emit})
  return()=>null
 }}))
 return {bindings:bindings!,emit}
}
beforeEach(()=>{api.create.mockReset();api.update.mockReset()})
it('edits and renames the same row on repeated saves, never calling create',async()=>{
 const {bindings,emit}=await open(structuredClone(row),'edit')
 bindings.draft.value.code='11199';bindings.draft.value.name_fa='Edited'
 api.update.mockResolvedValueOnce({product:{...row,code:'11199',name_fa:'Edited',revision:'second'}})
 await bindings.save()
 expect(api.update).toHaveBeenCalledWith(77,expect.objectContaining({code:'11199',name_fa:'Edited',name_en:'Original English'}),'first')
 bindings.draft.value.name_fa='Edited again'
 api.update.mockResolvedValueOnce({product:{...row,code:'11199',name_fa:'Edited again',revision:'third'}})
 await bindings.save()
 expect(api.update).toHaveBeenLastCalledWith(77,expect.objectContaining({code:'11199',name_fa:'Edited again'}),'second')
 expect(api.create).not.toHaveBeenCalled()
 expect(emit).toHaveBeenCalledWith('persisted',expect.objectContaining({id:77}))
})
it('materializes a reactive sample once and then updates its returned identity',async()=>{
 const {bindings,emit}=await open(null,'materialize',structuredClone(fields))
 bindings.draft.value.code='11199'
 api.create.mockResolvedValue({product:{...row,code:'11199'}})
 await bindings.save()
 bindings.draft.value.name_fa='Second edit'
 api.update.mockResolvedValue({product:{...row,code:'11199',name_fa:'Second edit'}})
 await bindings.save()
 expect(api.create).toHaveBeenCalledTimes(1)
 expect(api.update).toHaveBeenCalledWith(77,expect.objectContaining({code:'11199',name_fa:'Second edit'}),'first')
 expect(emit).toHaveBeenCalledWith('persisted',expect.objectContaining({id:77}))
})
it('never converts an unresolved existing edit into a create',async()=>{
 const {bindings}=await open(null,'edit',structuredClone(fields))
 await bindings.save()
 expect(api.create).not.toHaveBeenCalled();expect(api.update).not.toHaveBeenCalled()
 expect(bindings.writesBlocked.value).toBe(true)
})
it('blocks repeat creation after an ambiguous failed response',async()=>{
 const {bindings}=await open(null,'materialize',structuredClone(fields))
 api.create.mockRejectedValue(new Error('response lost'))
 await bindings.save();await bindings.save()
 expect(api.create).toHaveBeenCalledTimes(1)
 expect(bindings.writesBlocked.value).toBe(true)
})

it('ignores a second save while the first creation is in flight',async()=>{
 const {bindings}=await open(null,'materialize',structuredClone(fields))
 let resolve!:(value:{product:AdminProduct})=>void
 api.create.mockImplementation(()=>new Promise(done=>{resolve=done}))
 const first=bindings.save();const second=bindings.save()
 expect(api.create).toHaveBeenCalledTimes(1)
 resolve({product:row});await Promise.all([first,second])
 expect(bindings.current.value?.id).toBe(77)
})
