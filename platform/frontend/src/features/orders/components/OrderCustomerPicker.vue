<script setup lang="ts">
import {computed,onMounted,onBeforeUnmount,ref,useId} from 'vue'
import {useLocaleStore} from '@/stores/locale'
import {fetchAdminCustomers,type AdminCustomer,type CustomerPage} from '@/features/admin/services/adminApi'
import {inputDigits} from '@/features/auth/services/simpleLogin'
import {trackingCopy} from '../services/trackingCopy'
const props=defineProps<{modelValue:AdminCustomer|null;disabled?:boolean}>()
const emit=defineEmits<{'update:modelValue':[value:AdminCustomer|null]}>()
const searchId=useId()
const locale=useLocaleStore(),copy=computed(()=>trackingCopy[locale.locale])
const query=ref(''),busy=ref(false),error=ref(''),result=ref<CustomerPage>({customers:[],page:1,last_page:1,total:0})
let request=0
const choices=computed(()=>{
 const rows=result.value.customers.filter(row=>row.active)
 return props.modelValue&&!rows.some(row=>row.id===props.modelValue!.id)?[props.modelValue,...rows]:rows
})
function label(row:AdminCustomer){return [row.company_name||row.contact_name||row.name,row.phone||row.whatsapp||row.email,'#'+row.id].filter(Boolean).join(' · ')}
function select(event:Event){emit('update:modelValue',choices.value.find(row=>row.id===Number((event.target as HTMLSelectElement).value))??null)}
async function load(page=1){
 const id=++request;busy.value=true;error.value=''
 const raw=inputDigits(query.value.trim())
 const search=/^[+\d\s().-]+$/.test(raw)?raw.replace(/\D/g,'').replace(/^00/,'').replace(/^(964|98|90|971)0(?=\d)/,'$1').replace(/^0+/,''):raw
 try{const response=await fetchAdminCustomers(page,search);if(id===request)result.value=response}
 catch{if(id===request){result.value={customers:[],page:1,last_page:1,total:0};error.value=locale.t('adminRequestFailed')}}
 finally{if(id===request)busy.value=false}
}
onMounted(()=>load())
onBeforeUnmount(()=>{request++})
</script>
<template>
 <div class="space-y-2" :aria-busy="busy">
  <label class="form-field" :for="searchId">{{copy.search}}</label><div class="flex flex-wrap gap-2"><input :id="searchId" v-model="query" class="min-w-0 flex-1" maxlength="100" :disabled="disabled" @keydown.enter.prevent="load()"><button type="button" class="mini-action" :disabled="disabled||busy" @click="load()">{{locale.t('searchLabel')}}</button></div>
  <p v-if="error" class="auth-error" role="alert">{{error}}</p>
  <p v-if="busy" class="text-xs" role="status">{{locale.t('adminLoading')}}</p>
  <label class="form-field">{{copy.choose}}<select :value="modelValue?.id??''" required :disabled="disabled||busy" @change="select"><option value="">—</option><option v-for="customer in choices" :key="customer.id" :value="customer.id">{{label(customer)}}</option></select></label>
  <p v-if="!busy&&!error&&!result.customers.some(row=>row.active)" class="text-sm">{{copy.noCustomers}}</p>
  <div v-if="result.last_page>1" class="flex items-center justify-between gap-2 text-xs"><button type="button" class="mini-action" :disabled="disabled||busy||result.page<=1" @click="load(result.page-1)">{{locale.t('back')}}</button><span>{{result.page}} / {{result.last_page}}</span><button type="button" class="mini-action" :disabled="disabled||busy||result.page>=result.last_page" @click="load(result.page+1)">{{locale.t('adminNext')}}</button></div>
  <p v-if="modelValue" class="rounded-xl bg-[var(--c-surface-2)] p-3 text-sm"><b>{{copy.selected}}:</b> <bdi>{{label(modelValue)}}</bdi></p>
  <p v-if="modelValue?.has_account===false" class="text-sm leading-7">{{copy.noAccount}}</p>
 </div>
</template>
