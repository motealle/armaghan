<script setup lang="ts">
import {ref} from 'vue'
import {requestJson} from '@/features/auth/services/customerSessionApi'
import {useAdminStore} from '../store'
import {useLocaleStore} from '@/stores/locale'
const admin=useAdminStore(),locale=useLocaleStore()
const reason=ref(''),busy=ref(false),failed=ref(false)
async function enter(){if(busy.value||!reason.value)return;busy.value=true;failed.value=false;try{const result=await requestJson<{redirect:string}>('/api/admin/advanced-access',{method:'POST',body:JSON.stringify({reason:reason.value})});if(result.redirect!=='/backend/admin')throw Error('Invalid destination');window.location.assign(result.redirect)}catch{failed.value=true;busy.value=false}}
</script>
<template>
<details v-if="admin.identity?.is_owner" class="admin-surface rounded-2xl p-3">
  <summary>{{locale.t('advancedAdminTitle')}}</summary>
  <p class="my-3 text-sm">{{locale.t('advancedAdminHelp')}}</p>
  <form class="flex flex-wrap gap-3" @submit.prevent="enter">
    <label class="form-field">{{locale.t('advancedAdminReason')}}<select v-model="reason" required :disabled="busy"><option disabled value="">{{locale.t('advancedChooseReason')}}</option><option value="integration-gap">{{locale.t('advancedGap')}}</option><option value="diagnosis">{{locale.t('advancedDiagnosis')}}</option><option value="recovery">{{locale.t('advancedRecovery')}}</option></select></label>
    <button class="mini-action" :disabled="busy||!reason">{{locale.t('advancedAdminEnter')}}</button>
  </form>
  <p v-if="failed" role="alert" class="auth-error">{{locale.t('adminRequestFailed')}}</p>
</details>
</template>
