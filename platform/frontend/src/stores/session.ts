import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import type { UserRole } from '@/types/domain'
import { useCustomersStore } from '@/stores/customers'
import {
  consumeCustomerMagicLink,
  fetchCustomerSession,
  logoutCustomerSession,
  updateCustomerSession,
  type BackendCustomerSession,
} from '@/features/auth/services/customerSessionApi'

const STORAGE_KEY='armaghan:test29:role'
const IMPERSONATION_KEY='armaghan:test29:impersonation'
const CUSTOMER_ID_KEY='armaghan:test29:customer-id'
const ACCOUNTS_KEY='armaghan:test29:accounts'
const MAGIC_KEY='armaghan:test29:magic-links'

type LocalAccount={id:number;name:string;email:string;password:string}
type MagicMode='permanent'|'expiring'
type MagicRecord={token:string;email:string;mode:MagicMode;expiresAt:number|null;revoked:boolean}

function readRole():UserRole{
  const value=sessionStorage.getItem(STORAGE_KEY)
  return value==='admin'||value==='customer'?value:'guest'
}
function readImpersonation():number|null{
  const raw=sessionStorage.getItem(IMPERSONATION_KEY)
  if(!raw)return null
  const value=Number(raw)
  return Number.isFinite(value)&&value>0?value:null
}
function readAccounts():LocalAccount[]{
  try{return JSON.parse(localStorage.getItem(ACCOUNTS_KEY)??'[]') as LocalAccount[]}catch{return[]}
}
function readMagic():MagicRecord[]{
  try{return JSON.parse(localStorage.getItem(MAGIC_KEY)??'[]') as MagicRecord[]}catch{return[]}
}
function token(){return crypto.getRandomValues(new Uint32Array(4)).join('').slice(0,24)}
function normalizePhone(value:string){
  const compact=value.trim().replace(/[\s().-]/g,'')
  return compact.startsWith('00')?('+'+compact.slice(2)):compact
}

