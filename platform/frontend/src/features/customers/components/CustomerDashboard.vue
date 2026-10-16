<script setup lang="ts">
import OrderTrackingPanel from '@/features/orders/components/OrderTrackingPanel.vue'
import { computed, reactive, ref, watch } from 'vue'
import { Camera, Check, Clock3, Heart, RotateCcw, Save, UserRound } from '@lucide/vue'
import { compressImage } from '@/features/admin/services/imageCompression'
import { useCustomersStore } from '@/stores/customers'
import { useLocaleStore } from '@/stores/locale'
import { useSessionStore } from '@/stores/session'

const locale=useLocaleStore()
const session=useSessionStore()
const customers=useCustomersStore()
const timelineKeys=['inquiryRegistered','specsApproved','prepaymentApproved','inProduction']
const customer=computed(()=>{
  const id=session.impersonatedCustomerId??session.currentCustomerId
  return id?customers.items.find(item=>item.id===id)??null:null
})
const profile=reactive({name:'',email:'',whatsapp:'',address:'',location:''})
const business=reactive({
  contact_name:'',phone:'',contact_email:'',preferred_language:'',
  city:'',district:'',shop_number:'',sales_product_group:'',
  purchase_volume:'',cooperation_type:'',sales_type:'',
})
const businessFields=[
  {key:'contact_name',label:'customerContactName'},
  {key:'phone',label:'customerPhone'},
  {key:'contact_email',label:'customerContactEmail'},
  {key:'city',label:'customerCity'},
  {key:'district',label:'customerDistrict'},
  {key:'shop_number',label:'customerShopNumber'},
  {key:'sales_product_group',label:'customerSalesProductGroup'},
  {key:'purchase_volume',label:'customerPurchaseVolume'},
  {key:'cooperation_type',label:'customerCooperationType'},
  {key:'sales_type',label:'customerSalesType'},
] as const
const saving=ref(false),saveError=ref(''),saved=ref(false)
const clean=(value:string)=>value.trim()||null
watch(()=>session.backendCustomer,row=>{
  if(!row)return
  profile.name=row.company_name??''
  profile.email=row.email??''
  profile.whatsapp=row.whatsapp??''
  for(const field of businessFields)business[field.key]=row[field.key]??''
  business.preferred_language=row.preferred_language??''
},{immediate:true})

watch(customer,(row)=>{
  if(!row)return
  Object.assign(profile,{name:row.name,email:row.email,whatsapp:row.whatsapp,address:row.address,location:row.location})
},{immediate:true})

