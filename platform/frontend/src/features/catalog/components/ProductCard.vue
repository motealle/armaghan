<script setup lang="ts">
import { ArrowDownRight, Heart, Menu } from '@lucide/vue'
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
const displayName=computed(()=>locale.productName(props.product.code,props.product.name,props.product.names))
const visibleTitle=computed(()=>props.product.availability==='available'?displayName.value:locale.t('unavailable'))
</script>

<template>
  <!-- Test 23 low-copy card contract: image-first, code-only metadata, icon-only actions. -->
  <article data-style-id="product.card" data-style-label="همه کارت‌های محصول" class="product-card overflow-hidden rounded-2xl border border-[var(--c-border)] bg-[var(--c-surface)] shadow-sm">
    <div data-style-id="product.media" data-style-label="بخش تصویر کارت محصول" class="product-card-media">
      <ProductMediaCarousel :product="product" />
    </div>
    <div class="product-card-body">
      <div data-style-id="product.title" data-style-label="عنوان کارت محصول" class="product-card-title" :class="{unavailable:product.availability!=='available'}">{{visibleTitle}}</div>
      <div data-style-id="product.code" data-style-label="کد محصول" class="product-code-row">
        <code dir="ltr">{{product.code}}</code>
      </div>
      <div data-style-id="product.actions" data-style-label="دکمه‌های کارت محصول" class="card-actions compact grid grid-cols-3 gap-1.5">
        <button class="wa-card-action rounded-[.75rem]" :aria-label="locale.t('order')" @click="emit('whatsapp',product)">
          <WhatsAppIcon :size="25.3" tone="white"/>
        </button>
        <button class="card-action favorite-action rounded-[.75rem]" :aria-pressed="isFavorite" :aria-label="locale.t('favorite')" @click="favorites.toggle(product.id)">
          <Heart
            :size="17"
            class="favorite-heart"
            :class="{active:isFavorite}"
            :fill="isFavorite?'currentColor':'none'"
          />
        </button>
        <button class="card-action detail-action rounded-[.75rem]" :aria-label="locale.t('details')" @click="emit('detail',product)">
          <span class="detail-menu-icon" aria-hidden="true">
            <Menu :size="18" class="detail-menu-lines"/>
            <ArrowDownRight :size="11" class="detail-menu-arrow"/>
          </span>
        </button>
      </div>
    </div>
  </article>
</template>
