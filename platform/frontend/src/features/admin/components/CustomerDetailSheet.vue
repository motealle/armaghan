<script setup lang="ts">
import { computed, ref, toRaw, watch } from 'vue'
import { Camera, Copy, KeyRound, Link2, MapPin, PackageCheck, Save, ShieldOff, Star, UserRound } from '@lucide/vue'
import AdaptivePanel from '@/components/ui/AdaptivePanel.vue'
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

function cloneCustomer(row:CustomerRecord){return structuredClone(toRaw(row)) as CustomerRecord}
watch(()=>[props.open,props.customerId] as const,([open,id])=>{
  if(!open){draft.value=null;generatedLink.value='';note.value='';return}
  const row=customers.items.find(item=>item.id===id)
  draft.value=row?cloneCustomer(row):null
  generatedLink.value=''
  note.value=''
},{immediate:true})

const timelineSteps=computed(()=>[
  {key:'inquiryRegistered',done:true},
  {key:'specsApproved',done:['تأیید مشخصات','تأیید پیش‌پرداخت','در حال تولید','آماده ارسال'].includes(draft.value?.timelineStage??'')},
  {key:'prepaymentApproved',done:['تأیید پیش‌پرداخت','در حال تولید','آماده ارسال'].includes(draft.value?.timelineStage??'')},
  {key:'inProduction',done:['در حال تولید','آماده ارسال'].includes(draft.value?.timelineStage??'')},
])

