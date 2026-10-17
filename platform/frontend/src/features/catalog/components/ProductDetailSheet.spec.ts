import {createSSRApp,reactive,h} from 'vue'
import {renderToString} from 'vue/server-renderer'
import {beforeEach,expect,it,vi} from 'vitest'
import {products} from '@/data/catalog'
import type {AdminProduct} from '@/features/admin/services/adminApi'
const api=vi.hoisted(()=>({fetch:vi.fn(),save:vi.fn(),taxonomy:vi.fn(),schema:vi.fn()}))
const state=vi.hoisted(()=>({identity:{name:'QA'} as object|null,locale:'fa'}))
vi.mock('@/features/admin/store',()=>({useAdminStore:()=>({get identity(){return state.identity},clear:()=>{state.identity=null}})}))
vi.mock('@/stores/locale',()=>({useLocaleStore:()=>({get locale(){return state.locale},t:(key:string)=>key,subcategoryName:()=>'',specLabel:(value:string)=>value})}))
vi.mock('@/components/ui/BaseSheet.vue',()=>({default:{setup:(_:object,{slots}:{slots:{default:()=>unknown}})=>()=>h('div',slots.default() as never)}}))
vi.mock('@/features/admin/services/adminApi',()=>({fetchAdminProduct:api.fetch,updateProductSpecifications:api.save,fetchProductTaxonomy:api.taxonomy,saveProductSpecificationSchema:api.schema}))
import Sheet from './ProductDetailSheet.vue'
const row:AdminProduct={id:77,revision:'first',subcategory_id:1,code:'12011',name_fa:'پتو',name_ar:null,name_en:null,name_ku:null,availability:'available',active:true,sort_order:0,tags:[],media:[],category_code:'1',subcategory_code:'12',specifications:[{id:8,key:'fabric',locked:true,labels:{fa:'جنس',en:'Fabric',ar:null,ku:null},value_text:'پنبه',values:{fa:'پنبه',en:'Cotton',ar:null,ku:null}}]}
const product={...products[0]!,backendId:77,specificationValues:row.specifications.map(s=>({...s,value_text:s.value_text??null}))}
type Bindings={beginEdit:()=>Promise<void>;save:()=>Promise<void>;cancel:()=>void;draft:{value:Record<string,Record<string,string>>};language:{value:string};editing:{value:boolean};error:{value:string};conflict:{value:boolean}}
async function open(){let bindings:Bindings|undefined;const emit=vi.fn();const props=reactive({open:true,product,adminEditable:true});await renderToString(createSSRApp({setup(){bindings=(Sheet as unknown as {setup:(props:object,ctx:object)=>Bindings}).setup(props,{expose:()=>{},emit});return()=>null}}));return {bindings:bindings!,emit,props}}
beforeEach(()=>{state.identity={name:'QA'};state.locale='fa';api.fetch.mockResolvedValue({product:structuredClone(row)});api.save.mockReset()})
it('shows the edit control only for a real administrator and persisted product',async()=>{
 let html=await renderToString(createSSRApp({render:()=>h(Sheet,{open:true,product,adminEditable:true})}));expect(html).toContain('aria-label="specEdit"')
 state.identity=null;html=await renderToString(createSSRApp({render:()=>h(Sheet,{open:true,product,adminEditable:true})}));expect(html).not.toContain('aria-label="specEdit"')
})
it('edits locked values and retains separate language drafts, using fresh revisions sequentially',async()=>{
 const {bindings,emit,props}=await open();await bindings.beginEdit()
 emit.mockImplementation((event,saved)=>{if(event==='saved')props.product={...product,specificationValues:saved.specifications}})
 bindings.draft.value.fabric!.fa='کتان';bindings.language.value='en';bindings.draft.value.fabric!.en='Linen';bindings.language.value='fa'
 expect(bindings.draft.value.fabric!.fa).toBe('کتان')
 api.save.mockImplementation(async(current:AdminProduct,lang:'fa'|'en',changes:{value_text:string}[])=>{const next=JSON.parse(JSON.stringify(current)) as AdminProduct;next.revision=current.revision+'-next';next.specifications[0]!.values![lang]=changes[0]!.value_text;if(lang==='fa')next.specifications[0]!.value_text=changes[0]!.value_text;return {product:next}})
 await bindings.save();expect(api.save).toHaveBeenCalledTimes(2)
 expect(api.save.mock.calls[0]?.[1]).toBe('fa');expect(api.save.mock.calls[1]?.[0].revision).toBe('first-next')
 expect(bindings.editing.value).toBe(false);expect(emit).toHaveBeenCalledWith('saved',expect.objectContaining({id:77}))
})
it('preserves unsaved text on failed save and does not silently overwrite a conflict',async()=>{
 const {CustomerSessionApiError}=await import('@/features/auth/services/customerSessionApi')
 const {bindings}=await open();await bindings.beginEdit();bindings.draft.value.fabric!.en='Linen'
 api.save.mockRejectedValue(new CustomerSessionApiError(409,'changed'))
 await bindings.save();expect(bindings.editing.value).toBe(true);expect(bindings.draft.value.fabric!.en).toBe('Linen');expect(bindings.conflict.value).toBe(true)
 await bindings.save();expect(api.save).toHaveBeenCalledTimes(1)
})
it('leaves the view unchanged when canceling edits',async()=>{
 const {bindings}=await open();await bindings.beginEdit();bindings.draft.value.fabric!.fa='کتان'
 vi.stubGlobal('window',{confirm:()=>true});bindings.cancel();vi.unstubAllGlobals()
 expect(bindings.editing.value).toBe(false);expect(api.save).not.toHaveBeenCalled();expect(product.specificationValues[0]?.value_text).toBe('پنبه')
})

it('initializes only an empty server taxonomy from the visible default fields before editing',async()=>{
 api.fetch.mockResolvedValueOnce({product:{...row,specifications:[]}}).mockResolvedValueOnce({product:structuredClone(row)})
 const group={id:1,schema_revision:'empty-revision',specifications:[]}
 api.taxonomy.mockResolvedValue({subcategories:[group]});api.schema.mockResolvedValue({})
 const {bindings}=await open();await bindings.beginEdit()
 expect(api.schema).toHaveBeenCalledWith(group,expect.arrayContaining([expect.objectContaining({id:null,key:'catalog_spec_0',locked:true})]))
 expect(api.schema.mock.calls[0]?.[1]).toHaveLength(product.specs.locked.length+product.specs.negotiable.length)
 expect(bindings.editing.value).toBe(true)
})
