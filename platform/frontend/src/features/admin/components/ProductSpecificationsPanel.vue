<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import AdaptivePanel from '@/components/ui/AdaptivePanel.vue'
import { useLocaleStore } from '@/stores/locale'
import { useAdminStore } from '../store'
import { CustomerSessionApiError } from '@/features/auth/services/customerSessionApi'
import { saveProductSpecificationSchema, type AdminSubcategory, type AdminSpecification } from '../services/adminApi'
const props=defineProps<{open:boolean;taxonomy:AdminSubcategory[]}>()
const emit=defineEmits<{close:[];saved:[]}>()
const locale=useLocaleStore(),admin=useAdminStore()
type Definition={id:number|null;key:string;labels:AdminSpecification['labels'];locked:boolean}
const groupId=ref(0),definitions=ref<Definition[]>([]),acknowledged=ref(false),saving=ref(false),error=ref('')
const group=computed(()=>props.taxonomy.find(s=>s.id===groupId.value))
function load(){definitions.value=(group.value?.specifications??[]).map(s=>({id:s.id,key:s.key,labels:{...s.labels},locked:s.locked}));acknowledged.value=false;error.value=''}
watch(()=>props.open,open=>{if(open){groupId.value=props.taxonomy[0]?.id??0;load()}},{immediate:true})
watch(groupId,load)
function move(index:number,offset:number){const target=index+offset;if(target<0||target>=definitions.value.length)return;const item=definitions.value.splice(index,1)[0]!;definitions.value.splice(target,0,item);acknowledged.value=false}
function add(){definitions.value.push({id:null,key:'',labels:{fa:'',ar:null,en:null,ku:null},locked:false});acknowledged.value=false}
async function save(){
  if(!group.value||saving.value||!acknowledged.value)return
  saving.value=true;error.value=''
  try{await saveProductSpecificationSchema(group.value,definitions.value);emit('saved')}
  catch(e){if(e instanceof CustomerSessionApiError&&[401,403].includes(e.status)){admin.clear();emit('close')}
    error.value=locale.t(e instanceof CustomerSessionApiError&&e.status===409?'adminProductConflict':'adminRequestFailed')}
  finally{saving.value=false}
}
</script>
<template>
  <AdaptivePanel :open="open" :title="locale.t('adminSpecSchema')" wide @close="!saving&&emit('close')">
    <form class="space-y-4" :aria-busy="saving" @submit.prevent="save">
      <label class="form-field">{{locale.t('subcategoryLabel')}}<select v-model.number="groupId" :disabled="saving"><option v-for="sub in taxonomy" :key="sub.id" :value="sub.id">{{locale.subcategoryName(sub.code,sub.name)}}</option></select></label>
      <p class="text-sm text-[var(--c-muted)]">{{locale.t('adminSpecSchemaHelp')}}</p>
      <article v-for="(definition,index) in definitions" :key="definition.id??'new-'+index" class="admin-surface space-y-3 rounded-2xl p-4">
        <div class="flex flex-wrap items-center gap-3"><b>{{index+1}}</b><button type="button" class="mini-action" :aria-label="locale.t('previous')" :disabled="saving||index===0" @click="move(index,-1)">↑</button><button type="button" class="mini-action" :aria-label="locale.t('next')" :disabled="saving||index===definitions.length-1" @click="move(index,1)">↓</button><label class="form-field grow">{{locale.t('adminSpecKey')}}<input v-model="definition.key" dir="ltr" pattern="[a-z][a-z0-9_]*" maxlength="64" required :disabled="saving||definition.id!==null"></label><label class="form-field">{{locale.t('statusLabel')}}<select v-model="definition.locked" :disabled="saving"><option :value="true">{{locale.t('locked')}}</option><option :value="false">{{locale.t('negotiable')}}</option></select></label></div>
        <div class="grid gap-3 md:grid-cols-2"><label v-for="lang in (['fa','ar','en','ku'] as const)" :key="lang" class="form-field">{{({fa:'فارسی',ar:'العربية',en:'English',ku:'کوردی'})[lang]}}<input v-model="definition.labels[lang]" :dir="lang==='en'?'ltr':'rtl'" :required="lang==='fa'" maxlength="255" :disabled="saving"></label></div>
        <button v-if="definition.id===null" type="button" class="mini-action" :disabled="saving" @click="definitions.splice(index,1)">{{locale.t('remove')}}</button>
      </article>
      <button type="button" class="mini-action" :disabled="saving||definitions.length>=100" @click="add">{{locale.t('adminSpecAdd')}}</button>
      <label class="flex items-start gap-2 rounded-xl border border-[var(--c-border)] p-3 text-sm"><input v-model="acknowledged" type="checkbox" :disabled="saving" required>{{locale.t('adminSpecAcknowledge')}}</label>
      <p v-if="error" class="auth-error" role="alert">{{error}}</p>
      <div class="flex justify-end gap-2"><button type="button" class="mini-action" :disabled="saving" @click="emit('close')">{{locale.t('close')}}</button><button class="mini-action" :disabled="saving||!acknowledged||!group">{{locale.t(saving?'adminLoading':'save')}}</button></div>
    </form>
  </AdaptivePanel>
</template>
