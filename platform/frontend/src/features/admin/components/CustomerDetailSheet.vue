<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Camera, Copy, KeyRound, Link2, ShieldOff } from '@lucide/vue'
import BaseSheet from '@/components/ui/BaseSheet.vue'
import { compressImage } from '@/features/admin/services/imageCompression'
import { useCustomersStore, type AccessLinkMode, type CustomerRecord } from '@/stores/customers'
import { useLocaleStore } from '@/stores/locale'

const props=defineProps<{open:boolean;customerId:number|null}>()
const emit=defineEmits<{close:[]}>()
const customers=useCustomersStore()
const locale=useLocaleStore()
const draft=ref<CustomerRecord|null>(null)
const generatedLink=ref('')
const note=ref('')

const source=computed(()=>customers.items.find(item=>item.id===props.customerId)??null)
watch([()=>props.open,source],([open,row])=>{
  if(open&&row){draft.value=structuredClone(row);generatedLink.value='';note.value=''}
},{immediate:true})

function save(){
  if(!draft.value)return
  customers.update(draft.value.id,structuredClone(draft.value))
  note.value=locale.t('save')
}
async function upload(event:Event){
  const file=(event.target as HTMLInputElement).files?.[0]
  if(!file||!draft.value)return
  try{draft.value.profileImage=await compressImage(file)}catch{}
}
function makeLink(){
  if(!draft.value)return
  save()
  generatedLink.value=customers.generateAccess(draft.value.id,draft.value.accessMode as AccessLinkMode)
  draft.value.accessToken=customers.items.find(item=>item.id===draft.value?.id)?.accessToken??''
  draft.value.accessExpiresAt=customers.items.find(item=>item.id===draft.value?.id)?.accessExpiresAt??''
  draft.value.accessRevoked=false
}
function revoke(){
  if(!draft.value)return
  customers.revokeAccess(draft.value.id)
  draft.value.accessRevoked=true
}
async function copy(){
  if(!generatedLink.value)return
  try{await navigator.clipboard.writeText(generatedLink.value)}catch{}
}
</script>

