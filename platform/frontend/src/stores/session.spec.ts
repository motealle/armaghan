import { beforeEach, describe, expect, it, vi } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { useSessionStore } from './session'
import { useCustomersStore } from './customers'

class MemoryStorage {
  private data = new Map<string,string>()
  getItem(key:string){return this.data.get(key) ?? null}
  setItem(key:string,value:string){this.data.set(key,String(value))}
  removeItem(key:string){this.data.delete(key)}
  clear(){this.data.clear()}
  key(index:number){return [...this.data.keys()][index] ?? null}
  get length(){return this.data.size}
}

describe('session store',()=>{
  beforeEach(()=>{
    Object.defineProperty(globalThis,'sessionStorage',{value:new MemoryStorage(),configurable:true})
    Object.defineProperty(globalThis,'localStorage',{value:new MemoryStorage(),configurable:true})
    vi.restoreAllMocks()
    setActivePinia(createPinia())
  })

  it('logs in admin and always logs out cleanly',()=>{
    const store=useSessionStore()
    expect(store.login('1','1')).toBe(true)
    expect(store.role).toBe('admin')
    store.impersonate(42)
    expect(store.impersonatedCustomerId).toBe(42)
    store.logout()
    expect(store.role).toBe('guest')
    expect(store.impersonatedCustomerId).toBeNull()
    expect(sessionStorage.getItem('armaghan:test27:role')).toBeNull()
    expect(sessionStorage.getItem('armaghan:test27:impersonation')).toBeNull()
  })

  it('supports customer demo login',()=>{
    const store=useSessionStore()
    expect(store.login('2','2')).toBe(true)
    expect(store.role).toBe('customer')
  })

  it('restores explicit admin impersonation inside the same browser session',()=>{
    sessionStorage.setItem('armaghan:test27:role','admin')
    sessionStorage.setItem('armaghan:test27:impersonation','7')
    const store=useSessionStore()
    expect(store.role).toBe('admin')
    expect(store.impersonatedCustomerId).toBe(7)
  })
  it('hydrates a real backend customer session and overlays the local customer record',async()=>{
    const fetchMock=vi.fn().mockResolvedValue({
      ok:true,
      status:200,
      json:async()=>({
        customer:{
          id:9,
          company_name:'Real Backend Buyer',
          whatsapp:'+9647111111111',
          country_code:'IQ',
          country_name:'Iraq',
        },
      }),
    } as Response)
    vi.stubGlobal('fetch',fetchMock)

    const store=useSessionStore()
    const customers=useCustomersStore()

    expect(await store.hydrateFromBackend()).toBe(true)
    expect(store.backendAuthenticated).toBe(true)
    expect(store.role).toBe('customer')
    expect(store.currentCustomerId).toBe(9)
    expect(customers.items.find(item=>item.id===9)?.name).toBe('Real Backend Buyer')
    expect(fetchMock).toHaveBeenCalledWith('/backend/api/customer/session',expect.objectContaining({
      credentials:'same-origin',
    }))
  })

  it('keeps local demo state when no backend customer session exists',async()=>{
    const fetchMock=vi.fn().mockResolvedValue({
      ok:false,
      status:401,
      json:async()=>({message:'Unauthenticated.'}),
    } as Response)
    vi.stubGlobal('fetch',fetchMock)

    sessionStorage.setItem('armaghan:test27:role','admin')
    const store=useSessionStore()

    expect(await store.hydrateFromBackend()).toBe(false)
    expect(store.backendAuthenticated).toBe(false)
    expect(store.role).toBe('admin')
  })

  it('consumes a backend Magic Link token through the fixed POST endpoint',async()=>{
    const token='A'.repeat(64)
    const fetchMock=vi.fn()
      .mockResolvedValueOnce({
        ok:true,
        status:200,
        json:async()=>({token:'csrf-test-token'}),
      } as Response)
      .mockResolvedValueOnce({
        ok:true,
        status:200,
        json:async()=>({
          customer:{
            id:12,
            company_name:'Magic Buyer',
            whatsapp:'+9647222222222',
            country_code:'IQ',
            country_name:'Iraq',
          },
        }),
      } as Response)
    vi.stubGlobal('fetch',fetchMock)

    const store=useSessionStore()
    expect(await store.consumeBackendMagicLink(token)).toBe(true)
    expect(store.backendAuthenticated).toBe(true)
    expect(store.currentCustomerId).toBe(12)
    expect(store.role).toBe('customer')

    expect(fetchMock).toHaveBeenNthCalledWith(2,
      '/backend/api/customer/magic-link/consume',
      expect.objectContaining({
        method:'POST',
        credentials:'same-origin',
      }),
    )
    const secondCall=fetchMock.mock.calls[1]
    expect(JSON.parse(String((secondCall[1] as RequestInit).body))).toEqual({token})
  })

  it('rejects malformed Magic Link tokens before any network call',async()=>{
    const fetchMock=vi.fn()
    vi.stubGlobal('fetch',fetchMock)

    const store=useSessionStore()
    expect(await store.consumeBackendMagicLink('short')).toBe(false)
    expect(fetchMock).not.toHaveBeenCalled()
  })

})
