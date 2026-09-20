import { defineStore } from 'pinia'
import { computed, ref, watch } from 'vue'

export type AccessLinkMode='permanent'|'expiring'
export interface CustomerRecord{
  id:number
  flag:string
  country:string
  name:string
  whatsapp:string
  email:string
  address:string
  location:string
  notes:string
  activeOrder:string
  orderCount:number
  timelineStage:string
  profileImage?:string
  passwordSet:boolean
  accessMode:AccessLinkMode
  accessToken:string
  accessExpiresAt:string
  accessRevoked:boolean
  favoritesUpdatedAt?:string
}
export interface WishlistLead{
  id:string
  kind:'customer'|'guest'
  customerId?:number
  visitorToken?:string
  label:string
  favoritesCount:number
  updatedAt:string
  contactAvailable:boolean
  messagePending:boolean
}

const CUSTOMER_KEY='armaghan:test19:customers'
const LEAD_KEY='armaghan:test19:wishlist-leads'

const seed:CustomerRecord[]=[
  {id:1,flag:'🇮🇶',country:'Iraq',name:'Baghdad Buyer',whatsapp:'+964 7XX XXX XXXX',email:'buyer@example.test',address:'Baghdad',location:'Baghdad, Iraq',notes:'',activeOrder:'در حال تولید',orderCount:3,timelineStage:'در حال تولید',passwordSet:true,accessMode:'expiring',accessToken:'',accessExpiresAt:'',accessRevoked:false,favoritesUpdatedAt:new Date(Date.now()-3600_000).toISOString()},
  {id:2,flag:'🇦🇪',country:'UAE',name:'Dubai Trade',whatsapp:'+971 5X XXX XXXX',email:'trade@example.test',address:'Dubai',location:'Dubai, UAE',notes:'',activeOrder:'بدون سفارش فعال',orderCount:1,timelineStage:'بدون مرحله فعال',passwordSet:false,accessMode:'permanent',accessToken:'',accessExpiresAt:'',accessRevoked:false},
  {id:3,flag:'🇹🇷',country:'Turkey',name:'Istanbul Store',whatsapp:'+90 5XX XXX XXXX',email:'store@example.test',address:'Istanbul',location:'Istanbul, Turkey',notes:'',activeOrder:'در انتظار پیش‌پرداخت',orderCount:2,timelineStage:'تأیید پیش‌پرداخت',passwordSet:true,accessMode:'expiring',accessToken:'',accessExpiresAt:'',accessRevoked:false,favoritesUpdatedAt:new Date(Date.now()-3*3600_000).toISOString()},
  {id:4,flag:'🇶🇦',country:'Qatar',name:'Doha Buyer',whatsapp:'+974 3XXX XXXX',email:'doha@example.test',address:'Doha',location:'Doha, Qatar',notes:'',activeOrder:'آماده ارسال',orderCount:4,timelineStage:'آماده ارسال',passwordSet:true,accessMode:'permanent',accessToken:'',accessExpiresAt:'',accessRevoked:false},
]

function loadCustomers():CustomerRecord[]{
  try{return JSON.parse(localStorage.getItem(CUSTOMER_KEY)??'null')??structuredClone(seed)}catch{return structuredClone(seed)}
}
function loadLeads():WishlistLead[]{
  try{
    const raw=localStorage.getItem(LEAD_KEY)
    if(raw)return JSON.parse(raw) as WishlistLead[]
  }catch{}
  return [
    {id:'c-1',kind:'customer',customerId:1,label:'Baghdad Buyer',favoritesCount:5,updatedAt:new Date(Date.now()-3600_000).toISOString(),contactAvailable:true,messagePending:false},
    {id:'g-a31',kind:'guest',visitorToken:'visitor-a31f',label:'Guest visitor',favoritesCount:3,updatedAt:new Date(Date.now()-2*3600_000).toISOString(),contactAvailable:false,messagePending:true},
  ]
}
function makeToken(){return crypto.getRandomValues(new Uint32Array(4)).join('').slice(0,22)}

export const useCustomersStore=defineStore('customers',()=>{
  const items=ref<CustomerRecord[]>(loadCustomers())
  const wishlistLeads=ref<WishlistLead[]>(loadLeads())

  const changedWishlistCount=computed(()=>wishlistLeads.value.length)
  function add(input:Pick<CustomerRecord,'name'|'email'|'whatsapp'>&Partial<CustomerRecord>){
    const id=Math.max(0,...items.value.map(item=>item.id))+1
    items.value=[...items.value,{
      id,flag:input.flag??'🌐',country:input.country??'',name:input.name,email:input.email,whatsapp:input.whatsapp,
      address:input.address??'',location:input.location??'',notes:input.notes??'',activeOrder:input.activeOrder??'بدون سفارش فعال',
      orderCount:input.orderCount??0,timelineStage:input.timelineStage??'بدون مرحله فعال',profileImage:input.profileImage,passwordSet:input.passwordSet??false,
      accessMode:input.accessMode??'expiring',accessToken:'',accessExpiresAt:'',accessRevoked:false,
    }]
  }
  function update(id:number,patch:Partial<CustomerRecord>){
    const row=items.value.find(item=>item.id===id)
    if(row)Object.assign(row,patch)
  }
  function remove(id:number){items.value=items.value.filter(item=>item.id!==id)}
  function removeMany(ids:number[]){const set=new Set(ids);items.value=items.value.filter(item=>!set.has(item.id))}
  function setProfileImage(id:number,value:string){update(id,{profileImage:value})}
  function generateAccess(id:number,mode:AccessLinkMode){
    const row=items.value.find(item=>item.id===id);if(!row)return ''
    const accessToken=makeToken()
    const accessExpiresAt=mode==='expiring'?new Date(Date.now()+72*3600_000).toISOString():''
    update(id,{accessMode:mode,accessToken,accessExpiresAt,accessRevoked:false})
    return `${location.origin}/User-${accessToken}`
  }
  function revokeAccess(id:number){update(id,{accessRevoked:true})}
  function removeLead(id:string){wishlistLeads.value=wishlistLeads.value.filter(item=>item.id!==id)}
  function queueGuestMessage(id:string){
    const lead=wishlistLeads.value.find(item=>item.id===id)
    if(lead)lead.messagePending=true
  }

  watch(items,value=>localStorage.setItem(CUSTOMER_KEY,JSON.stringify(value)),{deep:true})
  watch(wishlistLeads,value=>localStorage.setItem(LEAD_KEY,JSON.stringify(value)),{deep:true})
  return{items,wishlistLeads,changedWishlistCount,add,update,remove,removeMany,setProfileImage,generateAccess,revokeAccess,removeLead,queueGuestMessage}
})
