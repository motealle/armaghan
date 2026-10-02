import { afterEach, describe, expect, it, vi } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { useSessionStore } from './session'
vi.mock('@/features/auth/services/customerSessionApi',()=>({
  fetchCustomerSession:vi.fn(async()=>({id:7,company_name:'Buyer',whatsapp:null,country_code:null,country_name:null})),
  consumeCustomerMagicLink:vi.fn(), logoutCustomerSession:vi.fn(), updateCustomerSession:vi.fn(),
}))
function storage(){
  const values=new Map<string,string>()
  return {getItem:(k:string)=>values.get(k)??null,setItem:(k:string,v:string)=>{values.set(k,v)},removeItem:(k:string)=>{values.delete(k)}}
}
function setup(path:string){
  vi.stubGlobal('window',{location:{pathname:path}})
  vi.stubGlobal('localStorage',storage())
  vi.stubGlobal('sessionStorage',storage())
  sessionStorage.setItem('armaghan:test29:role','admin')
  setActivePinia(createPinia())
  return useSessionStore()
}
afterEach(()=>vi.unstubAllGlobals())
describe('root production authentication',()=>{
  it('ignores a browser-only administrator role while keeping it untouched',()=>{
    const session=setup('/')
    expect(session.isAuthenticated).toBe(false)
    expect(session.isAdmin).toBe(false)
    expect(sessionStorage.getItem('armaghan:test29:role')).toBe('admin')
  })
  it('recognizes a real backend customer session without granting admin access',async()=>{
    const session=setup('/')
    expect(await session.hydrateFromBackend()).toBe(true)
    expect(session.isAuthenticated).toBe(true)
    expect(session.currentCustomerId).toBe(7)
    expect(session.isAdmin).toBe(false)
  })
  it('retains the existing numbered review fallback',()=>{
    const session=setup('/t/29/')
    expect(session.isAdmin).toBe(true)
  })
})
