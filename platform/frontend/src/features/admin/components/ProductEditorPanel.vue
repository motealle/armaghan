<script setup lang="ts">
import { computed, ref, toRaw, watch } from 'vue'
import { LockKeyhole, RotateCcw, Save, SlidersHorizontal } from '@lucide/vue'
import AdaptivePanel from '@/components/ui/AdaptivePanel.vue'
import { productDefaultsFromCode } from '@/data/catalog'
import { useCatalogStore } from '@/stores/catalog'
import { useLocaleStore } from '@/stores/locale'
import type { Product, ProductNames } from '@/types/domain'

const props=defineProps<{open:boolean;productId:number|null}>()
const emit=defineEmits<{close:[]}>()
const catalog=useCatalogStore()
const locale=useLocaleStore()
const draft=ref<Product|null>(null)
const error=ref('')
const note=ref('')
const lastPrefix=ref('')

function cloneProduct(product:Product){return structuredClone(toRaw(product)) as Product}
function namesFrom(product:Product):ProductNames{
  return {
    fa:product.names?.fa||locale.productNameFor(product.code,'fa',product.name,product.names),
    ar:product.names?.ar||locale.productNameFor(product.code,'ar',product.name,product.names),
    en:product.names?.en||locale.productNameFor(product.code,'en',product.name,product.names),
    ku:product.names?.ku||locale.productNameFor(product.code,'ku',product.name,product.names),
  }
}
function newProduct():Product{
  return {
    id:Math.max(0,...catalog.items.map(item=>item.id))+1,
    code:'',
    name:'',
    names:{fa:'',ar:'',en:'',ku:''},
    categoryCode:'1',
    subcategoryCode:'11',
    categoryName:'',
    subcategoryName:'',
    availability:'available',
    specs:{locked:[],negotiable:[]},
  }
}
function applyCodeDefaults(force=false){
  if(!draft.value)return
  const prefix=draft.value.code.trim().slice(0,2)
  const defaults=productDefaultsFromCode(draft.value.code)
  if(!defaults){error.value=draft.value.code.length>=2?locale.t('invalidProductCode'):'';return}
  if(!force&&prefix===lastPrefix.value)return
  Object.assign(draft.value,defaults)
  lastPrefix.value=prefix
  error.value=''
}
watch(()=>[props.open,props.productId] as const,([open,id])=>{
  if(!open){draft.value=null;error.value='';note.value='';lastPrefix.value='';return}
  const existing=id===null?null:catalog.items.find(item=>item.id===id)??null
  draft.value=existing?cloneProduct(existing):newProduct()
  if(existing)draft.value.names=namesFrom(existing)
  lastPrefix.value=''
  if(existing)applyCodeDefaults(true)
},{immediate:true})
watch(()=>draft.value?.code,()=>applyCodeDefaults())

const inferredValid=computed(()=>Boolean(draft.value&&productDefaultsFromCode(draft.value.code)))
function ensureNames(){
  if(!draft.value)return {fa:'',ar:'',en:'',ku:''} as ProductNames
  if(!draft.value.names)draft.value.names={fa:'',ar:'',en:'',ku:''}
  return draft.value.names
}
const nameFa=computed({get:()=>ensureNames().fa??'',set:(value:string)=>{ensureNames().fa=value}})
const nameAr=computed({get:()=>ensureNames().ar??'',set:(value:string)=>{ensureNames().ar=value}})
const nameEn=computed({get:()=>ensureNames().en??'',set:(value:string)=>{ensureNames().en=value}})
const nameKu=computed({get:()=>ensureNames().ku??'',set:(value:string)=>{ensureNames().ku=value}})

const lockedText=computed({
  get:()=>draft.value?.specs.locked.join('\n')??'',
  set:(value:string)=>{if(draft.value)draft.value.specs.locked=value.split(/\n|,/).map(v=>v.trim()).filter(Boolean)},
})
const negotiableText=computed({
  get:()=>draft.value?.specs.negotiable.join('\n')??'',
  set:(value:string)=>{if(draft.value)draft.value.specs.negotiable=value.split(/\n|,/).map(v=>v.trim()).filter(Boolean)},
})
function save(){
  if(!draft.value)return
  const defaults=productDefaultsFromCode(draft.value.code)
  if(!defaults){error.value=locale.t('invalidProductCode');return}
  const faName=draft.value.names?.fa?.trim()||draft.value.name.trim()
  if(!faName){error.value=locale.t('fullName');return}
  draft.value.name=faName
  if(props.productId===null)catalog.add(cloneProduct(draft.value))
  else catalog.update(cloneProduct(draft.value))
  note.value=locale.t('save')
  window.setTimeout(()=>emit('close'),180)
}
</script>