<template>
  <BaseSheet :open="open" :title="locale.t('customer360')" @close="emit('close')">
    <div v-if="draft" class="space-y-5">
      <section class="customer-profile-head">
        <div class="profile-photo">
          <img v-if="draft.profileImage" :src="draft.profileImage" alt="" />
          <span v-else>{{draft.flag}}</span>
        </div>
        <div class="min-w-0 flex-1">
          <b class="block truncate text-base text-[var(--c-text)]">{{draft.name}}</b>
          <span class="text-xs text-[var(--c-muted)]">{{draft.country}} · #{{draft.id}}</span>
        </div>
        <label class="mini-action cursor-pointer"><Camera :size="16"/>{{locale.t('profilePhoto')}}<input class="hidden" type="file" accept="image/*" @change="upload"></label>
      </section>

      <section class="grid gap-3 md:grid-cols-2">
        <label class="form-field">{{locale.t('fullName')}}<input v-model="draft.name"/></label>
        <label class="form-field">{{locale.t('email')}}<input v-model="draft.email" type="email"/></label>
        <label class="form-field">WhatsApp<input v-model="draft.whatsapp" dir="ltr"/></label>
        <label class="form-field">{{locale.t('country')}}<div class="grid grid-cols-[4.5rem_1fr] gap-2"><input v-model="draft.flag" :aria-label="locale.t('country')"/><input v-model="draft.country"/></div></label>
        <label class="form-field md:col-span-2">{{locale.t('address')}}<input v-model="draft.address"/></label>
        <label class="form-field md:col-span-2">{{locale.t('location')}}<input v-model="draft.location"/></label>
        <label class="form-field">{{locale.t('previousOrders')}}<input v-model.number="draft.orderCount" type="number" min="0"/></label>
        <label class="form-field">{{locale.t('currentOrder')}}
          <select v-model="draft.activeOrder">
            <option value="بدون سفارش فعال">{{locale.orderStatus('بدون سفارش فعال')}}</option>
            <option value="در حال مذاکره">{{locale.orderStatus('در حال مذاکره')}}</option>
            <option value="در انتظار پیش‌پرداخت">{{locale.orderStatus('در انتظار پیش‌پرداخت')}}</option>
            <option value="در حال تولید">{{locale.orderStatus('در حال تولید')}}</option>
            <option value="آماده ارسال">{{locale.orderStatus('آماده ارسال')}}</option>
          </select>
        </label>
        <label class="form-field md:col-span-2">{{locale.t('timelineStage')}}
          <select v-model="draft.timelineStage">
            <option value="بدون مرحله فعال">{{locale.orderStatus('بدون مرحله فعال')}}</option>
            <option value="در حال مذاکره">{{locale.orderStatus('در حال مذاکره')}}</option>
            <option value="تأیید پیش‌پرداخت">{{locale.orderStatus('تأیید پیش‌پرداخت')}}</option>
            <option value="در حال تولید">{{locale.orderStatus('در حال تولید')}}</option>
            <option value="آماده ارسال">{{locale.orderStatus('آماده ارسال')}}</option>
          </select>
        </label>
        <label class="form-field md:col-span-2">{{locale.t('notes')}}<textarea v-model="draft.notes" rows="4"/></label>
      </section>

      <section class="rounded-2xl border border-[var(--c-border)] bg-[var(--c-surface-2)] p-3">
        <div class="mb-3 flex items-center gap-2"><KeyRound :size="18" class="text-[var(--c-primary)]"/><b class="text-sm">{{locale.t('setPassword')}}</b></div>
        <label class="flex items-center gap-2 text-xs text-[var(--c-muted)]">
          <input v-model="draft.passwordSet" type="checkbox"> {{draft.passwordSet?locale.t('active'):locale.t('setPassword')}}
        </label>
      </section>

      <section class="rounded-2xl border border-[var(--c-border)] bg-[var(--c-surface)] p-3">
        <div class="mb-3 flex items-center gap-2"><Link2 :size="18" class="text-[var(--c-primary)]"/><b class="text-sm">{{locale.t('directAccess')}}</b></div>
        <div class="grid gap-2 sm:grid-cols-2">
          <button class="admin-display-mode" :class="{active:draft.accessMode==='permanent'}" @click="draft.accessMode='permanent'"><b>{{locale.t('permanent')}}</b><span>{{locale.t('directAccess')}}</span></button>
          <button class="admin-display-mode" :class="{active:draft.accessMode==='expiring'}" @click="draft.accessMode='expiring'"><b>{{locale.t('expiring')}}</b><span>72h</span></button>
        </div>
        <div class="mt-3 flex flex-wrap gap-2">
          <button class="mini-action bg-[var(--c-primary)] text-white" @click="makeLink"><Link2 :size="15"/>{{locale.t('generateLink')}}</button>
          <button v-if="draft.accessToken&&!draft.accessRevoked" class="mini-action text-rose-700" @click="revoke"><ShieldOff :size="15"/>{{locale.t('revoke')}}</button>
        </div>
        <div v-if="generatedLink" class="magic-link-box mt-3">
          <input :value="generatedLink" readonly dir="ltr"/>
          <button :aria-label="locale.t('copy')" @click="copy"><Copy :size="17"/></button>
        </div>
        <div v-if="draft.accessExpiresAt&&draft.accessMode==='expiring'" class="mt-2 text-[11px] text-[var(--c-muted)]">{{locale.t('expiresAt')}}: {{new Date(draft.accessExpiresAt).toLocaleString()}}</div>
      </section>

      <div class="flex items-center justify-end gap-2">
        <span v-if="note" class="me-auto text-xs text-[var(--c-secondary)]">{{note}}</span>
        <button class="mini-action bg-[var(--c-primary)] text-white" @click="save">{{locale.t('save')}}</button>
      </div>
    </div>
  </BaseSheet>
</template>
