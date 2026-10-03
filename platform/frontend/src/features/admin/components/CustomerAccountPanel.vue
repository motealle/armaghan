<script setup lang="ts">
import {ref,watch} from 'vue'
import AdaptivePanel from '@/components/ui/AdaptivePanel.vue'
import {useLocaleStore} from '@/stores/locale'
import {useAdminStore} from '../store'
import {CustomerSessionApiError} from '@/features/auth/services/customerSessionApi'
import {createAdminUser,type AdminCustomer} from '../services/adminApi'
const props=defineProps<{customer:AdminCustomer|null}>()
const emit=defineEmits<{close:[];saved:[];busy:[value:boolean]}>()
const locale=useLocaleStore(),admin=useAdminStore()
const name=ref(''),email=ref(''),password=ref(''),confirmation=ref(''),acknowledged=ref(false),busy=ref(false),error=ref('')
watch(()=>props.customer,()=>{
  name.value=props.customer?.company_name||'';email.value='';password.value='';confirmation.value='';acknowledged.value=false;error.value=''
},{immediate:true})
function close(){if(!busy.value)emit('close')}
async function save(){
  if(!props.customer||props.customer.has_account||!acknowledged.value||busy.value)return
  if(password.value!==confirmation.value){error.value=locale.t('customerAccountInvalid');return}
  busy.value=true;emit('busy',true);error.value=''
  try{
    await createAdminUser({name:name.value.trim(),email:email.value.trim(),role:'customer',active:true,password:password.value,password_confirmation:confirmation.value,customer_id:props.customer.id,customer_revision:props.customer.revision})
    password.value='';confirmation.value='';emit('saved')
  }catch(e){
    const status=e instanceof CustomerSessionApiError?e.status:0
    error.value=locale.t(status===409?'adminConflict':status===422?'customerAccountInvalid':status===401||status===403?'adminPermissionDenied':'adminRequestFailed')
    if(status===401){admin.clear();password.value='';confirmation.value='';emit('close')}
  }finally{busy.value=false;emit('busy',false)}
}
</script>
<template>
  <AdaptivePanel :open="!!customer" :title="locale.t('customerCreateAccount')" @close="close">
    <form v-if="customer" class="space-y-4" :aria-busy="busy" @submit.prevent="save">
      <p class="font-bold">{{customer.company_name}} · #{{customer.id}}</p>
      <p class="text-sm text-[var(--c-muted)]">{{locale.t('customerAccountHelp')}}</p>
      <label class="form-field">{{locale.t('fullName')}}<input v-model="name" required maxlength="255" autocomplete="off" :disabled="busy"></label>
      <label class="form-field">{{locale.t('email')}}<input v-model="email" type="email" required maxlength="255" dir="ltr" autocomplete="off" :disabled="busy"></label>
      <label class="form-field">{{locale.t('password')}}<input v-model="password" type="password" required minlength="12" maxlength="255" autocomplete="new-password" :disabled="busy"></label>
      <label class="form-field">{{locale.t('confirmPassword')}}<input v-model="confirmation" type="password" required minlength="12" maxlength="255" autocomplete="new-password" :disabled="busy"></label>
      <p class="text-xs">{{locale.t('adminPasswordRules')}}</p>
      <label class="flex items-start gap-2 text-sm"><input v-model="acknowledged" type="checkbox" required :disabled="busy">{{locale.t('customerAccountConfirm')}}</label>
      <p v-if="error" role="alert" class="auth-error">{{error}}</p>
      <div class="flex justify-end gap-2"><button type="button" class="mini-action" :disabled="busy" @click="close">{{locale.t('cancel')}}</button><button class="mini-action" :disabled="busy||!acknowledged">{{locale.t(busy?'adminLoading':'customerCreateAccount')}}</button></div>
    </form>
  </AdaptivePanel>
</template>
