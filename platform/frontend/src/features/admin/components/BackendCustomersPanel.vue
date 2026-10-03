<script setup lang="ts">
import CustomerAccountPanel from './CustomerAccountPanel.vue'
import RecordTags from './RecordTags.vue'
import BulkStatusBar from './BulkStatusBar.vue'
import { computed, onMounted, ref } from 'vue'
import { Plus, Save, RefreshCw } from '@lucide/vue'
import AdaptivePanel from '@/components/ui/AdaptivePanel.vue'
import { useLocaleStore } from '@/stores/locale'
import { useAdminStore } from '../store'
import { CustomerSessionApiError } from '@/features/auth/services/customerSessionApi'
import { fetchAdminCustomers, createAdminCustomer, updateAdminCustomer, type AdminCustomer, type CustomerPage, type CustomerFields } from '../services/adminApi'
const locale=useLocaleStore()
const admin=useAdminStore()
const result=ref<CustomerPage>({customers:[],page:1,last_page:1,total:0})
const search=ref('')
const loading=ref(false)
const saving=ref(false)
const error=ref('')
const note=ref('')
const open=ref(false)
const selected=ref<AdminCustomer|null>(null)
const accountCustomer=ref<AdminCustomer|null>(null),accountBusy=ref(false)
async function accountSaved(){
  accountCustomer.value=null;note.value=locale.t('adminSaved')
  try{result.value=await fetchAdminCustomers(result.value.page,search.value.trim())}catch(e){error.value=failed(e)}
}
const draft=ref<CustomerFields|null>(null)
let requestId=0
function failed(e:unknown){
  if(e instanceof CustomerSessionApiError&&[401,403].includes(e.status)){
    admin.clear();result.value={customers:[],page:1,last_page:1,total:0};draft.value=null;open.value=false
    return locale.t('adminSessionRequired')
  }
  return locale.t(e instanceof CustomerSessionApiError&&e.status===409?'adminConflict':'adminRequestFailed')
}
async function load(page=1){
  if(loading.value||saving.value||bulkBusy.value||accountBusy.value)return
  checked.value=[]
  const id=++requestId;loading.value=true;error.value=''
  try{const response=await fetchAdminCustomers(page,search.value.trim());if(id===requestId)result.value=response}
  catch(e){if(id===requestId){result.value={customers:[],page:1,last_page:1,total:0};error.value=failed(e)}}
  finally{if(id===requestId)loading.value=false}
}
function edit(row:AdminCustomer|null){
  selected.value=row;error.value='';note.value=''
  draft.value=row?{company_name:row.company_name||row.name||'',whatsapp:row.whatsapp,country_code:row.country_code,country_name:row.country_name,notes:row.notes,priority:row.priority,active:row.active,direct_link_enabled:row.direct_link_enabled}:{company_name:'',whatsapp:null,country_code:null,country_name:null,notes:null,priority:0,active:true,direct_link_enabled:false}
  open.value=true
}
async function save(){
  if(!draft.value||saving.value||loading.value)return
  saving.value=true;error.value='';note.value=''
  try{
    const fields={...draft.value,country_code:draft.value.country_code?.trim().toUpperCase()||null}
    if(selected.value)await updateAdminCustomer(selected.value.id,fields,selected.value.revision)
    else await createAdminCustomer(fields)
    // Only server success closes the editor. Re-read canonical rows to prove persistence.
    open.value=false;draft.value=null;selected.value=null;note.value=locale.t('adminSaved')
    result.value=await fetchAdminCustomers(result.value.page,search.value.trim())
  }catch(e){error.value=failed(e)}finally{saving.value=false}
}
function close(){if(!saving.value){open.value=false;draft.value=null;selected.value=null;error.value=''}}
const bulkBusy=ref(false),checked=ref<number[]>([])
const bulkRows=computed(()=>result.value.customers.filter(row=>checked.value.includes(row.id)))
const selectable=computed(()=>result.value.customers.filter(row=>true))
function togglePage(){checked.value=checked.value.length===selectable.value.length?[]:selectable.value.map(row=>row.id)}
async function bulkSaved(){bulkBusy.value=false;await load(result.value.page)}
onMounted(()=>load())
</script>
<template>
  <section class="space-y-3" :aria-busy="loading||saving">
    <div class="flex flex-wrap items-center gap-2">
      <div><h2 class="text-xl font-black">{{locale.t('adminCustomers')}}</h2></div>
      <button class="mini-action ms-auto" :disabled="bulkBusy||loading||saving" @click="edit(null)"><Plus :size="16"/>{{locale.t('addCustomer')}}</button>
    </div>
    <form class="admin-surface flex flex-wrap gap-2 rounded-2xl p-3" @submit.prevent="load(1)">
      <label class="form-field flex-1">{{locale.t('searchLabel')}}<input v-model="search" maxlength="100" :disabled="bulkBusy||loading||saving"></label>
      <button class="mini-action mt-auto" :disabled="bulkBusy||loading||saving"><RefreshCw :size="16"/>{{locale.t('adminReload')}}</button>
    </form>
    <p v-if="loading" role="status">{{locale.t('adminLoading')}}</p>
    <p v-if="error" role="alert" class="auth-error">{{error}}</p>
    <p v-if="note" role="status" class="text-sm text-[var(--c-secondary)]">{{note}}</p>
    <BulkStatusBar resource="customers" :items="bulkRows" :busy="loading||saving||bulkBusy" @busy="bulkBusy=$event" @clear="checked=[]" @saved="bulkSaved"/>
    <div class="data-table-shell">
      <table class="data-table">
        <thead><tr><th><input type="checkbox" :aria-label="locale.t('bulkSelectPage')" :checked="!!selectable.length&&checked.length===selectable.length" :indeterminate="checked.length>0&&checked.length<selectable.length" :disabled="bulkBusy||loading||saving||bulkBusy||!selectable.length" @change="togglePage"></th><th>{{locale.t('customerLabel')}}</th><th>{{locale.t('customerPriority')}}</th><th>WhatsApp</th><th>{{locale.t('email')}}</th><th>{{locale.t('statusLabel')}}</th><th>{{locale.t('actions')}}</th></tr></thead>
        <tbody><tr v-for="row in result.customers" :key="row.id"><td><input v-model="checked" type="checkbox" :value="row.id" :aria-label="String(row.id)" :disabled="bulkBusy||loading||saving||bulkBusy"></td>
          <td><button class="text-start font-bold" :disabled="bulkBusy||loading||saving" @click="edit(row)">{{row.company_name||row.name||'#'+row.id}}</button><RecordTags :tags="row.tags"/><small v-if="row.country_name" class="block">{{row.country_name}}</small></td>
          <td>{{row.priority}}</td><td dir="ltr">{{row.whatsapp||'—'}}</td><td dir="ltr">{{row.email||'—'}}</td>
          <td>{{locale.t(row.active?'active':'adminInactive')}}</td><td><button class="mini-action" :disabled="bulkBusy||loading||saving" @click="edit(row)">{{locale.t('manageCustomer')}}</button><button v-if="row.has_account===false&&row.active" class="mini-action ms-2" :disabled="bulkBusy||loading||saving||accountBusy" @click="accountCustomer=row">{{locale.t('customerCreateAccount')}}</button></td>
        </tr></tbody>
      </table>
      <p v-if="!loading&&!result.customers.length&&!error" class="p-4 text-sm">{{locale.t('adminNoCustomers')}}</p>
    </div>
    <div class="flex items-center justify-between gap-3">
      <button class="mini-action" :disabled="bulkBusy||loading||saving||result.page<=1" @click="load(result.page-1)">{{locale.t('back')}}</button>
      <span>{{result.page}} / {{result.last_page}} · {{result.total}}</span>
      <button class="mini-action" :disabled="bulkBusy||loading||saving||result.page>=result.last_page" @click="load(result.page+1)">{{locale.t('adminNext')}}</button>
    </div>
    <CustomerAccountPanel :customer="accountCustomer" @close="accountCustomer=null" @busy="accountBusy=$event" @saved="accountSaved"/>
    <AdaptivePanel :open="open" :title="locale.t('customer360')" wide @close="close">
      <form v-if="draft" class="space-y-5" @submit.prevent="save">
        <section class="customer-profile-head">
          <div class="profile-photo">🌐</div>
          <div class="min-w-0 flex-1"><b>{{draft.company_name||locale.t('addCustomer')}}</b><span v-if="selected" class="block text-xs">#{{selected.id}}</span></div>
        </section>
        <section class="admin-surface grid gap-3 rounded-2xl p-4 md:grid-cols-2">
          <p class="text-xs md:col-span-2">{{locale.t('adminCustomerAccountHelp')}}</p>
          <label class="form-field">{{locale.t('adminCompany')}}<input v-model="draft.company_name" required maxlength="255" :disabled="bulkBusy||saving"></label>
          <label class="form-field">WhatsApp<input v-model="draft.whatsapp" dir="ltr" maxlength="64" :disabled="bulkBusy||saving"></label>
          <label class="form-field">{{locale.t('country')}}<input v-model="draft.country_name" maxlength="255" :disabled="bulkBusy||saving"></label>
          <label class="form-field">{{locale.t('adminCountryCode')}}<input v-model="draft.country_code" dir="ltr" maxlength="2" pattern="[A-Za-z]{2}" :disabled="bulkBusy||saving"></label>
          <label class="form-field">{{locale.t('customerPriority')}}<input v-model.number="draft.priority" type="number" min="0" max="255" required :disabled="bulkBusy||saving"></label>
          <div v-if="selected" class="text-sm"><b>{{selected.name}}</b><p dir="ltr">{{selected.email}}</p></div>
          <label class="flex items-center gap-2"><input v-model="draft.active" type="checkbox" :disabled="bulkBusy||saving">{{locale.t('active')}}</label>
          <label class="flex items-center gap-2"><input v-model="draft.direct_link_enabled" type="checkbox" :disabled="bulkBusy||saving">{{locale.t('directAccess')}}</label>
          <label class="form-field md:col-span-2">{{locale.t('notes')}}<textarea v-model="draft.notes" rows="5" maxlength="10000" :disabled="bulkBusy||saving"></textarea></label>
          <p class="text-xs md:col-span-2">{{locale.t('adminArchiveHelp')}}</p>
        </section>
        <p v-if="error" role="alert" class="auth-error">{{error}}</p>
        <div class="sticky bottom-0 z-10 flex justify-end gap-2 border-t border-[var(--c-border)] bg-[var(--c-surface)] py-3">
          <button type="button" class="mini-action" :disabled="bulkBusy||saving" @click="close">{{locale.t('cancel')}}</button>
          <button class="mini-action" :disabled="bulkBusy||saving||loading||!draft.company_name?.trim()"><Save :size="17"/>{{locale.t(saving?'adminLoading':'save')}}</button>
        </div>
      </form>
    </AdaptivePanel>
  </section>
</template>
