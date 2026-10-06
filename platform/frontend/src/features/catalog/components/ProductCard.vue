<script setup lang="ts">
import { ArrowDownRight, Heart, Menu, Pencil } from '@lucide/vue'
import { computed } from 'vue'
import type { Product } from '@/types/domain'
import { useFavoritesStore } from '@/stores/favorites'
import { useLocaleStore } from '@/stores/locale'
import WhatsAppIcon from '@/components/icons/WhatsAppIcon.vue'
import ProductMediaCarousel from './ProductMediaCarousel.vue'
import { customerProductLabel } from '../presentation'

const props=withDefaults(defineProps<{product:Product;adminEditable?:boolean}>(),{adminEditable:false})
const emit=defineEmits<{detail:[product:Product];whatsapp:[product:Product];edit:[product:Product]}>()
const favorites=useFavoritesStore()
const locale=useLocaleStore()
const displayCode=computed(()=>locale.locale==='fa'?props.product.code.replace(/[0-9]/g,digit=>'۰۱۲۳۴۵۶۷۸۹'[Number(digit)]!):props.product.code)
const isFavorite=computed(()=>favorites.has(props.product.id))
const displaySubcategory=computed(()=>locale.subcategoryName(props.product.subcategoryCode,props.product.subcategoryName))
const visibleTitle=computed(()=>customerProductLabel(props.product,displaySubcategory.value,locale.t('unavailableOrProducible')))
</script>

<template>
  <!-- Test 23 low-copy hierarchy stays intact; Test 27 derives customer labels centrally from subcategory/status. -->
  <article data-style-id="product.card" data-style-label="همه کارت‌های محصول" class="product-card relative overflow-hidden rounded-2xl border border-[var(--c-border)] bg-[var(--role-product-card-background)] shadow-sm">
    <div data-style-id="product.media" data-style-label="بخش تصویر کارت محصول" class="product-card-media">
      <ProductMediaCarousel :product="product" />
    </div>
    <div class="product-card-body">
      <div data-style-id="product.title" data-style-label="عنوان کارت محصول" class="product-card-title" :class="{unavailable:product.availability!=='available'}">{{visibleTitle}}</div>
      <div data-style-id="product.code" data-style-label="کد محصول" class="product-code-row flex items-center gap-2">
        <code class="product-code-pill" :dir="locale.direction"><span>{{locale.t('codeLabel')}}</span> <bdi dir="ltr">{{displayCode}}</bdi></code>
        <button v-if="adminEditable" type="button" class="ms-auto grid h-9 w-9 place-items-center rounded-xl border border-[var(--c-border)] bg-[var(--c-surface)] text-[var(--c-primary)] shadow-sm" :aria-label="locale.t('editProduct')" @click.stop="emit('edit',product)">
          <Pencil :size="16"/>
        </button>
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
