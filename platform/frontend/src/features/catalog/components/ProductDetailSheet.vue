<script setup lang="ts">
import { LockKeyhole, SlidersHorizontal } from '@lucide/vue'
import type { Product } from '@/types/domain'
import BaseSheet from '@/components/ui/BaseSheet.vue'

defineProps<{ open: boolean; product: Product | null }>()
defineEmits<{ close: [] }>()
</script>

<template>
  <BaseSheet :open="open" :title="product?.name ?? 'مشخصات محصول'" @close="$emit('close')">
    <template v-if="product">
      <div class="mb-4 flex flex-wrap gap-2 text-xs">
        <span class="rounded-full bg-slate-100 px-2.5 py-1">کد {{ product.code }}</span>
        <span class="rounded-full bg-emerald-50 px-2.5 py-1 font-bold text-emerald-700">
          {{ product.availability === 'available' ? 'موجود' : 'ناموجود' }}
        </span>
      </div>
      <div class="mb-4 flex flex-wrap gap-2 text-[11px]">
        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1"><LockKeyhole :size="15" /> غیرقابل تغییر</span>
        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-emerald-800"><SlidersHorizontal :size="15" /> قابل مذاکره</span>
      </div>
      <div class="grid grid-cols-2 gap-2">
        <article v-for="item in product.specs.locked" :key="'l-'+item" class="rounded-xl border border-slate-200 bg-slate-100 p-3 text-slate-600">
          <div class="flex items-center gap-2"><LockKeyhole :size="17" /><b class="text-xs">{{ item }}</b></div>
          <small class="mt-1 block text-[10px]">غیرقابل تغییر</small>
        </article>
        <article v-for="item in product.specs.negotiable" :key="'n-'+item" class="rounded-xl border border-emerald-100 bg-emerald-50 p-3 text-emerald-800">
          <div class="flex items-center gap-2"><SlidersHorizontal :size="17" /><b class="text-xs">{{ item }}</b></div>
          <small class="mt-1 block text-[10px]">قابل مذاکره</small>
        </article>
      </div>
    </template>
  </BaseSheet>
</template>