async function upload(event:Event){
  const file=(event.target as HTMLInputElement).files?.[0]
  if(!file||!customer.value)return
  try{customers.setProfileImage(customer.value.id,await compressImage(file))}catch{}
}
async function saveProfile(){
  if(saving.value)return
  saveError.value='';saved.value=false;saving.value=true
  try{
    if(session.backendAuthenticated&&!session.impersonatedCustomerId){
      const ok=await session.saveBackendCustomer({
        company_name:clean(profile.name),whatsapp:clean(profile.whatsapp),
        contact_name:clean(business.contact_name),phone:clean(business.phone),
        contact_email:clean(business.contact_email),preferred_language:clean(business.preferred_language),
        city:clean(business.city),district:clean(business.district),
        shop_number:clean(business.shop_number),sales_product_group:clean(business.sales_product_group),
        purchase_volume:clean(business.purchase_volume),cooperation_type:clean(business.cooperation_type),
        sales_type:clean(business.sales_type),
      })
      if(!ok)throw new Error('Save failed')
    }else if(customer.value){
      customers.update(customer.value.id,{...profile})
    }
    saved.value=true
  }catch{
    saveError.value=({fa:'ذخیره انجام نشد؛ دوباره تلاش کنید.',en:'Could not save. Please retry.',ar:'تعذر الحفظ؛ حاول مرة أخرى.',ku:'پاشەکەوت نەکرا؛ دووبارە هەوڵ بدەوە.'})[locale.locale]
  }finally{saving.value=false}
}
</script>
<template>
  <div class="space-y-4">
    <OrderTrackingPanel v-if="session.backendAuthenticated&&!session.impersonatedCustomerId"/>
    <section v-if="customer" class="admin-surface rounded-2xl p-4">
      <div class="customer-profile-head">
        <div class="profile-photo"><img v-if="customer.profileImage" :src="customer.profileImage" alt=""><span v-else>{{customer.flag}}</span></div>
        <div class="min-w-0 flex-1"><b class="block truncate text-base text-[var(--c-text)]">{{customer.name}}</b><span class="text-xs text-[var(--c-muted)]">{{customer.country}}</span></div>
        <label v-if="!session.backendAuthenticated" class="mini-action cursor-pointer"><Camera :size="16"/>{{locale.t('profilePhoto')}}<input class="hidden" type="file" accept="image/*" @change="upload"></label>
      </div>
      <div class="mt-3 grid gap-2 md:grid-cols-2">
        <label class="form-field">{{locale.t(session.backendAuthenticated?'customerStoreName':'fullName')}}<input v-model="profile.name" maxlength="255"></label>
        <label class="form-field">{{locale.t('email')}}<input v-model="profile.email" type="email" :readonly="session.backendAuthenticated" dir="ltr"></label>
        <label class="form-field">WhatsApp<input v-model="profile.whatsapp" dir="ltr"></label>
        <label v-if="!session.backendAuthenticated" class="form-field">{{locale.t('address')}}<input v-model="profile.address"></label>
        <label v-if="!session.backendAuthenticated" class="form-field md:col-span-2">{{locale.t('location')}}<input v-model="profile.location"></label>
      </div>
      <fieldset v-if="session.backendAuthenticated&&!session.impersonatedCustomerId" class="mt-4 grid gap-3 rounded-xl border border-[var(--c-border)] p-3 md:grid-cols-2" :disabled="saving">
        <legend class="px-2 font-bold">{{locale.t('customerBusinessGroup')}}</legend>
        <label v-for="field in businessFields" :key="field.key" class="form-field">
          {{locale.t(field.label)}}<input v-model="business[field.key]" :type="field.key==='contact_email'?'email':'text'" :dir="field.key==='phone'||field.key==='contact_email'?'ltr':undefined" :maxlength="field.key==='phone'?64:255" :inputmode="field.key==='phone'?'tel':undefined">
        </label>
        <label class="form-field">{{locale.t('customerPreferredLanguage')}}<select v-model="business.preferred_language">
          <option value="">—</option><option value="fa">فارسی</option><option value="en">English</option><option value="ar">العربية</option><option value="ku">کوردی</option>
        </select></label>
      </fieldset>
      <div v-if="session.backendAuthenticated" class="mt-3"><a class="mini-action" href="/backend/account/security">{{locale.t('accountPassword')}}</a></div>
      <p v-if="saveError" role="alert" class="mt-2 text-sm text-red-600">{{saveError}}</p>
      <p v-if="saved" role="status" class="mt-2 text-sm text-[var(--c-secondary)]">{{locale.t('adminSaved')}}</p>
      <div class="mt-3 flex justify-end"><button class="mini-action bg-[var(--c-primary)] text-white" :disabled="saving" @click="saveProfile"><Save :size="15"/>{{locale.t('save')}}</button></div>
    </section>

    <div v-else class="empty-panel-state">
      <UserRound :size="28"/>
      <b>{{locale.t('customerNotFound')}}</b>
    </div>

    <div v-if="customer&&!session.backendAuthenticated" class="grid gap-4 md:grid-cols-2">
      <article class="rounded-2xl border border-[var(--c-border)] bg-[var(--c-surface)] p-4 shadow-sm">
        <div class="flex items-center justify-between"><h2 class="font-black text-[var(--c-text)]">{{locale.t('timeline')}}</h2><span class="rounded-full bg-[color-mix(in_srgb,var(--c-secondary)_10%,var(--c-surface))] px-2 py-1 text-[10px] font-bold text-[var(--c-secondary)]">{{locale.t('active')}}</span></div>
        <div class="mt-4 space-y-4">
          <div v-for="(key,index) in timelineKeys" :key="key" class="flex items-center gap-3">
            <span class="grid h-8 w-8 place-items-center rounded-full" :class="index<3?'bg-[color-mix(in_srgb,var(--c-secondary)_12%,var(--c-surface))] text-[var(--c-secondary)]':'bg-[var(--c-surface-2)] text-[var(--c-muted)]'"><Check v-if="index<3" :size="17"/><Clock3 v-else :size="17"/></span>
            <span class="text-sm text-[var(--c-text)]">{{locale.t(key)}}</span>
          </div>
        </div>
      </article>
      <article class="rounded-2xl border border-[var(--c-border)] bg-[var(--c-surface)] p-4 shadow-sm">
        <h2 class="font-black text-[var(--c-text)]">{{locale.t('quickAccess')}}</h2>
        <div class="mt-3 grid gap-2">
          <button class="quick-account"><RotateCcw :size="18"/>{{locale.t('repeatOrder')}}</button>
          <RouterLink to="/favorites" class="quick-account"><Heart :size="18"/>{{locale.t('favorites')}}</RouterLink>
          <button class="quick-account"><Check :size="18"/>{{locale.t('approvePrepayment')}}</button>
        </div>
      </article>
    </div>
  </div>
</template>
