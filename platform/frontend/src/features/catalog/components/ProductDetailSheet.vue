<script setup lang="ts">
import { computed, ref, watch, nextTick } from 'vue'
import { LockKeyhole, SlidersHorizontal, Pencil, Globe, Save, RefreshCw } from '@lucide/vue'
import type { Product } from '@/types/domain'
import type { Locale } from '@/services/localeDetection'
import BaseSheet from '@/components/ui/BaseSheet.vue'
import { useLocaleStore } from '@/stores/locale'
import { useAdminStore } from '@/features/admin/store'
import { CustomerSessionApiError } from '@/features/auth/services/customerSessionApi'
import { fetchAdminProduct, fetchProductTaxonomy, saveProductSpecificationSchema, updateProductSpecifications, type AdminProduct } from '@/features/admin/services/adminApi'
import { customerProductLabel } from '../presentation'

const props=withDefaults(defineProps<{open:boolean;product:Product|null;adminEditable?:boolean}>(),{adminEditable:false})
const emit=defineEmits<{close:[];saved:[product:AdminProduct]}>()
const locale=useLocaleStore(),admin=useAdminStore()
const languages={fa:'فارسی',ar:'العربية',en:'English',ku:'کوردی'} as const
const language=ref<Locale>(locale.locale)
const editing=ref(false),busy=ref(false),error=ref(''),note=ref(''),conflict=ref(false)
const row=ref<AdminProduct|null>(null)
const draft=ref<Record<string,Record<Locale,string>>>({})
const editorBody=ref<HTMLElement|null>(null)
const canEdit=computed(()=>props.adminEditable&&Boolean(admin.identity)&&Boolean(props.product?.backendId))
const title=computed(()=>props.product?customerProductLabel(props.product,locale.subcategoryName(props.product.subcategoryCode,props.product.subcategoryName),locale.t('unavailableOrProducible')):locale.t('productSpecs'))
const availability=computed(()=>props.product?.availability==='available'?locale.t('available'):locale.t('madeToOrder'))
const specs=computed(()=>row.value?.specifications??props.product?.specificationValues??[])
function exactValue(spec:{value_text?:string|null;values?:Partial<Record<Locale,string|null>>},lang:Locale){return (lang==='fa'?spec.values?.fa??spec.value_text:spec.values?.[lang])??''}
function shownValue(spec:typeof specs.value[number]){return exactValue(spec,locale.locale)||exactValue(spec,'fa')}
const dirty=computed(()=>row.value?.specifications.some(spec=>(Object.keys(languages) as Locale[]).some(lang=>(draft.value[spec.key]?.[lang]??'')!==exactValue(spec,lang)))??false)
function reset(){editing.value=false;row.value=null;draft.value={};error.value='';note.value='';conflict.value=false;language.value=locale.locale}
watch([()=>props.open,()=>props.product?.id],()=>reset())
watch(canEdit,value=>{if(!value)reset()})
function close(){if(busy.value)return;if(dirty.value&&!window.confirm(locale.t('specDiscardChanges')))return;reset();emit('close')}
function cancel(){if(busy.value)return;if(dirty.value&&!window.confirm(locale.t('specDiscardChanges')))return;reset()}
function failed(e:unknown){
 if(e instanceof CustomerSessionApiError&&[401,403].includes(e.status)){admin.clear();reset();error.value=locale.t('adminSessionRequired');return}
 conflict.value=e instanceof CustomerSessionApiError&&e.status===409
 error.value=locale.t(conflict.value?'specConflict':'adminRequestFailed')
}
async function beginEdit(){
 if(!canEdit.value||busy.value||!props.product?.backendId)return
 if(dirty.value&&!window.confirm(locale.t('specDiscardChanges')))return
 if(!editing.value)language.value=locale.locale
 const id=props.product.backendId;busy.value=true;error.value='';note.value=''
 try{
  let product=(await fetchAdminProduct(id)).product
  if(!product.specifications.length){
   const group=(await fetchProductTaxonomy()).subcategories.find(item=>item.id===product.subcategory_id)
   if(!group)throw new Error('Product taxonomy unavailable')
   if(!group.specifications.length){
    const defaults=[...props.product.specs.locked.map(label=>({label,locked:true})),...props.product.specs.negotiable.map(label=>({label,locked:false}))]
    const definitions=defaults.map((item,index)=>({id:null,key:'catalog_spec_'+index,locked:item.locked,labels:Object.fromEntries((Object.keys(languages) as Locale[]).map(lang=>{const text=locale.t('spec.'+item.label,lang);return [lang,text&&text!=='spec.'+item.label?text:lang==='fa'?item.label:null]})) as AdminProduct['specifications'][number]['labels']}))
    if(definitions.length){
     try{await saveProductSpecificationSchema(group,definitions)}catch(e){
      // Another manager may have initialized the same empty category first.
      if(!(e instanceof CustomerSessionApiError)||e.status!==409)throw e
     }
    }
   }
   product=(await fetchAdminProduct(id)).product
  }
  if(!props.open||props.product?.backendId!==id||!canEdit.value)return
  row.value=product
  draft.value=Object.fromEntries(product.specifications.map(spec=>[spec.key,Object.fromEntries((Object.keys(languages) as Locale[]).map(lang=>[lang,exactValue(spec,lang)]))])) as typeof draft.value
  editing.value=true;conflict.value=false
  await nextTick();editorBody.value?.querySelector('textarea')?.focus({preventScroll:true})
 }catch(e){failed(e)}finally{busy.value=false}
}
async function save(){
 if(!canEdit.value||!row.value||busy.value||!dirty.value||conflict.value)return
 busy.value=true;error.value='';note.value=''
 try{
  // Every language uses the latest server revision. Successful writes become the
  // baseline immediately so retry never resubmits an already saved language.
  for(const lang of Object.keys(languages) as Locale[]){
   if(!canEdit.value)throw new CustomerSessionApiError(401,'Admin session required')
   const changes:{definition_id:number;value_text:string|null}[]=row.value.specifications.filter(spec=>(draft.value[spec.key]?.[lang]??'')!==exactValue(spec,lang)).map(spec=>({definition_id:spec.id,value_text:draft.value[spec.key]?.[lang]?.trim()||null}))
   if(!changes.length)continue
   const saved:AdminProduct=(await updateProductSpecifications(row.value,lang,changes)).product
   if(!props.open||props.product?.backendId!==saved.id||!canEdit.value)return
   row.value=saved
   for(const spec of row.value.specifications)if(draft.value[spec.key])draft.value[spec.key]![lang]=exactValue(spec,lang)
   emit('saved',row.value)
  }
  editing.value=false;note.value='adminSaved'
 }catch(e){failed(e)}finally{busy.value=false}
}
</script>

