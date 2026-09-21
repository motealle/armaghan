<script setup lang="ts">
import { computed } from 'vue'
import type { Product } from '@/types/domain'
import SmartImage from '@/components/media/SmartImage.vue'
import { productPlaceholder } from '@/data/productPlaceholders'
import { useDesignStore } from '@/stores/design'
import { useLocaleStore } from '@/stores/locale'

const props=defineProps<{product:Product}>()
const locale=useLocaleStore()
const design=useDesignStore()
const productName=computed(()=>locale.productName(props.product.code,props.product.name,props.product.names))
const subcategoryName=computed(()=>locale.subcategoryName(props.product.subcategoryCode,props.product.subcategoryName))
const fallbackImage=computed(()=>productPlaceholder(design.placeholderSet,props.product.subcategoryCode))
const productImage=computed(()=>props.product.image?.trim()||fallbackImage.value)
</script>

<template>
  <div class="product-media-placeholder relative overflow-hidden">
    <svg class="product-placeholder-svg hidden" viewBox="0 0 1 1" aria-hidden="true"><path d="M0 0h1v1H0z"/></svg>
    <SmartImage :src="productImage" :fallback-src="fallbackImage" :alt="productName" aspect="card"/>
    <div class="absolute inset-x-0 bottom-0 z-10 border-t border-white/25 bg-[color-mix(in_srgb,var(--c-surface)_74%,transparent)] px-3 py-2 text-[11px] font-bold text-[var(--c-muted)] backdrop-blur-md">
      {{product.subcategoryCode}} · {{subcategoryName}}
    </div>
  </div>
</template>
