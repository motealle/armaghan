import { beforeEach, describe, expect, it } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { useSessionStore } from './session'

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
    expect(sessionStorage.getItem('armaghan:test19:role')).toBeNull()
    expect(sessionStorage.getItem('armaghan:test19:impersonation')).toBeNull()
  })

  it('supports customer demo login',()=>{
    const store=useSessionStore()
    expect(store.login('2','2')).toBe(true)
    expect(store.role).toBe('customer')
  })

  it('restores explicit admin impersonation inside the same browser session',()=>{
    sessionStorage.setItem('armaghan:test19:role','admin')
    sessionStorage.setItem('armaghan:test19:impersonation','7')
    const store=useSessionStore()
    expect(store.role).toBe('admin')
    expect(store.impersonatedCustomerId).toBe(7)
  })
})
