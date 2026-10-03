<script setup lang="ts">
import BulkStatusBar from './BulkStatusBar.vue'
import {computed,onMounted,ref} from 'vue'
import {Plus,Save} from '@lucide/vue'
import AdaptivePanel from '@/components/ui/AdaptivePanel.vue'
import {useLocaleStore} from '@/stores/locale'
import {useAdminStore} from '../store'
import {CustomerSessionApiError} from '@/features/auth/services/customerSessionApi'
import {fetchAdminUsers,createAdminUser,updateAdminUser,type AdminUser,type UserPage} from '../services/adminApi'
const locale=useLocaleStore(),admin=useAdminStore()
const page=ref<UserPage>({users:[],page:1,last_page:1,total:0}),search=ref(''),busy=ref(false),error=ref(''),note=ref(''),open=ref(false)
const selected=ref<AdminUser|null>(null)
const draft=ref({name:'',email:'',role:'customer',active:true,password:'',password_confirmation:''})
function fail(e:unknown){
  if(e instanceof CustomerSessionApiError&&e.status===401){admin.clear();open.value=false;page.value={users:[],page:1,last_page:1,total:0}}
  error.value=locale.t(e instanceof CustomerSessionApiError&&e.status===409?'adminConflict':e instanceof CustomerSessionApiError&&e.status===403?'adminPermissionDenied':'adminRequestFailed')
}
async function load(number=1){if(busy.value||bulkBusy.value)return;checked.value=[];busy.value=true;error.value='';try{page.value=await fetchAdminUsers(number,search.value.trim())}catch(e){page.value={users:[],page:1,last_page:1,total:0};fail(e)}finally{busy.value=false}}
function edit(row:AdminUser|null){selected.value=row;error.value='';note.value='';draft.value={name:row?.name||'',email:row?.email||'',role:row?.role||'customer',active:row?.active??true,password:'',password_confirmation:''};open.value=true}
function close(){if(busy.value)return;open.value=false;draft.value.password='';draft.value.password_confirmation=''}
async function save(){
  if(busy.value)return;busy.value=true;error.value='';note.value=''
  try{
    if(selected.value)await updateAdminUser(selected.value,{name:draft.value.name,role:selected.value.role,active:draft.value.active})
    else await createAdminUser({...draft.value,email:draft.value.email.trim().toLowerCase()})
    // Never retain credentials after submission or show success before server confirmation.
    open.value=false;note.value=locale.t('adminSaved');page.value=await fetchAdminUsers(page.value.page,search.value.trim())
  }catch(e){fail(e)}finally{draft.value.password='';draft.value.password_confirmation='';busy.value=false}
}
const bulkBusy=ref(false),checked=ref<number[]>([])
const bulkRows=computed(()=>page.value.users.filter(row=>checked.value.includes(row.id)))
const selectable=computed(()=>page.value.users.filter(row=>!row.protected&&row.email!==admin.identity?.email))
function togglePage(){checked.value=checked.value.length===selectable.value.length?[]:selectable.value.map(row=>row.id)}
async function bulkSaved(){bulkBusy.value=false;await load(page.value.page)}
onMounted(()=>load())
</script>
<template>
<section class="space-y-3" :aria-busy="busy">
  <div class="flex flex-wrap items-center gap-2"><h2 class="text-xl font-black">{{locale.t('adminAccounts')}}</h2><button class="mini-action ms-auto" :disabled="bulkBusy||busy" @click="edit(null)"><Plus :size="16"/>{{locale.t('adminAddAccount')}}</button></div>
  <p class="text-sm text-[var(--c-muted)]">{{locale.t(admin.identity?.is_owner?'adminOwnerHelp':'adminBusinessHelp')}}</p>
  <a href="/backend/account/security" class="mini-action">{{locale.t('adminOwnPassword')}}</a>
  <form class="admin-surface flex gap-2 rounded-2xl p-3" @submit.prevent="load()"><label class="form-field flex-1">{{locale.t('searchLabel')}}<input v-model="search" maxlength="100" :disabled="bulkBusy||busy"></label><button class="mini-action" :disabled="bulkBusy||busy">{{locale.t('adminReload')}}</button></form>
  <p v-if="error" role="alert" class="auth-error">{{error}}</p><p v-if="note" role="status">{{note}}</p><p v-if="busy" role="status">{{locale.t('adminLoading')}}</p>
  <BulkStatusBar resource="users" :items="bulkRows" :busy="busy||bulkBusy" @busy="bulkBusy=$event" @clear="checked=[]" @saved="bulkSaved"/>
    <div class="data-table-shell"><table class="data-table"><thead><tr><th><input type="checkbox" :aria-label="locale.t('bulkSelectPage')" :checked="!!selectable.length&&checked.length===selectable.length" :indeterminate="checked.length>0&&checked.length<selectable.length" :disabled="bulkBusy||busy||bulkBusy||!selectable.length" @change="togglePage"></th><th>{{locale.t('fullName')}}</th><th>{{locale.t('email')}}</th><th>{{locale.t('adminAccountRole')}}</th><th>{{locale.t('statusLabel')}}</th><th>{{locale.t('actions')}}</th></tr></thead><tbody>
  <tr v-for="row in page.users" :key="row.id"><td><input v-model="checked" type="checkbox" :value="row.id" :aria-label="String(row.id)" :disabled="bulkBusy||busy||bulkBusy||row.protected||row.email===admin.identity?.email"></td><td>{{row.name}}</td><td dir="ltr">{{row.email}}</td><td>{{locale.t(row.is_owner?'adminOwnerRole':row.role==='admin'?'adminAdminRole':'customerLabel')}}</td><td>{{locale.t(row.active?'active':'adminInactive')}}</td><td><button class="mini-action" :disabled="bulkBusy||busy||(row.protected?!(admin.identity?.is_owner&&row.email===admin.identity.email):row.email===admin.identity?.email)" @click="edit(row)">{{locale.t('adminEditAccount')}}</button></td></tr>
  </tbody></table><p v-if="!busy&&!page.users.length&&!error" class="p-4">{{locale.t('adminNoAccounts')}}</p></div>
  <div class="flex justify-between gap-2"><button class="mini-action" :disabled="bulkBusy||busy||page.page<=1" @click="load(page.page-1)">{{locale.t('back')}}</button><span>{{page.page}} / {{page.last_page}} · {{page.total}}</span><button class="mini-action" :disabled="bulkBusy||busy||page.page>=page.last_page" @click="load(page.page+1)">{{locale.t('adminNext')}}</button></div>
  <AdaptivePanel :open="open" :title="locale.t('adminAccounts')" wide @close="close"><form class="space-y-4" @submit.prevent="save">
    <p class="text-sm">{{locale.t('adminAccountSafetyHelp')}}</p>
    <section class="admin-surface grid gap-3 rounded-2xl p-4 md:grid-cols-2">
    <label class="form-field">{{locale.t('fullName')}}<input v-model="draft.name" required maxlength="255" :disabled="bulkBusy||busy"></label>
    <label class="form-field">{{locale.t('email')}}<input v-model="draft.email" type="email" required maxlength="255" dir="ltr" :disabled="bulkBusy||busy||!!selected"></label>
    <label v-if="!selected&&admin.identity?.is_owner" class="form-field">{{locale.t('adminAccountRole')}}<select v-model="draft.role" :disabled="bulkBusy||busy"><option value="customer">{{locale.t('customerLabel')}}</option><option value="admin">{{locale.t('adminAdminRole')}}</option></select></label>
    <label class="flex items-center gap-2"><input v-model="draft.active" type="checkbox" :disabled="bulkBusy||busy||selected?.protected">{{locale.t('active')}}</label>
    <template v-if="!selected"><label class="form-field">{{locale.t('password')}}<input v-model="draft.password" type="password" autocomplete="new-password" required minlength="12" maxlength="255" :disabled="bulkBusy||busy"></label><label class="form-field">{{locale.t('confirmPassword')}}<input v-model="draft.password_confirmation" type="password" autocomplete="new-password" required minlength="12" maxlength="255" :disabled="bulkBusy||busy"></label><p class="text-xs md:col-span-2">{{locale.t('adminPasswordRules')}}</p></template>
    </section>
    <p v-if="error" role="alert" class="auth-error">{{error}}</p>
    <div class="flex justify-end gap-2"><button class="mini-action" type="button" :disabled="bulkBusy||busy" @click="close">{{locale.t('cancel')}}</button><button class="mini-action" :disabled="bulkBusy||busy"><Save :size="16"/>{{locale.t('save')}}</button></div>
  </form></AdaptivePanel>
</section>
</template>
