import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import type { UserRole } from '@/types/domain'

const STORAGE_KEY='armaghan:test19:role'
const IMPERSONATION_KEY='armaghan:test19:impersonation'
const ACCOUNTS_KEY='armaghan:test19:accounts'
const MAGIC_KEY='armaghan:test19:magic-links'

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

export const useSessionStore=defineStore('session',()=>{
  const role=ref<UserRole>(readRole())
  const impersonatedCustomerId=ref<number|null>(readImpersonation())
  const accounts=ref<LocalAccount[]>(readAccounts())
  const magicLinks=ref<MagicRecord[]>(readMagic())
  const currentEmail=ref(sessionStorage.getItem('armaghan:test19:email')??'')

  const isAuthenticated=computed(()=>role.value!=='guest')
  const isAdmin=computed(()=>role.value==='admin')
  const isCustomer=computed(()=>role.value==='customer')

  function persistRole(next:UserRole,email=''){
    role.value=next
    currentEmail.value=email
    impersonatedCustomerId.value=null
    if(next==='guest')sessionStorage.removeItem(STORAGE_KEY)
    else sessionStorage.setItem(STORAGE_KEY,next)
    if(email)sessionStorage.setItem('armaghan:test19:email',email)
    else sessionStorage.removeItem('armaghan:test19:email')
    sessionStorage.removeItem(IMPERSONATION_KEY)
  }

  function login(username:string,password:string):boolean{
    if(username==='1'&&password==='1'){persistRole('admin');return true}
    if(username==='2'&&password==='2'){persistRole('customer','buyer@example.test');return true}
    const account=accounts.value.find(item=>item.email.toLowerCase()===username.toLowerCase()&&item.password===password)
    if(!account)return false
    persistRole('customer',account.email)
    return true
  }

  function register(name:string,email:string,password:string):{ok:boolean;reason?:'exists'|'invalid'}{
    const cleanEmail=email.trim().toLowerCase()
    if(!name.trim()||!cleanEmail.includes('@')||password.length<4)return{ok:false,reason:'invalid'}
    if(accounts.value.some(item=>item.email.toLowerCase()===cleanEmail))return{ok:false,reason:'exists'}
    const id=Math.max(0,...accounts.value.map(item=>item.id))+1
    accounts.value=[...accounts.value,{id,name:name.trim(),email:cleanEmail,password}]
    localStorage.setItem(ACCOUNTS_KEY,JSON.stringify(accounts.value))
    persistRole('customer',cleanEmail)
    return{ok:true}
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

  function logout(){persistRole('guest')}
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
    role,isAuthenticated,isAdmin,isCustomer,impersonatedCustomerId,currentEmail,accounts,magicLinks,
    login,register,logout,impersonate,stopImpersonating,createMagicLink,revokeMagicLink,consumeMagicLink,
  }
})
