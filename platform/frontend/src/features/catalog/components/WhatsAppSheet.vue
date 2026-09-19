<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { CheckCircle2, PackageCheck, PackageX, ShoppingBag } from '@lucide/vue'
import type { Product, RequestPath } from '@/types/domain'
import { buildProductMessage, requestPathTitle, whatsappUrl } from '@/services/whatsapp'
import BaseSheet from '@/components/ui/BaseSheet.vue'
import WhatsAppIcon from '@/components/icons/WhatsAppIcon.vue'

const props = defineProps<{ open: boolean; product: Product | null }>()
defineEmits<{ close: [] }>()

const path = ref<Extract<RequestPath, 'simple' | 'available' | 'unavailable'> | null>(null)

watch(() => props.open, (open) => { if (open) path.value = null })

const preview = computed(() => props.product && path.value ? buildProductMessage(props.product, path.value) : '')
const href = computed(() => preview.value ? whatsappUrl(preview.value) : '#')

const options: Array<{ id: Extract<RequestPath,'simple'|'available'|'unavailable'>; icon: typeof ShoppingBag; desc: string }> = [
  { id: 'simple', icon: ShoppingBag, desc: 'برای شروع سریع گفتگو درباره همین محصول' },
  { id: 'available', icon: PackageCheck, desc: 'برای محصول موجود و آماده مذاکره فروش' },
  { id: 'unavailable', icon: PackageX, desc: 'برای درخواست محصول ناموجود یا تولید مجدد' },
]
</script>

<template>
  <BaseSheet :open="open" title="مسیر واتساپ" @close="$emit('close')">
    <div v-if="product" class="space-y-4">
      <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3">
        <b class="text-sm">{{ product.name }}</b>
        <div class="mt-1 text-xs text-slate-500">کد {{ product.code }} · {{ product.subcategoryName }}</div>
      </div>

      <div class="grid gap-2">
        <button
          v-for="item in options"
          :key="item.id"
          class="flex items-center gap-3 rounded-2xl border p-3 text-start transition"
          :class="path === item.id ? 'border-emerald-400 bg-emerald-50' : 'border-slate-200 bg-white'"
          @click="path = item.id"
        >
          <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-emerald-100 text-emerald-700"><component :is="item.icon" :size="21" /></span>
          <span class="min-w-0">
            <b class="block text-sm">{{ requestPathTitle(item.id) }}</b>
            <small class="mt-0.5 block text-[11px] leading-5 text-slate-500">{{ item.desc }}</small>
          </span>
          <CheckCircle2 v-if="path === item.id" class="ms-auto text-emerald-600" :size="20" />
          <span v-else-if="(item.id === 'available' && product.availability === 'available') || (item.id === 'unavailable' && product.availability !== 'available')" class="ms-auto rounded-full bg-amber-50 px-2 py-1 text-[9px] font-bold text-amber-800">پیشنهادی</span>
        </button>
      </div>

      <div v-if="preview" class="rounded-2xl border border-emerald-100 bg-emerald-50 p-3">
        <div class="mb-2 text-xs font-extrabold text-emerald-800">پیش‌نمایش پیام</div>
        <pre class="whitespace-pre-wrap font-sans text-xs leading-6 text-slate-700">{{ preview }}</pre>
      </div>

      <a
        :href="href"
        target="_blank"
        rel="noopener"
        class="wa-primary flex min-h-14 items-center justify-center gap-2 rounded-[.25rem] px-4 text-sm font-extrabold"
        :class="{ 'pointer-events-none opacity-40': !preview }"
      >
        <span class="wa-logo-chip"><WhatsAppIcon :size="23" /></span>
        ادامه در واتساپ
      </a>
    </div>
  </BaseSheet>
</template>