export const useSessionStore=defineStore('session',()=>{
  const customers=useCustomersStore()
  const role=ref<UserRole>(readRole())
  const impersonatedCustomerId=ref<number|null>(readImpersonation())
  const accounts=ref<LocalAccount[]>(readAccounts())
  const magicLinks=ref<MagicRecord[]>(readMagic())
  const currentEmail=ref(sessionStorage.getItem('armaghan:test29:email')??'')
  const currentCustomerId=ref<number|null>(Number(sessionStorage.getItem(CUSTOMER_ID_KEY))||null)
  const backendAuthenticated=ref(false)
  const backendCustomer=ref<BackendCustomerSession|null>(null)

  // Root accepts only the real customer session; numbered lanes retain review fallback.
  const productionEntry=typeof window!=='undefined'&&(window.location.pathname==='/'||window.location.pathname==='/index.html')
  const isAuthenticated=computed(()=>productionEntry?backendAuthenticated.value:role.value!=='guest')
  const isAdmin=computed(()=>!productionEntry&&role.value==='admin')
  const isCustomer=computed(()=>productionEntry?backendAuthenticated.value:role.value==='customer')

  function persistRole(next:UserRole,email='',customerId:number|null=null){
    role.value=next
    currentEmail.value=email
    impersonatedCustomerId.value=null
    currentCustomerId.value=customerId
    if(next==='guest')sessionStorage.removeItem(STORAGE_KEY)
    else sessionStorage.setItem(STORAGE_KEY,next)
    if(email)sessionStorage.setItem('armaghan:test29:email',email)
    else sessionStorage.removeItem('armaghan:test29:email')
    if(customerId)sessionStorage.setItem(CUSTOMER_ID_KEY,String(customerId))
    else sessionStorage.removeItem(CUSTOMER_ID_KEY)
    sessionStorage.removeItem(IMPERSONATION_KEY)
  }

  function login(username:string,password:string):boolean{
    if(username==='1'&&password==='1'){persistRole('admin');return true}
    if(username==='2'&&password==='2'){persistRole('customer','',1);return true}
    const normalized=username.trim().toLowerCase()
    const normalizedPhone=normalizePhone(username)
    const managedCustomer=customers.items.find(item=>{
      if(!item.loginPassword||item.loginPassword!==password)return false
      const emailMatches=Boolean(item.email)&&item.email.trim().toLowerCase()===normalized
      const phoneMatches=Boolean(item.whatsapp)&&normalizePhone(item.whatsapp)===normalizedPhone
      return emailMatches||phoneMatches
    })
    if(managedCustomer){persistRole('customer',managedCustomer.email,managedCustomer.id);return true}
    const account=accounts.value.find(item=>item.email.toLowerCase()===normalized&&item.password===password)
    if(!account)return false
    persistRole('customer',account.email,account.id)
    return true
  }

  function register(name:string,email:string,password:string,customerId?:number):{ok:boolean;reason?:'exists'|'invalid';customerId?:number}{
    const cleanEmail=email.trim().toLowerCase()
    if(!name.trim()||!cleanEmail.includes('@')||password.length<4)return{ok:false,reason:'invalid'}
    if(accounts.value.some(item=>item.email.toLowerCase()===cleanEmail))return{ok:false,reason:'exists'}
    const id=customerId??(Math.max(1000,...accounts.value.map(item=>item.id))+1)
    accounts.value=[...accounts.value,{id,name:name.trim(),email:cleanEmail,password}]
    localStorage.setItem(ACCOUNTS_KEY,JSON.stringify(accounts.value))
    persistRole('customer',cleanEmail,id)
    return{ok:true,customerId:id}
  }

  function createMagicLink(email:string,mode:MagicMode='expiring',hours=72):string{
    const record:MagicRecord={
      token:token(),email:email.trim().toLowerCase()||'buyer@example.test',mode,
      expiresAt:mode==='expiring'?Date.now()+hours*3600_000:null,revoked:false,
    }
    magicLinks.value=[...magicLinks.value,record]
    localStorage.setItem(MAGIC_KEY,JSON.stringify(magicLinks.value))
    return `${location.origin}${location.pathname}?magic=${record.token}#/`
  }

  function revokeMagicLink(value:string){
    const row=magicLinks.value.find(item=>item.token===value)
    if(row)row.revoked=true
    localStorage.setItem(MAGIC_KEY,JSON.stringify(magicLinks.value))
  }

  function consumeMagicLink(value:string):boolean{
    const row=magicLinks.value.find(item=>item.token===value)
    if(!row||row.revoked)return false
    if(row.expiresAt&&Date.now()>row.expiresAt)return false
    persistRole('customer',row.email)
    return true
  }

  async function consumeBackendMagicLink(value:string):Promise<boolean>{
    if(!/^[A-Za-z0-9]{64}$/.test(value))return false
    try{
      const customer=await consumeCustomerMagicLink(value)
      backendAuthenticated.value=true
      backendCustomer.value=customer
      customers.upsertBackendCustomer(customer)
      persistRole('customer','',customer.id)
      return true
    }catch{
      return false
    }
  }

  async function hydrateFromBackend():Promise<boolean>{
    try{
      const customer=await fetchCustomerSession()
      if(!customer){
        backendAuthenticated.value=false
        backendCustomer.value=null
        return false
      }
      backendAuthenticated.value=true
      backendCustomer.value=customer
      customers.upsertBackendCustomer(customer)
      persistRole('customer','',customer.id)
      return true
    }catch{
      return false
    }
  }

  async function saveBackendCustomer(
    patch:Partial<Pick<BackendCustomerSession,'company_name'|'whatsapp'|'country_code'|'country_name'>>,
  ):Promise<boolean>{
    if(!backendAuthenticated.value)return false
    try{
      const customer=await updateCustomerSession(patch)
      backendCustomer.value=customer
      customers.upsertBackendCustomer(customer)
      return true
    }catch{
      return false
    }
  }

  function loginCustomerRecord(customerId:number,email=''){persistRole('customer',email,customerId)}
  async function logout():Promise<boolean>{
    if(backendAuthenticated.value){
      try{await logoutCustomerSession()}catch{return false}
    }
    backendAuthenticated.value=false
    backendCustomer.value=null
    persistRole('guest')
    return true
  }
  function impersonate(customerId:number){
    if(!isAdmin.value)return
    impersonatedCustomerId.value=customerId
    sessionStorage.setItem(IMPERSONATION_KEY,String(customerId))
  }
  function stopImpersonating(){
    impersonatedCustomerId.value=null
    sessionStorage.removeItem(IMPERSONATION_KEY)
  }

  return{
    role,isAuthenticated,isAdmin,isCustomer,impersonatedCustomerId,currentCustomerId,currentEmail,accounts,magicLinks,
    backendAuthenticated,backendCustomer,
    login,register,loginCustomerRecord,consumeBackendMagicLink,hydrateFromBackend,saveBackendCustomer,logout,impersonate,stopImpersonating,createMagicLink,revokeMagicLink,consumeMagicLink,
  }
})
