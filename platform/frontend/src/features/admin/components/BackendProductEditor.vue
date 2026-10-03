<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { ArrowUp, ArrowDown, ImagePlus, Save } from '@lucide/vue'
import AdaptivePanel from '@/components/ui/AdaptivePanel.vue'
import SmartImage from '@/components/media/SmartImage.vue'
import { useLocaleStore } from '@/stores/locale'
import { useAdminStore } from '../store'
import { CustomerSessionApiError } from '@/features/auth/services/customerSessionApi'
import { createAdminProduct, updateAdminProduct, uploadAdminProductImage, orderAdminProductImages, type AdminProduct, type AdminSubcategory, type ProductFields, type AdminSpecification } from '../services/adminApi'
const props=defineProps<{open:boolean;product:AdminProduct|null;taxonomy:AdminSubcategory[]}>()
const emit=defineEmits<{close:[];saved:[product:AdminProduct]}>()
const locale=useLocaleStore(),admin=useAdminStore()
const draft=ref<ProductFields|null>(null),current=ref<AdminProduct|null>(null)
const saving=ref(false),error=ref(''),note=ref(''),changed=ref(false)
watch(()=>[props.open,props.product] as const,([open,row])=>{
  error.value='';note.value='';changed.value=false;current.value=row
  draft.value=!open?null:row?fields(row):{subcategory_id:props.taxonomy[0]?.id??0,code:'',name_fa:'',name_ar:null,name_en:null,name_ku:null,availability:'available',active:true,sort_order:0,specifications:[]}
},{immediate:true})
function fields(row:AdminProduct):ProductFields{return {subcategory_id:row.subcategory_id,code:row.code,name_fa:row.name_fa,name_ar:row.name_ar,name_en:row.name_en,name_ku:row.name_ku,availability:row.availability,active:row.active,sort_order:row.sort_order,specifications:row.specifications.map(s=>({definition_id:s.id,value_text:s.value_text??null}))}}
const visibleAvailability=computed({get:()=>draft.value?.availability==='available'?'available':'made_to_order',set:(value)=>{if(draft.value)draft.value.availability=value as ProductFields['availability']}})
const group=computed(()=>props.taxonomy.find(s=>s.id===draft.value?.subcategory_id))
const definitions=computed(()=>current.value&&current.value.subcategory_id===draft.value?.subcategory_id?current.value.specifications:group.value?.specifications??[])
function specLabel(spec:AdminSpecification){return spec.labels[locale.locale]||spec.labels.fa}
function specValue(id:number){return draft.value?.specifications?.find(s=>s.definition_id===id)?.value_text??''}
function setSpec(id:number,value:string){
  if(!draft.value)return
  const values=draft.value.specifications??=[]
  const existing=values.find(s=>s.definition_id===id)
  if(existing)existing.value_text=value.trim()?value:null
  else values.push({definition_id:id,value_text:value.trim()?value:null})
}
function changedSubcategory(){if(draft.value)draft.value.specifications=[]}
// Infer only from real server taxonomy, and never reset specifications or localized names.
watch(()=>draft.value?.code,(code)=>{if(!draft.value||current.value)return;const sub=props.taxonomy.find(s=>code?.startsWith(s.code));if(sub)draft.value.subcategory_id=sub.id})
const dirty=computed(()=>Boolean(draft.value&&current.value&&JSON.stringify(draft.value)!==JSON.stringify(fields(current.value))))
function failed(e:unknown){
  if(e instanceof CustomerSessionApiError&&[401,403].includes(e.status)){admin.clear();draft.value=null;current.value=null;emit('close');return locale.t('adminSessionRequired')}
  return locale.t(e instanceof CustomerSessionApiError&&e.status===409?'adminProductConflict':'adminRequestFailed')
}
async function save(){
  if(!draft.value||saving.value)return
  if(!draft.value.name_fa.trim()&&!current.value)draft.value.name_fa=group.value?.name||draft.value.code
  saving.value=true;error.value='';note.value=''
  try{
    const response=current.value?await updateAdminProduct(current.value.id,draft.value,current.value.revision):await createAdminProduct(draft.value)
    current.value=response.product;draft.value=fields(response.product);changed.value=true;note.value=locale.t('adminSaved')
    // Keep the sheet open so a newly created product can receive its gallery immediately.
  }catch(e){error.value=failed(e)}finally{saving.value=false}
}
async function upload(event:Event){
  const input=event.target as HTMLInputElement,file=input.files?.[0];input.value=''
  if(!file||!current.value||saving.value||dirty.value)return
  if(!['image/jpeg','image/png','image/webp'].includes(file.type)||file.size>8*1024*1024){error.value=locale.t('adminImageLimits');return}
  saving.value=true;error.value='';note.value=''
  try{current.value=(await uploadAdminProductImage(current.value,file)).product;changed.value=true;note.value=locale.t('adminSaved')}
  catch(e){error.value=failed(e)}finally{saving.value=false}
}
async function move(index:number,offset:number){
  if(!current.value||saving.value||dirty.value)return
  const ids=current.value.media.map(m=>m.id),target=index+offset
  if(target<0||target>=ids.length)return
  const id=ids[index]!;ids[index]=ids[target]!;ids[target]=id
  saving.value=true;error.value=''
  try{current.value=(await orderAdminProductImages(current.value,ids)).product;changed.value=true;note.value=locale.t('adminSaved')}
  catch(e){error.value=failed(e)}finally{saving.value=false}
}
function close(){if(saving.value)return;if(changed.value&&current.value)emit('saved',current.value);else emit('close')}
</script>
<template>
  <AdaptivePanel :open="open" :title="current?locale.t('editProduct'):locale.t('addProduct')" wide @close="close">
    <form v-if="draft" class="space-y-5" :aria-busy="saving" @submit.prevent="save">
      <section class="admin-surface rounded-2xl p-4">
        <div class="grid gap-3 md:grid-cols-[12rem_1fr_12rem]">
          <label class="form-field">{{locale.t('codeLabel')}}<input v-model="draft.code" required maxlength="64" :disabled="saving" dir="ltr"></label>
          <label class="form-field">{{locale.t('subcategoryLabel')}}<select v-model.number="draft.subcategory_id" required :disabled="saving" @change="changedSubcategory"><option v-for="sub in taxonomy" :key="sub.id" :value="sub.id">{{sub.code}} · {{locale.subcategoryName(sub.code,sub.name)}}{{sub.active?'':' · '+locale.t('adminInactive')}}</option></select><small v-if="group">{{locale.categoryName(group.category_code,group.category_name)}}</small></label>
          <label class="form-field">{{locale.t('statusLabel')}}<select v-model="visibleAvailability" :disabled="saving"><option value="available">{{locale.t('available')}}</option><option value="made_to_order">{{locale.t('madeToOrder')}}</option></select></label>
        </div>
        <div class="mt-3 grid gap-3 md:grid-cols-2"><label class="form-field">{{locale.t('adminSortOrder')}}<input v-model.number="draft.sort_order" type="number" min="0" max="1000000" required :disabled="saving"></label><label class="flex items-center gap-2"><input v-model="draft.active" type="checkbox" :disabled="saving">{{locale.t('active')}}</label></div>
        <p class="mt-3 text-xs text-[var(--c-muted)]">{{locale.t('adminProductArchiveHelp')}}</p>
      </section>
      <details class="admin-surface rounded-2xl p-4"><summary class="text-sm font-bold">{{locale.t('namesByLanguage')}}</summary><div class="grid gap-3 md:grid-cols-2">
        <label class="form-field" lang="fa">فارسی<input v-model="draft.name_fa" dir="rtl" maxlength="255" :disabled="saving"></label>
        <label class="form-field" lang="ar">العربية<input v-model="draft.name_ar" dir="rtl" maxlength="255" :disabled="saving"></label>
        <label class="form-field" lang="en">English<input v-model="draft.name_en" dir="ltr" maxlength="255" :disabled="saving"></label>
        <label class="form-field" lang="ckb">کوردی<input v-model="draft.name_ku" dir="rtl" maxlength="255" :disabled="saving"></label>
      </div></details>
      <section class="admin-surface rounded-2xl p-4">
        <h3 class="mb-3 text-sm font-black">{{locale.t('adminProductGallery')}}</h3><p class="mb-3 text-xs text-[var(--c-muted)]">{{locale.t('adminImageLimits')}}</p>
        <p v-if="!current||dirty" class="text-sm">{{locale.t('adminSaveBeforeImage')}}</p>
        <div v-if="current" class="grid grid-cols-2 gap-3 sm:grid-cols-3">
          <article v-for="(media,index) in current.media" :key="media.id" class="rounded-xl border border-[var(--c-border)] p-2">
            <SmartImage :src="media.thumb_url" :alt="draft.name_fa" class="aspect-[3/4] w-full rounded-lg" fit="contain"/>
            <div class="mt-2 flex items-center justify-between gap-1"><span class="text-xs">{{index+1}}</span><button type="button" class="mini-action" :disabled="saving||dirty||index===0" :aria-label="locale.t('previous')" @click="move(index,-1)"><ArrowUp :size="15"/></button><button type="button" class="mini-action" :disabled="saving||dirty||index===current.media.length-1" :aria-label="locale.t('next')" @click="move(index,1)"><ArrowDown :size="15"/></button></div>
          </article>
        </div>
        <label v-if="current&&current.media.length<6&&!dirty&&!saving" class="mini-action mt-3 cursor-pointer"><ImagePlus :size="16"/>{{locale.t('adminAddImage')}}<input class="sr-only" type="file" accept="image/jpeg,image/png,image/webp" @change="upload"></label>
      </section>
      <section class="admin-surface rounded-2xl p-4">
        <h3 class="mb-3 text-sm font-black">{{locale.t('productSpecs')}}</h3>
        <p v-if="!definitions.length" class="text-sm text-[var(--c-muted)]">{{locale.t('adminSpecsEmpty')}}</p>
        <div v-else class="grid gap-3 md:grid-cols-2">
          <label v-for="spec in definitions" :key="spec.id" class="form-field">
            <span>{{specLabel(spec)}} <small class="text-[var(--c-muted)]">· {{locale.t(spec.locked?'locked':'negotiable')}}</small></span>
            <input :value="specValue(spec.id)" maxlength="1000" :disabled="saving" @input="setSpec(spec.id,($event.target as HTMLInputElement).value)">
          </label>
        </div>
      </section>
      <p v-if="error" role="alert" class="auth-error">{{error}}</p><p v-if="note" role="status" class="text-xs text-[var(--c-secondary)]">{{note}}</p>
      <div class="sticky bottom-0 z-10 flex justify-end gap-2 border-t border-[var(--c-border)] bg-[color-mix(in_srgb,var(--c-surface)_96%,transparent)] py-3 backdrop-blur">
        <button type="button" class="mini-action" :disabled="saving" @click="close">{{locale.t('close')}}</button><button class="inline-flex min-h-11 items-center gap-2 rounded-xl bg-[var(--c-primary)] px-4 text-sm font-black text-white" :disabled="saving||!draft.code.trim()||!draft.subcategory_id"><Save :size="17"/>{{locale.t(saving?'adminLoading':'save')}}</button>
      </div>
    </form>
  </AdaptivePanel>
</template>
