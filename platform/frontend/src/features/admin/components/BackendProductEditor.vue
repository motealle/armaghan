<script setup lang="ts">
import { computed, ref, watch, nextTick, onMounted, onBeforeUnmount } from 'vue'
import { ArrowUp, ArrowDown, ImagePlus, Save } from '@lucide/vue'
import { onBeforeRouteLeave } from 'vue-router'
import AdaptivePanel from '@/components/ui/AdaptivePanel.vue'
import SmartImage from '@/components/media/SmartImage.vue'
import { useLocaleStore } from '@/stores/locale'
import { useAdminStore } from '../store'
import { productFailure, useProductDraftGuard } from '../services/productEditorSafety'
import { ProductImagePreparationError, optimizeProductImage, submitProductImages, validProductImage } from '../services/productImageSubmission'
import { CustomerSessionApiError } from '@/features/auth/services/customerSessionApi'
import { createAdminProduct, updateAdminProduct, uploadAdminProductImage, orderAdminProductImages, type AdminProduct, type AdminSubcategory, type ProductFields, type AdminSpecification } from '../services/adminApi'
const props=defineProps<{open:boolean;product:AdminProduct|null;taxonomy:AdminSubcategory[]}>()
const emit=defineEmits<{close:[];saved:[product:AdminProduct]}>()
const locale=useLocaleStore(),admin=useAdminStore()
const draft=ref<ProductFields|null>(null),current=ref<AdminProduct|null>(null)
const saving=ref(false),error=ref(''),note=ref(''),changed=ref(false),writesBlocked=ref(false)
type PendingImage={file:File;preview:string}
const pendingImages=ref<PendingImage[]>([])
const pendingCount=computed(()=>pendingImages.value.length)
const uploadProgress=ref({done:0,total:0})
const guard=useProductDraftGuard(draft,saving,pendingCount)
const hasUnsaved=guard.dirty,confirmClose=guard.confirmClose,closePrompt=ref<HTMLElement|null>(null)
watch(()=>[props.open,props.product] as const,([open,row])=>{
  clearPendingImages();error.value='';note.value='';changed.value=false;writesBlocked.value=false;uploadProgress.value={done:0,total:0};current.value=row
  draft.value=!open?null:row?fields(row):{subcategory_id:props.taxonomy[0]?.id??0,code:'',name_fa:'',name_ar:null,name_en:null,name_ku:null,availability:'available',active:true,sort_order:0,specifications:[]}
  guard.checkpoint()
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
  const failure=productFailure(e);writesBlocked.value=failure.block;return locale.t(failure.key)
}
async function save(){
  if(!draft.value||saving.value||writesBlocked.value)return
  if(!draft.value.name_fa.trim()&&!current.value)draft.value.name_fa=group.value?.name||draft.value.code
  saving.value=true;error.value='';note.value=''
  let persisted=false
  const files=pendingImages.value.map(item=>item.file)
  uploadProgress.value={done:0,total:files.length}
  try{
    await submitProductImages({
      current:current.value,dirty:dirty.value,files,
      persist:async()=>{const response=current.value?await updateAdminProduct(current.value.id,draft.value!,current.value.revision):await createAdminProduct(draft.value!);return response.product},
      persisted:(product)=>{current.value=product;draft.value=fields(product);guard.checkpoint();changed.value=true;persisted=true},
      prepare:optimizeProductImage,
      upload:async(product,file)=>(await uploadAdminProductImage(product,file)).product,
      uploaded:(product,source)=>{current.value=product;const index=pendingImages.value.findIndex(item=>item.file===source);if(index>=0){URL.revokeObjectURL(pendingImages.value[index]!.preview);pendingImages.value.splice(index,1)};uploadProgress.value.done+=1;changed.value=true;guard.checkpoint()},
    })
    note.value=locale.t('adminSaved')
  }catch(e){
    if(e instanceof ProductImagePreparationError){error.value=locale.t('adminImageLimits');writesBlocked.value=false}
    else error.value=failed(e)
    if(persisted&&draft.value)note.value=locale.t('adminProductSavedImagePending')
  }finally{saving.value=false;uploadProgress.value={done:0,total:0}}
}
function upload(event:Event){
  const input=event.target as HTMLInputElement,files=Array.from(input.files??[]);input.value=''
  if(!files.length||saving.value||writesBlocked.value)return
  const remaining=Math.max(0,6-(current.value?.media.length??0)-pendingImages.value.length)
  const valid=files.filter(validProductImage).slice(0,remaining)
  if(valid.length!==files.length)error.value=locale.t('adminImageLimits')
  else error.value=''
  const existing=new Set(pendingImages.value.map(item=>item.file.name+'|'+item.file.size+'|'+item.file.lastModified))
  for(const file of valid){
    const key=file.name+'|'+file.size+'|'+file.lastModified
    if(existing.has(key))continue
    pendingImages.value.push({file,preview:URL.createObjectURL(file)});existing.add(key)
  }
  note.value=''
}
function removePendingImage(index:number){
  if(saving.value)return
  const [removed]=pendingImages.value.splice(index,1)
  if(removed)URL.revokeObjectURL(removed.preview)
  note.value=''
}
function clearPendingImages(){
  for(const item of pendingImages.value)URL.revokeObjectURL(item.preview)
  pendingImages.value=[]
}
async function move(index:number,offset:number){
  if(!current.value||saving.value||dirty.value||writesBlocked.value)return
  const ids=current.value.media.map(m=>m.id),target=index+offset
  if(target<0||target>=ids.length)return
  const id=ids[index]!;ids[index]=ids[target]!;ids[target]=id
  saving.value=true;error.value=''
  try{current.value=(await orderAdminProductImages(current.value,ids)).product;changed.value=true;note.value=locale.t('adminSaved')}
  catch(e){error.value=failed(e)}finally{saving.value=false}
}
function finishClose(){clearPendingImages();if(changed.value&&current.value)emit('saved',current.value);else emit('close')}
async function close(){guard.requestClose(finishClose);if(confirmClose.value){await nextTick();closePrompt.value?.querySelector<HTMLButtonElement>('button')?.focus()}}
function discard(){guard.discard(finishClose)}
function beforeUnload(event:BeforeUnloadEvent){if(props.open&&(hasUnsaved.value||saving.value)){event.preventDefault();event.returnValue=''}}
onBeforeRouteLeave(()=>!props.open||(!saving.value&&(!hasUnsaved.value||window.confirm(locale.t('adminProductDiscardHelp')))))
onMounted(()=>window.addEventListener('beforeunload',beforeUnload))
onBeforeUnmount(()=>{clearPendingImages();window.removeEventListener('beforeunload',beforeUnload)})
</script>
<template>
  <AdaptivePanel :open="open" :title="current?locale.t('editProduct'):locale.t('addProduct')" wide @close="close">
    <section v-if="confirmClose" ref="closePrompt" role="alert" class="admin-surface mb-4 space-y-3 rounded-2xl p-4">
      <p>{{locale.t('adminProductDiscardHelp')}}</p>
      <div class="flex flex-wrap gap-2"><button type="button" class="mini-action" @click="confirmClose=false">{{locale.t('adminProductKeepEditing')}}</button><button type="button" class="mini-action" @click="discard">{{locale.t('adminProductDiscard')}}</button></div>
    </section>
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
        <p class="text-sm">{{locale.t('adminImageSelectionHelp')}}</p>
        <div v-if="current" class="grid grid-cols-2 gap-3 sm:grid-cols-3">
          <article v-for="(media,index) in current.media" :key="media.id" class="rounded-xl border border-[var(--c-border)] p-2">
            <SmartImage :src="media.thumb_url" :alt="draft.name_fa" class="aspect-[3/4] w-full rounded-lg" fit="contain"/>
            <div class="mt-2 flex items-center justify-between gap-1"><span class="text-xs">{{index+1}}</span><button type="button" class="mini-action" :disabled="writesBlocked||saving||dirty||index===0" :aria-label="locale.t('previous')" @click="move(index,-1)"><ArrowUp :size="15"/></button><button type="button" class="mini-action" :disabled="writesBlocked||saving||dirty||index===current.media.length-1" :aria-label="locale.t('next')" @click="move(index,1)"><ArrowDown :size="15"/></button></div>
          </article>
        </div>
        <label class="form-field mt-3"><span class="flex items-center gap-2"><ImagePlus :size="16"/>{{locale.t('adminAddImage')}} <small dir="ltr">{{(current?.media.length??0)+pendingImages.length}} / 6</small></span><input type="file" multiple accept="image/jpeg,image/png,image/webp" :disabled="writesBlocked||saving||(current?.media.length??0)+pendingImages.length>=6" @change="upload"></label>
        <div v-if="pendingImages.length" class="mt-3 space-y-3 rounded-xl border border-[var(--c-border)] p-3">
          <p role="status" class="text-sm">{{locale.t('adminImagePending')}} · {{pendingImages.length}}</p>
          <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
            <article v-for="(item,index) in pendingImages" :key="item.preview" class="rounded-lg border border-[var(--c-border)] p-2">
              <img :src="item.preview" :alt="item.file.name" class="aspect-[3/4] w-full rounded-md object-contain" loading="lazy">
              <p class="mt-1 truncate text-[11px]" dir="auto">{{item.file.name}}</p>
              <p class="text-[10px] text-[var(--c-muted)]" dir="ltr">{{(item.file.size/1024/1024).toFixed(1)}} MB</p>
              <button type="button" class="mini-action mt-1 w-full" :disabled="saving" @click="removePendingImage(index)">{{locale.t('adminImageRemoveSelection')}}</button>
            </article>
          </div>
          <p v-if="saving&&uploadProgress.total" class="text-xs text-[var(--c-secondary)]" dir="ltr">{{uploadProgress.done}} / {{uploadProgress.total}}</p>
        </div>
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
        <button type="button" class="mini-action" :disabled="saving" @click="close">{{locale.t('close')}}</button><button class="inline-flex min-h-11 items-center gap-2 rounded-xl bg-[var(--c-primary)] px-4 text-sm font-black text-white" :disabled="writesBlocked||saving||!draft.code.trim()||!draft.subcategory_id"><Save :size="17"/>{{locale.t(saving?'adminLoading':pendingImages.length?'adminSaveAndUpload':'save')}}</button>
      </div>
    </form>
  </AdaptivePanel>
</template>
