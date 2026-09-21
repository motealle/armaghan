<script setup lang="ts">
import { computed } from 'vue'
import type { Product } from '@/types/domain'
import { useLocaleStore } from '@/stores/locale'

const props=defineProps<{product:Product}>()
const locale=useLocaleStore()
const tone=computed(()=>{
  if(props.product.subcategoryCode.startsWith('1'))return 'baby'
  if(props.product.subcategoryCode.startsWith('2'))return 'kids'
  return 'women'
})
const productName=computed(()=>locale.productName(props.product.code,props.product.name,props.product.names))
const subcategoryName=computed(()=>locale.subcategoryName(props.product.subcategoryCode,props.product.subcategoryName))
</script>

<template>
  <div class="product-media-placeholder relative aspect-[4/3] overflow-hidden" :data-tone="tone" role="img" :aria-label="productName">
    <div class="placeholder-orb placeholder-orb-a"/>
    <div class="placeholder-orb placeholder-orb-b"/>
    <svg class="product-placeholder-svg" viewBox="0 0 220 170" aria-hidden="true">
      <defs>
        <linearGradient :id="`garmentFill-${product.id}`" x1="0" x2="1" y1="0" y2="1">
          <stop offset="0" stop-color="currentColor" stop-opacity=".18"/>
          <stop offset="1" stop-color="currentColor" stop-opacity=".06"/>
        </linearGradient>
      </defs>
      <path d="M78 36c10 12 21 18 32 18s22-6 32-18l31 18-18 31-18-10v59H83V75L65 85 47 54l31-18Z" :fill="`url(#garmentFill-${product.id})`" stroke="currentColor" stroke-width="6" stroke-linejoin="round"/>
      <path d="M91 43c4 8 10 12 19 12s15-4 19-12" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
    </svg>
    <div class="absolute inset-x-0 bottom-0 z-10 border-t border-white/25 bg-[color-mix(in_srgb,var(--c-surface)_74%,transparent)] px-3 py-2 text-[11px] font-bold text-[var(--c-muted)] backdrop-blur-md">
      {{product.subcategoryCode}} · {{subcategoryName}}
    </div>
  </div>
</template>
