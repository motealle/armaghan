<script setup lang="ts">
import {createTrackedOrder} from '@/features/orders/services/trackingApi'
import {useSessionStore} from '@/stores/session'

import { computed, ref, watch } from 'vue'
import { CheckCircle2, PackageCheck, PackageX, ShoppingBag } from '@lucide/vue'
import type { Product, RequestPath } from '@/types/domain'
import { buildProductMessage, requestPathTitle, whatsappUrl } from '@/services/whatsapp'
import BaseSheet from '@/components/ui/BaseSheet.vue'
import WhatsAppIcon from '@/components/icons/WhatsAppIcon.vue'
import { useLocaleStore } from '@/stores/locale'
import { customerProductLabel } from '../presentation'

const props=defineProps<{open:boolean;product:Product|null}>()
defineEmits<{close:[]}>()
const locale=useLocaleStore()
const session=useSessionStore(),trackingBusy=ref(false),trackingReference=ref(''),trackingError=ref('')
const path=ref<Extract<RequestPath,'simple'|'available'|'unavailable'>|null>(null)
const displaySubcategory=computed(()=>props.product?locale.subcategoryName(props.product.subcategoryCode,props.product.subcategoryName):'')
const displayName=computed(()=>props.product?customerProductLabel(props.product,displaySubcategory.value,locale.t('unavailableOrProducible')):'')

watch(()=>props.open,(open)=>{if(open){path.value=null;trackingReference.value='';trackingError.value=''}})
watch(path,()=>{trackingReference.value='';trackingError.value=''})
const preview=computed(()=>{
  if(!props.product||!path.value)return ''
  const localized={
    ...props.product,
    name:customerProductLabel(props.product,locale.subcategoryName(props.product.subcategoryCode,props.product.subcategoryName),locale.t('unavailableOrProducible')),
    categoryName:locale.categoryName(props.product.categoryCode,props.product.categoryName),
    subcategoryName:locale.subcategoryName(props.product.subcategoryCode,props.product.subcategoryName),
  }
  return buildProductMessage(localized,path.value,locale.locale)
})
const href=computed(()=>preview.value?whatsappUrl(preview.value):'#')
const options=computed<Array<{id:Extract<RequestPath,'simple'|'available'|'unavailable'>;icon:typeof ShoppingBag;desc:string}>>(()=>[
  {id:'simple',icon:ShoppingBag,desc:locale.t('simplePathDesc')},
  {id:'available',icon:PackageCheck,desc:locale.t('availablePathDesc')},
  {id:'unavailable',icon:PackageX,desc:locale.t('unavailablePathDesc')},
])
async function registerTracking(){
 if(!session.backendAuthenticated||trackingBusy.value||trackingReference.value||!path.value)return
 trackingBusy.value=true;trackingError.value=''
 try{trackingReference.value=(await createTrackedOrder({request_path:path.value,description:preview.value.slice(0,4000)})).order.reference}
 catch{trackingError.value=locale.t('adminRequestFailed')}finally{trackingBusy.value=false}
}
</script>

<template>
  <BaseSheet :open="open" :title="locale.t('whatsappPath')" @close="$emit('close')">
    <div v-if="product" class="space-y-4">
      <div class="rounded-2xl border border-[var(--c-border)] bg-[var(--c-surface-2)] p-3">
        <b class="text-sm text-[var(--c-text)]">{{displayName}}</b>
        <div class="mt-1 text-xs text-[var(--c-muted)]">{{locale.t('codeLabel')}} {{product.code}} · {{displaySubcategory}}</div>
      </div>

      <div class="grid gap-2">
        <button
          v-for="item in options"
          :key="item.id"
          class="flex items-center gap-3 rounded-2xl border p-3 text-start transition"
          :class="path===item.id?'border-[var(--c-secondary)] bg-[color-mix(in_srgb,var(--c-secondary)_8%,var(--c-surface))]':'border-[var(--c-border)] bg-[var(--c-surface)]'"
          @click="path=item.id"
        >
          <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-[color-mix(in_srgb,var(--c-secondary)_12%,var(--c-surface))] text-[var(--c-secondary)]"><component :is="item.icon" :size="21"/></span>
          <span class="min-w-0">
            <b class="block text-sm text-[var(--c-text)]">{{requestPathTitle(item.id,locale.locale)}}</b>
            <small class="mt-0.5 block text-[11px] leading-5 text-[var(--c-muted)]">{{item.desc}}</small>
          </span>
          <CheckCircle2 v-if="path===item.id" class="ms-auto text-[var(--c-secondary)]" :size="20"/>
          <span v-else-if="(item.id==='simple'&&product.availability==='available')||(item.id==='unavailable'&&product.availability!=='available')" class="ms-auto rounded-full bg-[color-mix(in_srgb,var(--c-accent)_16%,var(--c-surface))] px-2 py-1 text-[9px] font-bold text-[var(--c-text)]">{{locale.t('recommendedPath')}}</span>
        </button>
      </div>

      <div v-if="preview" class="message-preview">
        <div class="mb-2 text-xs font-extrabold text-[var(--c-secondary)]">{{locale.t('messagePreview')}}</div>
        <pre class="whitespace-pre-wrap font-sans text-xs leading-6 text-[var(--c-text)]">{{preview}}</pre>
      </div>

      <div v-if="session.backendAuthenticated" class="space-y-2"><button type="button" class="mini-action w-full justify-center" :disabled="trackingBusy||!!trackingReference||!preview" @click="registerTracking">{{locale.locale==='fa'?'ثبت درخواست برای پیگیری':locale.locale==='ar'?'تسجيل الطلب للمتابعة':locale.locale==='ku'?'تۆمارکردن بۆ بەدواداچوون':'Register request for tracking'}}</button><RouterLink v-if="trackingReference" to="/tracking" class="block text-center font-bold text-[var(--c-secondary)]">{{trackingReference}} · {{locale.t('tracking')}}</RouterLink><p v-if="trackingError" role="alert" class="auth-error">{{trackingError}}</p></div>
      <a :href="href" target="_blank" rel="noopener" class="wa-primary flex min-h-14 items-center justify-center gap-2 rounded-xl px-4 text-sm font-extrabold" :class="{'pointer-events-none opacity-40':!preview}">
        <WhatsAppIcon :size="28" tone="white"/> {{locale.t('continueWhatsApp')}}
      </a>
    </div>
  </BaseSheet>
</template>
