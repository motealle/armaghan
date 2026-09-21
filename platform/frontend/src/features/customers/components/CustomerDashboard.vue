<script setup lang="ts">
import { computed, reactive, watch } from 'vue'
import { Camera, Check, Clock3, Heart, RotateCcw, Save } from '@lucide/vue'
import { compressImage } from '@/features/admin/services/imageCompression'
import { useCustomersStore } from '@/stores/customers'
import { useLocaleStore } from '@/stores/locale'
import { useSessionStore } from '@/stores/session'

const locale=useLocaleStore()
const session=useSessionStore()
const customers=useCustomersStore()
const timelineKeys=['inquiryRegistered','specsApproved','prepaymentApproved','inProduction']
const customer=computed(()=>customers.items.find(item=>item.id===(session.impersonatedCustomerId??session.currentCustomerId??1))??customers.items[0]??null)
const profile=reactive({name:'',email:'',whatsapp:'',address:'',location:''})

watch(customer,(row)=>{
  if(!row)return
  Object.assign(profile,{name:row.name,email:row.email,whatsapp:row.whatsapp,address:row.address,location:row.location})
},{immediate:true})

async function upload(event:Event){
  const file=(event.target as HTMLInputElement).files?.[0]
  if(!file||!customer.value)return
  try{customers.setProfileImage(customer.value.id,await compressImage(file))}catch{}
}
function saveProfile(){
  if(!customer.value)return
  customers.update(customer.value.id,{...profile})
}
</script>
<template>
  <div class="space-y-4">
    <section v-if="customer" class="admin-surface rounded-2xl p-4">
      <div class="customer-profile-head">
        <div class="profile-photo"><img v-if="customer.profileImage" :src="customer.profileImage" alt=""><span v-else>{{customer.flag}}</span></div>
        <div class="min-w-0 flex-1"><b class="block truncate text-base text-[var(--c-text)]">{{customer.name}}</b><span class="text-xs text-[var(--c-muted)]">{{customer.country}}</span></div>
        <label class="mini-action cursor-pointer"><Camera :size="16"/>{{locale.t('profilePhoto')}}<input class="hidden" type="file" accept="image/*" @change="upload"></label>
      </div>
      <div class="mt-3 grid gap-2 md:grid-cols-2">
        <label class="form-field">{{locale.t('fullName')}}<input v-model="profile.name"></label>
        <label class="form-field">{{locale.t('email')}}<input v-model="profile.email" type="email"></label>
        <label class="form-field">WhatsApp<input v-model="profile.whatsapp" dir="ltr"></label>
        <label class="form-field">{{locale.t('address')}}<input v-model="profile.address"></label>
        <label class="form-field md:col-span-2">{{locale.t('location')}}<input v-model="profile.location"></label>
      </div>
      <div class="mt-3 flex justify-end"><button class="mini-action bg-[var(--c-primary)] text-white" @click="saveProfile"><Save :size="15"/>{{locale.t('save')}}</button></div>
    </section>

    <div class="grid gap-4 md:grid-cols-2">
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
