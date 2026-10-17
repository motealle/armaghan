import { beforeEach, describe, expect, it, vi } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { useAdminStore } from '../store'
import { useCatalogStore } from '@/stores/catalog'
import { useProductQuickOrder } from './productQuickOrder'
import { fetchProductOrder, saveProductOrder, type ProductOrderSnapshot } from './adminApi'
import { CustomerSessionApiError } from '@/features/auth/services/customerSessionApi'
vi.mock('./adminApi',async importOriginal=>({...await importOriginal<typeof import('./adminApi')>(),fetchProductOrder:vi.fn(),saveProductOrder:vi.fn()}))
const snapshot=():ProductOrderSnapshot=>({groups:{
 available:{revision:'a'.repeat(64),products:[{id:10,code:'11001',sort_order:0,subcategory_code:'11',label:'',thumb_url:null}]},
 producible:{revision:'b'.repeat(64),products:[1,2,3].map((id,index)=>({id,code:'1200'+id,sort_order:index,subcategory_code:'12',label:'',thumb_url:null}))},
}})
async function manager(){const admin=useAdminStore(),order=useProductQuickOrder();admin.identity={name:'QA',email:'qa@example.invalid'};await vi.waitFor(()=>expect(order.busy).toBe(false));return {admin,order}}
beforeEach(()=>{vi.clearAllMocks();setActivePinia(createPinia());vi.stubGlobal('localStorage',{getItem:()=>null,setItem:()=>{}});vi.mocked(fetchProductOrder).mockResolvedValue(snapshot())})
describe('shared immediate product ordering',()=>{
 it('never requests protected order or writes for guests',async()=>{const order=useProductQuickOrder();await order.reload();await order.move('12003','first');expect(fetchProductOrder).not.toHaveBeenCalled();expect(saveProductOrder).not.toHaveBeenCalled()})
 it('sends the complete global group, updates canonical card ranks only after success and preserves the other group',async()=>{
  const {order}=await manager(),catalog=useCatalogStore();catalog.items=[{...catalog.items[0]!,backendId:3,code:'12003',sortOrder:2}]
  const result=snapshot();result.groups.producible.products=[3,1,2].map((id,index)=>({...snapshot().groups.producible.products.find(p=>p.id===id)!,sort_order:index}));result.groups.producible.revision='c'.repeat(64)
  vi.mocked(saveProductOrder).mockResolvedValue(result)
  expect(await order.move('12003','first')).toBe(true)
  expect(saveProductOrder).toHaveBeenCalledWith('producible','b'.repeat(64),[3,1,2]);expect(catalog.items[0]!.sortOrder).toBe(0)
  expect(order.locate('11001')?.index).toBe(0);expect(order.locate('12003')?.index).toBe(0)
  await order.move('12003','up');expect(saveProductOrder).toHaveBeenCalledTimes(1)
 })
 it('blocks overlapping moves and ignores a late response after logout',async()=>{
  const {order,admin}=await manager();let resolve!:(value:ProductOrderSnapshot)=>void
  vi.mocked(saveProductOrder).mockImplementation(()=>new Promise(r=>resolve=r))
  const request=order.move('12002','up');await order.move('12003','first');expect(saveProductOrder).toHaveBeenCalledTimes(1)
  admin.clear();resolve(snapshot());expect(await request).toBe(false);expect(order.snapshot).toBeNull();expect(order.note).toBe('')
 })
 it('preserves displayed order on a conflict and requires explicit reload before retry',async()=>{
  const {order}=await manager();vi.mocked(saveProductOrder).mockRejectedValue(new CustomerSessionApiError(409,'changed'))
  expect(await order.move('12003','first')).toBe(false);expect(order.locate('12003')?.index).toBe(2);expect(order.error).toBe('orderConflict')
  await order.move('12002','up');expect(saveProductOrder).toHaveBeenCalledTimes(1)
  await order.reload();expect(order.error).toBe('');expect(order.locate('12003')?.index).toBe(2)
 })
})
