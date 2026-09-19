<script setup lang="ts">
import { Heart, MessageCircle, SlidersHorizontal } from '@lucide/vue'
import type { Product } from '@/types/domain'
import { useFavoritesStore } from '@/stores/favorites'
import SmartImage from '@/components/media/SmartImage.vue'

const props = defineProps<{ product: Product }>()
const emit = defineEmits<{ detail: [product: Product]; whatsapp: [product: Product] }>()
const favorites = useFavoritesStore()
</script>

<template>
  <article class="product-card overflow-hidden rounded-2xl border border-slate-200 bg-[var(--c-paper)] shadow-sm">
    <div class="relative">
      <SmartImage :src="product.image" :alt="product.name" :label="product.subcategoryName" />
      <span class="absolute end-2 top-2 z-30 rounded-full bg-white/92 px-2 py-1 text-[10px] font-extrabold text-emerald-700 shadow-sm">
        {{ product.availability === 'available' ? 'موجود' : 'ناموجود' }}
      </span>
    </div>
    <div class="p-3">
      <div class="min-h-11 text-sm font-extrabold leading-6">{{ product.name }}</div>
      <div class="mt-1 flex items-center justify-between gap-2 text-[10px] text-slate-500">
        <span class="truncate">{{ product.subcategoryCode }} · {{ product.subcategoryName }}</span>
        <code class="shrink-0 rounded-md bg-slate-100 px-1.5 py-1 font-sans text-[var(--c-primary)]">{{ product.code }}</code>
      </div>
      <div class="mt-3 grid grid-cols-3 gap-1.5">
        <button class="wa-card-action" aria-label="واتساپ" @click="emit('whatsapp', product)">
          <MessageCircle :size="19" /><span>واتساپ</span>
        </button>
        <button class="card-action" :class="{ 'is-favorite': favorites.has(product.id) }" aria-label="مطلوب‌ها" @click="favorites.toggle(product.id)">
          <Heart :size="19" :fill="favorites.has(product.id) ? 'currentColor' : 'none'" /><span>مطلوب</span>
        </button>
        <button class="card-action detail-action" aria-label="مشخصات" @click="emit('detail', product)">
          <SlidersHorizontal :size="19" /><span>مشخصات</span>
        </button>
      </div>
    </div>
  </article>
</template>
