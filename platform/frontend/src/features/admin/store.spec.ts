import { beforeEach, describe, expect, it, vi } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { useAdminStore } from './store'
import { fetchAdminSession, logoutAdmin } from './services/adminApi'
vi.mock('./services/adminApi',()=>({fetchAdminSession:vi.fn(),logoutAdmin:vi.fn()}))
beforeEach(()=>{setActivePinia(createPinia());vi.resetAllMocks()})
describe('real administrator state',()=>{
  it('never starts as authenticated and clears stale identity when a recheck fails',async()=>{
    const admin=useAdminStore()
    expect(admin.identity).toBeNull()
    vi.mocked(fetchAdminSession).mockResolvedValueOnce({admin:{name:'Owner',email:'owner@example.test'}})
    expect(await admin.hydrate()).toBe(true)
    vi.mocked(fetchAdminSession).mockRejectedValueOnce(new Error('Forbidden'))
    expect(await admin.hydrate()).toBe(false)
    expect(admin.identity).toBeNull()
  })
  it('does not claim logout when the server rejects it',async()=>{
    const admin=useAdminStore();admin.identity={name:'Owner',email:'owner@example.test'}
    vi.mocked(logoutAdmin).mockRejectedValueOnce(new Error('Unavailable'))
    await expect(admin.logout()).rejects.toThrow()
    expect(admin.identity).not.toBeNull()
    vi.mocked(logoutAdmin).mockResolvedValueOnce({ok:true})
    await admin.logout();expect(admin.identity).toBeNull()
  })
})
