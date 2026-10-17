<script setup lang="ts">
import { computed, ref, watch, nextTick } from 'vue'
import { ArrowUp, ArrowDown, ChevronsUp, ChevronsDown, Search, Save, RefreshCw, ImageOff } from '@lucide/vue'
import AdaptivePanel from '@/components/ui/AdaptivePanel.vue'
import { useLocaleStore } from '@/stores/locale'
import { useAdminStore } from '../store'
import { CustomerSessionApiError } from '@/features/auth/services/customerSessionApi'
import { normalizeSearch } from '@/features/catalog/services/productFilters'
import { fetchProductOrder,saveProductOrder,type ProductOrderGroup,type ProductOrderSnapshot,type OrderedProduct } from '../services/adminApi'
import { moveOrderedProduct,sameProductOrder,type OrderMove } from '../services/productOrder'
const props=withDefaults(defineProps<{open:boolean;initialGroup?:ProductOrderGroup}>(),{initialGroup:'producible'})
const emit=defineEmits<{close:[];saved:[]}>()
const locale=useLocaleStore(),admin=useAdminStore()
const groups:ProductOrderGroup[]=['available','producible']
const current=ref<ProductOrderSnapshot|null>(null),draft=ref<Record<ProductOrderGroup,OrderedProduct[]>>({available:[],producible:[]})
const group=ref<ProductOrderGroup>('producible'),selected=ref<number|null>(null),query=ref(''),busy=ref(false),error=ref(''),note=ref(''),conflict=ref(false)
const failedImages=ref<number[]>([]),list=ref<HTMLElement|null>(null)
const rows=computed(()=>draft.value[group.value])
const dirty=computed(()=>current.value&&groups.some(key=>!sameProductOrder(draft.value[key],current.value!.groups[key].products)))
function apply(snapshot:ProductOrderSnapshot){current.value=snapshot;draft.value={available:[...snapshot.groups.available.products],producible:[...snapshot.groups.producible.products]};conflict.value=false}
function failure(e:unknown){
 if(e instanceof CustomerSessionApiError&&[401,403].includes(e.status)){admin.clear();emit('close');return}
 conflict.value=e instanceof CustomerSessionApiError&&e.status===409
 error.value=locale.t(conflict.value?'orderConflict':'adminRequestFailed')
}
async function load(){
 if(busy.value||!admin.identity)return
 if(dirty.value&&!window.confirm(locale.t('specDiscardChanges')))return
 busy.value=true;error.value='';note.value=''
 try{const snapshot=await fetchProductOrder();if(props.open&&admin.identity)apply(snapshot)}catch(e){failure(e)}finally{busy.value=false}
}
watch(()=>props.open,open=>{if(open){current.value=null;draft.value={available:[],producible:[]};group.value=props.initialGroup;selected.value=null;query.value='';void load()}},{immediate:true})
watch(()=>admin.identity,value=>{if(!value){current.value=null;draft.value={available:[],producible:[]};emit('close')}})
function close(){if(busy.value)return;if(dirty.value&&!window.confirm(locale.t('specDiscardChanges')))return;emit('close')}
async function reveal(id:number){await nextTick();const node=list.value?.querySelector<HTMLElement>('[data-order-id="'+id+'"]');node?.scrollIntoView({block:'center',behavior:'auto'});node?.focus({preventScroll:true})}
function move(id:number,direction:OrderMove){if(busy.value||conflict.value)return;draft.value[group.value]=moveOrderedProduct(rows.value,id,direction);selected.value=id;note.value='';void reveal(id)}
function jump(){
 const code=normalizeSearch(query.value).replace(/^کد\s*/,'');note.value=''
 for(const key of [group.value,...groups.filter(g=>g!==group.value)]){
  const found=draft.value[key].find(row=>normalizeSearch(row.code)===code)
  if(found){group.value=key;selected.value=found.id;void reveal(found.id);return}
 }
 note.value='orderNotFound'
}
async function save(){
 if(busy.value||!current.value||!dirty.value||conflict.value||!admin.identity)return
 busy.value=true;error.value='';note.value=''
 try{
  let latest=current.value
  for(const key of groups){
   if(sameProductOrder(draft.value[key],current.value.groups[key].products))continue
   const result:ProductOrderSnapshot=await saveProductOrder(key,current.value.groups[key].revision,draft.value[key].map(row=>row.id))
   if(!props.open||!admin.identity)return
   current.value.groups[key]=result.groups[key];draft.value[key]=[...result.groups[key].products];latest=result;emit('saved')
  }
  apply(latest);note.value='orderSaved'
 }catch(e){failure(e)}finally{busy.value=false}
}
</script>
<template>
 <AdaptivePanel :open="open" :title="locale.t('orderTitle')" @close="close">
  <section :aria-busy="busy" class="space-y-4">
   <p class="rounded-xl bg-[var(--c-surface-2)] p-3 text-sm leading-7">{{locale.t('orderHelp')}}</p>
   <div class="grid grid-cols-2 gap-2" role="group" :aria-label="locale.t('statusLabel')">
    <button v-for="key in groups" :key="key" type="button" class="min-h-12 rounded-xl border border-[var(--c-border)] px-2 text-sm font-bold" :class="group===key?'bg-[var(--c-primary)] text-white':'bg-[var(--c-surface-2)]'" :aria-pressed="group===key" :disabled="busy" @click="group=key;selected=null">{{locale.t(key==='available'?'available':'madeToOrder')}} · {{draft[key].length}}</button>
   </div>
   <form class="flex gap-2" @submit.prevent="jump"><label class="sr-only" for="order-code">{{locale.t('orderFind')}}</label><input id="order-code" v-model="query" class="min-h-12 min-w-0 flex-1 rounded-xl border border-[var(--c-border)] bg-[var(--c-surface)] px-3 text-base" :placeholder="locale.t('orderFind')" :disabled="busy" inputmode="numeric"><button type="submit" class="mini-action" :disabled="busy||!query.trim()"><Search :size="17"/>{{locale.t('orderGo')}}</button></form>
   <p v-if="busy" role="status" class="text-sm">{{locale.t('adminLoading')}}</p>
   <p v-if="error" role="alert" class="auth-error">{{error}}</p><p v-if="note" role="status" class="text-sm text-[var(--c-secondary)]">{{locale.t(note)}}</p>
   <p v-if="current&&!rows.length" class="p-4 text-center text-sm">{{locale.t('adminNoProducts')}}</p>
   <div ref="list" class="space-y-2">
    <article v-for="(product,index) in rows" :key="product.id" :data-order-id="product.id" tabindex="-1" class="min-w-0 rounded-2xl border border-[var(--c-border)] bg-[var(--c-surface)] p-2 focus:outline-2 focus:outline-[var(--c-primary)]" :class="selected===product.id?'ring-2 ring-[var(--c-primary)]':''">
     <button type="button" class="flex min-h-14 w-full items-center gap-3 text-start" :aria-pressed="selected===product.id" :disabled="busy" @click="selected=selected===product.id?null:product.id">
      <span class="flex h-14 w-11 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-[var(--c-surface-2)]"><img v-if="product.thumb_url&&!failedImages.includes(product.id)" :src="product.thumb_url" alt="" class="h-full w-full object-contain" loading="lazy" @error="failedImages.push(product.id)"><ImageOff v-else :size="18" class="text-[var(--c-muted)]"/></span>
      <span class="min-w-0 flex-1"><b class="block text-sm" dir="ltr">{{product.code}}</b><small class="block truncate text-[var(--c-muted)]">{{locale.subcategoryName(product.subcategory_code,product.label)}}</small></span>
      <span class="shrink-0 rounded-lg bg-[var(--c-surface-2)] px-2 py-1 text-xs">{{locale.t('orderPosition')}} {{index+1}}</span>
     </button>
     <div v-if="selected===product.id" class="mt-2 grid grid-cols-4 gap-1 border-t border-[var(--c-border)] pt-2">
      <button v-for="action in (['first','up','down','last'] as const)" :key="action" type="button" class="flex min-h-12 flex-col items-center justify-center gap-1 rounded-lg bg-[var(--c-surface-2)] text-xs disabled:opacity-40" :disabled="busy||conflict||(['first','up'].includes(action)?index===0:index===rows.length-1)" @click="move(product.id,action)"><component :is="{first:ChevronsUp,up:ArrowUp,down:ArrowDown,last:ChevronsDown}[action]" :size="17"/>{{locale.t('order'+action)}}</button>
     </div>
    </article>
   </div>
   <div class="sticky bottom-0 flex flex-wrap justify-end gap-2 border-t border-[var(--c-border)] bg-[var(--c-surface)] py-3">
    <button type="button" class="mini-action" :disabled="busy" @click="load"><RefreshCw :size="16"/>{{locale.t('adminReload')}}</button><button type="button" class="mini-action" :disabled="busy" @click="close">{{locale.t('close')}}</button>
    <button type="button" class="inline-flex min-h-12 items-center gap-2 rounded-xl bg-[var(--c-primary)] px-4 text-sm font-bold text-white" :disabled="busy||!dirty||conflict" @click="save"><Save :size="17"/>{{locale.t('orderSave')}}</button>
   </div>
  </section>
 </AdaptivePanel>
</template>
