<script setup lang="ts">
import { computed } from 'vue'
import type { Product } from '@/types/domain'

const props=defineProps<{product:Product}>()
const tone=computed(()=>{
  if(props.product.subcategoryCode.startsWith('1'))return 'baby'
  if(props.product.subcategoryCode.startsWith('2'))return 'kids'
  return 'women'
})
</script>

<template>
  <div class="product-media-placeholder relative aspect-[4/3] overflow-hidden" :data-tone="tone" role="img" :aria-label="`${product.name} — تصویر نمونه`">
    <div class="placeholder-orb placeholder-orb-a" />
    <div class="placeholder-orb placeholder-orb-b" />
    <svg class="product-placeholder-svg" viewBox="0 0 220 170" aria-hidden="true">
      <defs>
        <linearGradient id="garmentFill" x1="0" x2="1" y1="0" y2="1">
          <stop offset="0" stop-color="currentColor" stop-opacity=".18"/>
          <stop offset="1" stop-color="currentColor" stop-opacity=".06"/>
        </linearGradient>
      </defs>
      <path
        d="M78 36c10 12 21 18 32 18s22-6 32-18l31 18-18 31-18-10v59H83V75L65 85 47 54l31-18Z"
        fill="url(#garmentFill)"
        stroke="currentColor"
        stroke-width="6"
        stroke-linejoin="round"
      />
      <path d="M91 43c4 8 10 12 19 12s15-4 19-12" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
    </svg>
    <div class="absolute inset-x-0 bottom-0 z-10 flex items-center justify-between gap-2 border-t border-white/35 bg-white/48 px-3 py-2 text-[10px] font-bold text-slate-600 backdrop-blur-md dark:border-white/10 dark:bg-slate-950/28 dark:text-slate-300">
      <span>{{product.subcategoryCode}} · {{product.subcategoryName}}</span>
      <span class="rounded-full bg-white/72 px-2 py-1 text-[9px] text-[var(--c-primary)] dark:bg-slate-950/35">SVG</span>
    </div>
  </div>
</template>
