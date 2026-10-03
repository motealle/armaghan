<script setup lang="ts">
import { computed, ref } from 'vue'
import BaseModal from '@/components/ui/BaseModal.vue'
import { useLocaleStore } from '@/stores/locale'
import { CustomerSessionApiError } from '@/features/auth/services/customerSessionApi'
import { bulkAdminStatus } from '../services/adminApi'
const props=defineProps<{resource:'products'|'customers'|'users';items:{id:number;revision:string}[];busy:boolean}>()
const emit=defineEmits<{saved:[];busy:[value:boolean];clear:[]}>()
const locale=useLocaleStore(),action=ref<boolean|null>(null),saving=ref(false),error=ref('')
const words=computed(()=>({fa:{selected:'مورد انتخاب شده',on:'فعال‌سازی',off:'غیرفعال‌سازی',clear:'پاک کردن انتخاب',confirm:'تأیید عملیات دسته‌ای',warning:'این تغییر برای همه موارد انتخاب‌شده اعمال می‌شود.',done:'اعمال تغییرات'},en:{selected:'selected',on:'Activate',off:'Deactivate',clear:'Clear selection',confirm:'Confirm bulk action',warning:'This change will apply to every selected item.',done:'Apply changes'},ar:{selected:'عناصر محددة',on:'تفعيل',off:'تعطيل',clear:'إلغاء التحديد',confirm:'تأكيد العملية الجماعية',warning:'سيتم تطبيق هذا التغيير على جميع العناصر المحددة.',done:'تطبيق التغييرات'},ku:{selected:'هەڵبژێردراو',on:'چالاککردن',off:'ناچالاککردن',clear:'سڕینەوەی هەڵبژاردن',confirm:'پشتڕاستکردنەوەی گۆڕانکاری',warning:'ئەم گۆڕانکارییە بۆ هەموو هەڵبژێردراوەکان جێبەجێ دەکرێت.',done:'جێبەجێکردن'}}[locale.locale]))
function close(){if(!saving.value){action.value=null;error.value=''}}
async function confirm(){
  if(action.value===null||saving.value||props.busy||!props.items.length)return
  saving.value=true;emit('busy',true);error.value=''
  try{await bulkAdminStatus(props.resource,props.items.map(({id,revision})=>({id,revision})),action.value);action.value=null;emit('clear');emit('saved')}
  catch(e){error.value=locale.t(e instanceof CustomerSessionApiError&&e.status===409?'adminConflict':e instanceof CustomerSessionApiError&&[401,403].includes(e.status)?'adminPermissionDenied':'adminRequestFailed')}
  finally{saving.value=false;emit('busy',false)}
}
</script>
<template>
  <div v-if="items.length" class="admin-surface flex flex-wrap items-center gap-2 rounded-2xl p-3" role="region" :aria-label="words.confirm">
    <b class="text-sm">{{items.length}} {{words.selected}}</b>
    <button class="mini-action" :disabled="busy||saving" @click="action=true">{{words.on}}</button>
    <button class="mini-action" :disabled="busy||saving" @click="action=false">{{words.off}}</button>
    <button class="mini-action ms-auto" :disabled="busy||saving" @click="emit('clear')">{{words.clear}}</button>
  </div>
  <BaseModal :open="action!==null" :title="words.confirm" @close="close">
    <p>{{words.warning}}</p><p class="my-4 font-bold">{{action?words.on:words.off}} · {{items.length}} {{words.selected}}</p>
    <p v-if="error" class="auth-error" role="alert">{{error}}</p>
    <div class="mt-4 flex justify-end gap-2"><button class="mini-action" :disabled="saving" @click="close">{{locale.t('cancel')}}</button><button class="mini-action" :disabled="saving||busy" @click="confirm">{{saving?locale.t('adminLoading'):words.done}}</button></div>
  </BaseModal>
</template>
