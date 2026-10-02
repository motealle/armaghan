import { defineStore } from 'pinia'
import { ref } from 'vue'
import { fetchAdminSession, logoutAdmin, type AdminIdentity } from './services/adminApi'
export const useAdminStore=defineStore('server-admin',()=>{
  const identity=ref<AdminIdentity|null>(null)
  const checked=ref(false)
  async function hydrate(){
    // Never inherit a browser-local review role or retain a stale positive session.
    identity.value=null
    try { identity.value=(await fetchAdminSession()).admin; return true }
    catch { return false }
    finally { checked.value=true }
  }
  async function logout(){
    await logoutAdmin(); identity.value=null
  }
  function clear(){identity.value=null}
  return {identity,checked,hydrate,logout,clear}
})
