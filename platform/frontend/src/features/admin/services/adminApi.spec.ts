import { beforeEach, describe, expect, it, vi } from 'vitest'
import { createAdminUser, updateAdminUser, type AdminUser, createAdminProduct, updateAdminProduct, uploadAdminProductImage, orderAdminProductImages, type AdminProduct, type ProductFields } from './adminApi'

beforeEach(()=>vi.restoreAllMocks())
const fields:ProductFields={subcategory_id:91,code:'11099',name_fa:'واقعی',name_ar:null,name_en:null,name_ku:null,availability:'available',active:true,sort_order:0}
const product:AdminProduct={...fields,id:872,revision:'a'.repeat(64),category_code:'1',subcategory_code:'11',media:[{id:42,url:'/backend/storage/card.jpg',thumb_url:'/backend/storage/thumb.jpg'}]}
function server(){
  return vi.spyOn(globalThis,'fetch').mockImplementation(async(url)=>String(url).endsWith('/api/csrf-token')?new Response(JSON.stringify({token:'test-csrf'}),{status:200}):new Response(JSON.stringify({product}),{status:200}))
}
describe('canonical product transport',()=>{
  it('sends multipart with a browser boundary, credentials and CSRF, using real server IDs',async()=>{
    const fetch=server(),file=new File(['image-bytes'],'front.jpg',{type:'image/jpeg'})
    await uploadAdminProductImage(product,file)
    const call=fetch.mock.calls.find(([url])=>String(url).endsWith('/products/872/images'))!
    const init=call[1]!,headers=new Headers(init.headers)
    expect(headers.has('Content-Type')).toBe(false)
    expect(headers.has('X-CSRF-TOKEN')).toBe(true)
    expect(init.credentials).toBe('same-origin')
    expect(init.body).toBeInstanceOf(FormData)
    expect((init.body as FormData).get('revision')).toBe(product.revision)
    expect((init.body as FormData).get('image')).toBe(file)
  })
  it('does not send fixture identity/media in create or update and keeps revision on ordering',async()=>{
    const fetch=server()
    await createAdminProduct(fields);await updateAdminProduct(product.id,fields,product.revision);await orderAdminProductImages(product,[42])
    const calls=fetch.mock.calls.filter(([url])=>!String(url).endsWith('/api/csrf-token'))
    expect(JSON.parse(calls[0]![1]!.body as string)).toEqual(fields)
    expect(JSON.parse(calls[1]![1]!.body as string)).toEqual({...fields,revision:product.revision})
    expect(JSON.parse(calls[2]![1]!.body as string)).toEqual({revision:product.revision,media_ids:[42]})
  })
  it('surfaces upload and stale-edit errors without claiming success',async()=>{
    vi.spyOn(globalThis,'fetch').mockResolvedValue(new Response(JSON.stringify({message:'Conflict'}),{status:409}))
    await expect(updateAdminProduct(product.id,fields,product.revision)).rejects.toMatchObject({status:409})
  })
})

describe('real account transport',()=>{
  it('keeps initial passwords in the protected POST body and does not expose them in an update URL',async()=>{
    const fetch=server()
    const fields={name:'Buyer',email:'buyer@example.test',role:'customer',active:true,password:'InitialPass2026',password_confirmation:'InitialPass2026'}
    await createAdminUser(fields)
    const row:AdminUser={id:911,name:fields.name,email:fields.email,role:'customer',active:true,is_owner:false,protected:false,revision:'b'.repeat(64)}
    await updateAdminUser(row,{name:'Updated',role:'customer',active:false})
    const calls=fetch.mock.calls.filter(([url])=>!String(url).endsWith('/api/csrf-token'))
    expect(calls[0]![0]).toBe('/backend/api/admin/users')
    expect(JSON.parse(calls[0]![1]!.body as string)).toEqual(fields)
    expect(calls[0]![1]!.credentials).toBe('same-origin')
    expect(new Headers(calls[0]![1]!.headers).has('X-CSRF-TOKEN')).toBe(true)
    expect(JSON.parse(calls[1]![1]!.body as string)).toEqual({name:'Updated',role:'customer',active:false,revision:row.revision})
    expect(String(calls[1]![0])).not.toContain(fields.password)
  })
  it('preserves the server privilege rejection rather than fabricating a changed account',async()=>{
    vi.spyOn(globalThis,'fetch').mockResolvedValue(new Response(JSON.stringify({message:'Forbidden'}),{status:403}))
    await expect(createAdminUser({name:'Admin',email:'admin@example.test',role:'admin',active:true,password:'InitialPass2026',password_confirmation:'InitialPass2026'})).rejects.toMatchObject({status:403})
  })
})