function save(){
  if(!draft.value)return
  customers.update(draft.value.id,cloneCustomer(draft.value))
  note.value=locale.t('save')
}
async function upload(event:Event){
  const file=(event.target as HTMLInputElement).files?.[0]
  if(!file||!draft.value)return
  try{draft.value.profileImage=await compressImage(file)}catch{}
}
function setPriority(value:number){
  if(!draft.value)return
  draft.value.priorityStars=draft.value.priorityStars===value?0:value
}
function makeLink(){
  if(!draft.value)return
  save()
  generatedLink.value=customers.generateAccess(draft.value.id,draft.value.accessMode as AccessLinkMode)
  const fresh=customers.items.find(item=>item.id===draft.value?.id)
  if(fresh){
    draft.value.accessToken=fresh.accessToken
    draft.value.accessExpiresAt=fresh.accessExpiresAt
    draft.value.accessRevoked=false
  }
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
  <AdaptivePanel :open="open" :title="locale.t('customer360')" wide @close="emit('close')">
    <div v-if="draft" class="space-y-5">
      <section class="customer-profile-head">
        <div class="profile-photo">
          <img v-if="draft.profileImage" :src="draft.profileImage" alt="" />
          <span v-else>{{draft.flag||'🌐'}}</span>
        </div>
        <div class="min-w-0 flex-1">
          <b class="block truncate text-base text-[var(--c-text)]">{{draft.name}}</b>
          <span class="text-xs text-[var(--c-muted)]">{{draft.country||locale.t('country')}} · #{{draft.id}}</span>
        </div>
        <label class="mini-action cursor-pointer"><Camera :size="16"/>{{locale.t('profilePhoto')}}<input class="hidden" type="file" accept="image/*" @change="upload"></label>
      </section>

      <section class="grid grid-cols-2 gap-2 lg:grid-cols-4">
        <article class="customer-kpi"><Star :size="18"/><strong>{{draft.priorityStars??0}}/5</strong><span>{{locale.t('customerPriority')}}</span></article>
        <article class="customer-kpi"><PackageCheck :size="18"/><strong>{{draft.orderCount??0}}</strong><span>{{locale.t('previousOrders')}}</span></article>
        <article class="customer-kpi lg:col-span-2"><UserRound :size="18"/><strong class="truncate">{{locale.orderStatus(draft.activeOrder)}}</strong><span>{{locale.t('currentOrder')}}</span></article>
      </section>

      <section class="admin-surface rounded-2xl p-4">
        <div class="mb-3 flex items-center gap-2"><Star :size="18" class="text-amber-500"/><b class="text-sm">{{locale.t('customerPriority')}}</b></div>
        <div class="customer-stars" role="group" :aria-label="locale.t('customerPriority')">
          <button v-for="value in 5" :key="value" type="button" :aria-label="`${value}/5`" :aria-pressed="(draft.priorityStars??0)>=value" @click="setPriority(value)">
            <Star :size="24" :fill="(draft.priorityStars??0)>=value?'currentColor':'none'"/>
          </button>
        </div>
      </section>

      <section class="admin-surface rounded-2xl p-4">
        <div class="mb-3 flex items-center gap-2"><UserRound :size="18" class="text-[var(--c-primary)]"/><b class="text-sm">{{locale.t('contactInfo')}}</b></div>
        <div class="grid gap-3 md:grid-cols-2">
          <label class="form-field">{{locale.t('fullName')}}<input v-model="draft.name"/></label>
          <label class="form-field">{{locale.t('email')}}<input v-model="draft.email" type="email"/></label>
          <label class="form-field">WhatsApp<input v-model="draft.whatsapp" dir="ltr" inputmode="tel"/></label>
          <div class="md:col-span-2 rounded-xl border border-[var(--c-border)] bg-[var(--c-surface-2)] px-3 py-2 text-xs text-[var(--c-muted)]">
            <b class="text-[var(--c-text)]">{{locale.t('loginIdentifier')}}:</b>
            <span dir="ltr">{{draft.email || draft.whatsapp || '—'}}</span>
          </div>
          <label class="form-field">{{locale.t('country')}}<div class="grid grid-cols-[4.5rem_1fr] gap-2"><input v-model="draft.flag" :aria-label="locale.t('country')"/><input v-model="draft.country"/></div></label>
          <label class="form-field md:col-span-2">{{locale.t('address')}}<input v-model="draft.address"/></label>
          <label class="form-field md:col-span-2">{{locale.t('location')}}<span class="relative block"><MapPin :size="16" class="absolute start-3 top-1/2 mt-1 -translate-y-1/2 text-[var(--c-muted)]"/><input v-model="draft.location" class="ps-9"/></span></label>
          <label class="form-field md:col-span-2">{{locale.t('notes')}}<textarea v-model="draft.notes" rows="4"/></label>
        </div>
      </section>

      <section class="admin-surface rounded-2xl p-4">
        <div class="mb-3 flex items-center gap-2"><PackageCheck :size="18" class="text-[var(--c-primary)]"/><b class="text-sm">{{locale.t('ordersLabel')}} · {{locale.t('timeline')}}</b></div>
        <div class="grid gap-3 md:grid-cols-3">
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
          <label class="form-field">{{locale.t('timelineStage')}}
            <select v-model="draft.timelineStage">
              <option value="بدون مرحله فعال">{{locale.orderStatus('بدون مرحله فعال')}}</option>
              <option value="در حال مذاکره">{{locale.orderStatus('در حال مذاکره')}}</option>
              <option value="تأیید مشخصات">{{locale.t('specsApproved')}}</option>
              <option value="تأیید پیش‌پرداخت">{{locale.t('prepaymentApproved')}}</option>
              <option value="در حال تولید">{{locale.t('inProduction')}}</option>
              <option value="آماده ارسال">{{locale.orderStatus('آماده ارسال')}}</option>
            </select>
          </label>
        </div>
        <div class="customer-timeline mt-4">
          <div v-for="(step,index) in timelineSteps" :key="step.key" class="customer-timeline-step" :class="{done:step.done}">
            <span>{{index+1}}</span><b>{{locale.t(step.key)}}</b>
          </div>
        </div>
      </section>

      <section class="admin-surface rounded-2xl p-4">
        <div class="mb-3 flex items-center gap-2"><KeyRound :size="18" class="text-[var(--c-primary)]"/><b class="text-sm">{{locale.t('setPassword')}}</b></div>
        <div class="grid gap-3 md:grid-cols-[1fr_auto]">
          <label class="form-field">{{locale.t('password')}}<input v-model="draft.loginPassword" type="password" autocomplete="new-password" @input="draft.passwordSet=Boolean(draft.loginPassword)"/></label>
          <div class="flex items-end"><span class="rounded-xl bg-[var(--c-surface-2)] px-3 py-3 text-xs text-[var(--c-muted)]">{{draft.passwordSet?locale.t('active'):locale.t('setPassword')}}</span></div>
        </div>
      </section>

      <section class="admin-surface rounded-2xl p-4">
        <div class="mb-3 flex items-center gap-2"><Link2 :size="18" class="text-[var(--c-primary)]"/><b class="text-sm">{{locale.t('directAccess')}}</b></div>
        <div class="grid gap-2 sm:grid-cols-2">
          <button class="admin-display-mode" :class="{active:draft.accessMode==='permanent'}" @click="draft.accessMode='permanent'"><b>{{locale.t('permanent')}}</b><span>{{locale.t('directAccess')}}</span></button>
          <button class="admin-display-mode" :class="{active:draft.accessMode==='expiring'}" @click="draft.accessMode='expiring'"><b>{{locale.t('expiring')}}</b><span>72 h</span></button>
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

      <div class="sticky bottom-0 z-10 flex items-center justify-end gap-2 border-t border-[var(--c-border)] bg-[color-mix(in_srgb,var(--c-surface)_96%,transparent)] py-3 backdrop-blur">
        <span v-if="note" class="me-auto text-xs font-bold text-[var(--c-secondary)]">{{note}}</span>
        <button class="inline-flex min-h-11 items-center gap-2 rounded-xl bg-[var(--c-primary)] px-4 text-sm font-black text-white" @click="save"><Save :size="17"/>{{locale.t('save')}}</button>
      </div>
    </div>

    <div v-else class="empty-panel-state">
      <UserRound :size="28"/>
      <b>{{locale.t('customerNotFound')}}</b>
    </div>
  </AdaptivePanel>
</template>