<template>
  <AdaptivePanel :open="open" :title="productId===null?locale.t('addProduct'):locale.t('editProduct')" wide @close="emit('close')">
    <div v-if="draft" class="space-y-5">
      <section class="admin-surface rounded-2xl p-4">
        <div class="grid gap-3 md:grid-cols-[12rem_1fr_12rem]">
          <label class="form-field">{{locale.t('codeLabel')}}<input v-model="draft.code" inputmode="numeric" autofocus/></label>
          <div class="rounded-xl border border-[var(--c-border)] bg-[var(--c-surface-2)] p-3 text-xs leading-6 text-[var(--c-muted)]">
            <b class="block text-[var(--c-text)]">{{locale.t('inferredFromCode')}}</b>
            <span v-if="inferredValid">{{locale.categoryName(draft.categoryCode,draft.categoryName)}} · {{draft.subcategoryCode}} · {{locale.subcategoryName(draft.subcategoryCode,draft.subcategoryName)}}</span>
            <span v-else>{{locale.t('invalidProductCode')}}</span>
          </div>
          <label class="form-field">{{locale.t('statusLabel')}}
            <select v-model="draft.availability">
              <option value="available">{{locale.t('available')}}</option>
              <option value="unavailable">{{locale.t('unavailable')}}</option>
              <option value="made_to_order">{{locale.t('madeToOrder')}}</option>
            </select>
          </label>
        </div>
        <p v-if="error" class="auth-error mt-3">{{error}}</p>
      </section>

      <section class="admin-surface rounded-2xl p-4">
        <h3 class="mb-3 text-sm font-black">{{locale.t('namesByLanguage')}}</h3>
        <div class="grid gap-3 md:grid-cols-2">
          <label class="form-field" lang="fa">فارسی<input v-model="nameFa" dir="rtl"/></label>
          <label class="form-field" lang="ar">العربية<input v-model="nameAr" dir="rtl"/></label>
          <label class="form-field" lang="en">English<input v-model="nameEn" dir="ltr"/></label>
          <label class="form-field" lang="ckb">کوردی<input v-model="nameKu" dir="rtl"/></label>
        </div>
      </section>

      <section class="admin-surface rounded-2xl p-4">
        <div class="mb-3 flex flex-wrap items-center gap-2">
          <h3 class="text-sm font-black">{{locale.t('productSpecs')}}</h3>
          <button class="mini-action ms-auto" :disabled="!inferredValid" @click="applyCodeDefaults(true)"><RotateCcw :size="15"/>{{locale.t('resetFromCode')}}</button>
        </div>
        <div class="grid gap-3 md:grid-cols-2">
          <label class="form-field">
            <span class="mb-1 inline-flex items-center gap-1"><LockKeyhole :size="15"/>{{locale.t('lockedSpecs')}}</span>
            <textarea v-model="lockedText" rows="7"/>
          </label>
          <label class="form-field">
            <span class="mb-1 inline-flex items-center gap-1 text-[var(--c-secondary)]"><SlidersHorizontal :size="15"/>{{locale.t('negotiableSpecs')}}</span>
            <textarea v-model="negotiableText" rows="7"/>
          </label>
        </div>
      </section>

      <div class="sticky bottom-0 z-10 flex items-center justify-end gap-2 border-t border-[var(--c-border)] bg-[color-mix(in_srgb,var(--c-surface)_96%,transparent)] py-3 backdrop-blur">
        <span v-if="note" class="me-auto text-xs font-bold text-[var(--c-secondary)]">{{note}}</span>
        <button class="inline-flex min-h-11 items-center gap-2 rounded-xl bg-[var(--c-primary)] px-4 text-sm font-black text-white" :disabled="!inferredValid" @click="save"><Save :size="17"/>{{locale.t('save')}}</button>
      </div>
    </div>
  </AdaptivePanel>
</template>
