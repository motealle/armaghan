<script setup lang="ts">
import { ref } from 'vue'
import type { Product } from '@/types/domain'
import ProductCard from './ProductCard.vue'
import ProductDetailSheet from './ProductDetailSheet.vue'
import WhatsAppSheet from './WhatsAppSheet.vue'

defineProps<{ products: Product[] }>()
const detail = ref<Product | null>(null)
const whatsapp = ref<Product | null>(null)
</script>

<template>
  <div class="grid grid-cols-2 gap-3 md:grid-cols-3">
    <ProductCard
      v-for="product in products"
      :key="product.id"
      :product="product"
      @detail="detail = $event"
      @whatsapp="whatsapp = $event"
    />
  </div>
  <ProductDetailSheet :open="!!detail" :product="detail" @close="detail = null" />
  <WhatsAppSheet :open="!!whatsapp" :product="whatsapp" @close="whatsapp = null" />
</template>