<template>
 <BaseSheet :open="open" :title="title" @close="close">
  <template v-if="product">
   <div v-if="canEdit" class="mb-4 flex items-center justify-end gap-2" data-spec-toolbar>
    <label v-if="editing" class="flex min-h-11 min-w-0 items-center gap-2 rounded-xl border border-[var(--c-border)] bg-[var(--c-surface-2)] px-3 text-xs">
     <Globe :size="17" aria-hidden="true"/><span>{{locale.t('specTextLanguage')}}</span>
     <select v-model="language" :disabled="busy" :aria-label="locale.t('specTextLanguage')" class="min-h-11 min-w-0 bg-transparent font-bold" :dir="language==='en'?'ltr':'rtl'">
      <option v-for="(label,key) in languages" :key="key" :value="key">{{label}}</option>
     </select>
    </label>
    <button type="button" class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-xl border border-[var(--c-border)] text-[var(--c-primary)]" :aria-label="locale.t('specEdit')" :title="locale.t('specEdit')" :aria-pressed="editing" :disabled="busy||editing" @click="beginEdit"><Pencil :size="18"/></button>
   </div>
   <div class="mb-4 flex flex-wrap gap-2 text-xs">
    <span class="rounded-full bg-[var(--c-surface-2)] px-2.5 py-1">{{locale.t('codeLabel')}} {{product.code}}</span>
    <span class="rounded-full bg-[color-mix(in_srgb,var(--c-secondary)_10%,var(--c-surface))] px-2.5 py-1 font-bold text-[var(--c-secondary)]">{{availability}}</span>
   </div>
   <p v-if="editing" class="mb-3 rounded-xl bg-[var(--c-surface-2)] p-3 text-xs leading-6">{{locale.t('specEditingHelp')}} <b>{{languages[language]}}</b></p>
   <p v-else-if="busy" role="status" class="mb-3 text-sm">{{locale.t('adminLoading')}}</p>
   <form ref="editorBody" :aria-busy="busy" @submit.prevent="save">
    <div v-if="specs.length" class="grid grid-cols-2 gap-2">
     <article v-for="spec in specs" :key="spec.key" class="spec-tile min-w-0" :class="spec.locked?'locked':'negotiable'">
      <div class="flex items-start gap-2"><LockKeyhole v-if="spec.locked" :size="16" class="shrink-0"/><SlidersHorizontal v-else :size="16" class="shrink-0"/><b class="text-xs">{{spec.labels[locale.locale]||spec.labels.fa}}</b></div>
      <label v-if="editing&&draft[spec.key]" class="mt-3 block">
       <span class="sr-only">{{spec.labels[locale.locale]||spec.labels.fa}} · {{languages[language]}}</span>
       <textarea v-model="draft[spec.key]![language]" :aria-label="(spec.labels[locale.locale]||spec.labels.fa)+' · '+languages[language]" :lang="language" :dir="language==='en'?'ltr':'rtl'" :disabled="busy||conflict" maxlength="1000" rows="3" :placeholder="locale.t('specValuePlaceholder')" class="w-full resize-y rounded-lg border border-[var(--c-border)] bg-[var(--c-surface)] p-2 text-base leading-6 text-[var(--c-text)] focus:outline-2 focus:outline-[var(--c-primary)]"/>
       <small v-if="language!=='fa'&&exactValue(spec,'fa')" class="mt-1 block break-words text-[10px]">{{locale.t('specPersianReference')}} {{exactValue(spec,'fa')}}</small>
      </label>
      <p v-else class="mt-2 whitespace-pre-wrap break-words text-sm text-[var(--c-text)]">{{shownValue(spec)||locale.t('specValueEmpty')}}</p>
      <small v-if="!editing&&locale.locale!=='fa'&&!exactValue(spec,locale.locale)&&exactValue(spec,'fa')" class="mt-1 block text-[10px]">{{locale.t('specPersianFallback')}}</small>
      <small class="mt-2 block text-[10px]">{{locale.t(spec.locked?'locked':'negotiable')}}</small>
     </article>
    </div>
    <div v-else class="grid grid-cols-2 gap-2">
     <article v-for="item in product.specs.locked" :key="'l-'+item" class="spec-tile locked"><div class="flex items-center gap-2"><LockKeyhole :size="17"/><b class="text-xs">{{locale.specLabel(item)}}</b></div><small class="mt-1 block text-[10px]">{{locale.t('locked')}}</small></article>
     <article v-for="item in product.specs.negotiable" :key="'n-'+item" class="spec-tile negotiable"><div class="flex items-center gap-2"><SlidersHorizontal :size="17"/><b class="text-xs">{{locale.specLabel(item)}}</b></div><small class="mt-1 block text-[10px]">{{locale.t('negotiable')}}</small></article>
    </div>
    <p v-if="editing&&!specs.length" class="my-3 text-sm">{{locale.t('adminSpecsEmpty')}}</p>
    <p v-if="error" role="alert" class="auth-error mt-3">{{error}}</p>
    <p v-if="note" role="status" class="mt-3 rounded-xl bg-[var(--c-surface-2)] p-3 text-sm">{{locale.t(note)}}</p>
    <div v-if="editing" class="sticky bottom-0 mt-4 flex flex-wrap justify-end gap-2 border-t border-[var(--c-border)] bg-[var(--c-surface)] py-3">
     <button v-if="conflict" type="button" class="mini-action" :disabled="busy" @click="beginEdit"><RefreshCw :size="16"/>{{locale.t('specReload')}}</button>
     <button type="button" class="mini-action" :disabled="busy" @click="cancel">{{locale.t('cancel')}}</button>
     <button type="submit" class="inline-flex min-h-12 items-center gap-2 rounded-xl bg-[var(--c-primary)] px-5 text-sm font-bold text-white" :disabled="busy||!dirty||conflict"><Save :size="17"/>{{locale.t(busy?'adminLoading':'save')}}</button>
    </div>
   </form>
  </template>
 </BaseSheet>
</template>
