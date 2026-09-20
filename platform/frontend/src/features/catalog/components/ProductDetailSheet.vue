<script setup lang="ts">
import { computed } from 'vue'
import { LockKeyhole, SlidersHorizontal } from '@lucide/vue'
import type { Product } from '@/types/domain'
import BaseSheet from '@/components/ui/BaseSheet.vue'
import { useLocaleStore } from '@/stores/locale'

const props=defineProps<{open:boolean;product:Product|null}>()
defineEmits<{close:[]}>()
const locale=useLocaleStore()
const title=computed(()=>props.product?locale.productName(props.product.code,props.product.name):locale.t('productSpecs'))
const availability=computed(()=>props.product?.availability==='available'?locale.t('available'):props.product?.availability==='unavailable'?locale.t('unavailable'):locale.t('madeToOrder'))
</script>

<template>
  <BaseSheet :open="open" :title="title" @close="$emit('close')">
    <template v-if="product">
      <div class="mb-4 flex flex-wrap gap-2 text-xs">
        <span class="rounded-full bg-[var(--c-surface-2)] px-2.5 py-1">{{locale.t('codeLabel')}} {{product.code}}</span>
        <span class="rounded-full bg-[color-mix(in_srgb,var(--c-secondary)_10%,var(--c-surface))] px-2.5 py-1 font-bold text-[var(--c-secondary)]">{{availability}}</span>
      </div>
      <div class="mb-4 flex flex-wrap gap-2 text-[11px]">
        <span class="inline-flex items-center gap-1 rounded-full bg-[var(--c-surface-2)] px-2.5 py-1"><LockKeyhole :size="15"/> {{locale.t('locked')}}</span>
        <span class="inline-flex items-center gap-1 rounded-full bg-[color-mix(in_srgb,var(--c-secondary)_8%,var(--c-surface))] px-2.5 py-1 text-[var(--c-secondary)]"><SlidersHorizontal :size="15"/> {{locale.t('negotiable')}}</span>
      </div>
      <div class="grid grid-cols-2 gap-2">
        <article v-for="item in product.specs.locked" :key="'l-'+item" class="spec-tile locked">
          <div class="flex items-center gap-2"><LockKeyhole :size="17"/><b class="text-xs">{{locale.specLabel(item)}}</b></div>
          <small class="mt-1 block text-[10px]">{{locale.t('locked')}}</small>
        </article>
        <article v-for="item in product.specs.negotiable" :key="'n-'+item" class="spec-tile negotiable">
          <div class="flex items-center gap-2"><SlidersHorizontal :size="17"/><b class="text-xs">{{locale.specLabel(item)}}</b></div>
          <small class="mt-1 block text-[10px]">{{locale.t('negotiable')}}</small>
        </article>
      </div>
    </template>
  </BaseSheet>
</template>
