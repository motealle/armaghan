<script setup lang="ts">
import { Heart, SlidersHorizontal } from '@lucide/vue'
import { computed } from 'vue'
import type { Product } from '@/types/domain'
import { useFavoritesStore } from '@/stores/favorites'
import { useLocaleStore } from '@/stores/locale'
import WhatsAppIcon from '@/components/icons/WhatsAppIcon.vue'
import ProductMediaCarousel from './ProductMediaCarousel.vue'

const props=defineProps<{product:Product}>()
const emit=defineEmits<{detail:[product:Product];whatsapp:[product:Product]}>()
const favorites=useFavoritesStore()
const locale=useLocaleStore()
const isFavorite=computed(()=>favorites.has(props.product.id))
const availabilityLabel=computed(()=>props.product.availability==='available'?locale.t('available'):props.product.availability==='unavailable'?locale.t('unavailable'):locale.t('madeToOrder'))
const availabilityClass=computed(()=>props.product.availability==='available'?'text-emerald-700':props.product.availability==='unavailable'?'text-rose-700':'text-amber-800')
</script>

<template>
  <article class="product-card overflow-hidden rounded-2xl border border-slate-200 bg-[var(--c-paper)] shadow-sm">
    <div class="relative">
      <ProductMediaCarousel :product="product" />
      <span class="absolute end-2 top-2 z-30 rounded-full bg-white/92 px-2 py-1 text-[10px] font-extrabold shadow-sm" :class="availabilityClass">
        {{availabilityLabel}}
      </span>
    </div>
    <div class="p-3">
      <div class="min-h-11 text-sm font-extrabold leading-6">{{product.name}}</div>
      <div class="mt-1 flex items-center justify-between gap-2 text-[10px] text-slate-500">
        <span class="truncate">{{product.subcategoryCode}} · {{product.subcategoryName}}</span>
        <code class="shrink-0 rounded-md bg-slate-100 px-1.5 py-1 font-sans text-[var(--c-primary)]">{{product.code}}</code>
      </div>
      <div class="mt-3 grid grid-cols-3 gap-1.5">
        <button class="wa-card-action" :aria-label="locale.t('whatsapp')" @click="emit('whatsapp',product)">
          <span class="wa-logo-chip"><WhatsAppIcon :size="22"/></span>
          <span>{{locale.t('whatsapp')}}</span>
        </button>
        <button class="card-action favorite-action" :aria-pressed="isFavorite" :aria-label="locale.t('favorite')" @click="favorites.toggle(product.id)">
          <Heart
            :size="20"
            class="favorite-heart"
            :class="{active:isFavorite}"
            :fill="isFavorite?'currentColor':'none'"
          />
          <span>{{locale.t('favorite')}}</span>
        </button>
        <button class="card-action detail-action" :aria-label="locale.t('details')" @click="emit('detail',product)">
          <SlidersHorizontal :size="20" class="text-[var(--c-primary)]"/>
          <span>{{locale.t('details')}}</span>
        </button>
      </div>
    </div>
  </article>
</template>
