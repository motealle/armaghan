<script setup lang="ts">
import { computed, ref } from 'vue'
import BaseModal from '@/components/ui/BaseModal.vue'
import { useLocaleStore } from '@/stores/locale'
import { CustomerSessionApiError } from '@/features/auth/services/customerSessionApi'
import { bulkAdminStatus, bulkAdminTags } from '../services/adminApi'
const props=defineProps<{resource:'products'|'customers'|'users';items:{id:number;revision:string}[];busy:boolean}>()
const emit=defineEmits<{saved:[];busy:[value:boolean];clear:[]}>()
const locale=useLocaleStore(),action=ref<boolean|null>(null),saving=ref(false),error=ref('')
const tagOpen=ref(false),tagMode=ref<'add'|'remove'|'replace'>('add'),tagText=ref('')
const tagWords=computed(()=>({fa:{title:'برچسب‌ها',add:'افزودن برچسب',remove:'برداشتن برچسب',replace:'جایگزینی برچسب‌ها',help:'برچسب‌ها را با ویرگول یا خط جدید جدا کنید؛ حداکثر ۱۰ برچسب و هرکدام ۴۰ حرف. از حروف، عدد، فاصله و خط تیره استفاده کنید.',warning:'جایگزینی، برچسب‌های قبلی تمام موارد انتخاب‌شده را تغییر می‌دهد؛ متن خالی همه برچسب‌ها را پاک می‌کند.'},en:{title:'Tags',add:'Add tags',remove:'Remove tags',replace:'Replace tags',help:'Separate tags with commas or new lines. Up to 10 tags, 40 characters each; letters, numbers, spaces and hyphens.',warning:'Replace changes existing tags on all selected records. Empty text clears all tags.'},ar:{title:'الوسوم',add:'إضافة وسوم',remove:'إزالة وسوم',replace:'استبدال الوسوم',help:'افصل الوسوم بفاصلة أو سطر جديد؛ حتى 10 وسوم، 40 حرفاً لكل وسم. استخدم الحروف والأرقام والمسافات والشرطة.',warning:'الاستبدال يغيّر الوسوم السابقة لجميع العناصر المحددة. النص الفارغ يمسح جميع الوسوم.'},ku:{title:'تاگەکان',add:'زیادکردنی تاگ',remove:'لابردنی تاگ',replace:'جێگرتنەوەی تاگەکان',help:'تاگەکان بە کۆما یان هێڵی نوێ جیا بکەوە؛ تا 10 تاگ و 40 پیت بۆ هەر تاگێک.',warning:'جێگرتنەوە تاگەکانی پێشووی هەموو هەڵبژێردراوەکان دەگۆڕێت. دەقی بەتاڵ هەموو تاگەکان پاک دەکاتەوە.'}}[locale.locale]))
const parsedTags=computed(()=>tagText.value.split(/[,،\n]/).map(t=>t.trim()).filter(Boolean))
async function confirmTags(){
 if(saving.value||props.busy||!props.items.length||parsedTags.value.length>10||(!parsedTags.value.length&&tagMode.value!=='replace'))return
 saving.value=true;emit('busy',true);error.value=''
 try{await bulkAdminTags(props.resource,props.items.map(({id,revision})=>({id,revision})),tagMode.value,parsedTags.value);tagOpen.value=false;tagText.value='';emit('clear');emit('saved')}
 catch(e){error.value=locale.t(e instanceof CustomerSessionApiError&&e.status===409?'adminConflict':e instanceof CustomerSessionApiError&&[401,403].includes(e.status)?'adminPermissionDenied':'adminRequestFailed')}
 finally{saving.value=false;emit('busy',false)}
}
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
    <button class="mini-action" :disabled="busy||saving" @click="tagOpen=true;error=''">{{tagWords.title}}</button>
    <button class="mini-action ms-auto" :disabled="busy||saving" @click="emit('clear')">{{words.clear}}</button>
  </div>
  <BaseModal :open="tagOpen" :title="tagWords.title" @close="!saving&&(tagOpen=false)">
    <form class="space-y-4" @submit.prevent="confirmTags">
      <p>{{items.length}} {{words.selected}}</p>
      <label class="form-field"><select v-model="tagMode" :disabled="saving"><option value="add">{{tagWords.add}}</option><option value="remove">{{tagWords.remove}}</option><option value="replace">{{tagWords.replace}}</option></select></label>
      <label class="form-field">{{tagWords.title}}<textarea v-model="tagText" rows="3" maxlength="500" :disabled="saving"/></label>
      <p class="text-xs">{{tagWords.help}}</p><p v-if="tagMode==='replace'" class="text-sm font-bold">{{tagWords.warning}}</p>
      <p v-if="error" class="auth-error" role="alert">{{error}}</p>
      <div class="flex justify-end gap-2"><button type="button" class="mini-action" :disabled="saving" @click="tagOpen=false">{{locale.t('cancel')}}</button><button class="mini-action" :disabled="saving||busy||parsedTags.length>10||(!parsedTags.length&&tagMode!=='replace')">{{saving?locale.t('adminLoading'):words.done}}</button></div>
    </form>
  </BaseModal>
  <BaseModal :open="action!==null" :title="words.confirm" @close="close">
    <p>{{words.warning}}</p><p class="my-4 font-bold">{{action?words.on:words.off}} · {{items.length}} {{words.selected}}</p>
    <p v-if="error" class="auth-error" role="alert">{{error}}</p>
    <div class="mt-4 flex justify-end gap-2"><button class="mini-action" :disabled="saving" @click="close">{{locale.t('cancel')}}</button><button class="mini-action" :disabled="saving||busy" @click="confirm">{{saving?locale.t('adminLoading'):words.done}}</button></div>
  </BaseModal>
</template>
