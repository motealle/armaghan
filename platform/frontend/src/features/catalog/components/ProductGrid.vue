<script setup lang="ts">
import { ref } from 'vue'
import type { Product } from '@/types/domain'
import ProductCard from './ProductCard.vue'
import ProductDetailSheet from './ProductDetailSheet.vue'
import WhatsAppSheet from './WhatsAppSheet.vue'

withDefaults(defineProps<{ products: Product[]; adminEditable?: boolean }>(),{adminEditable:false})
const emit=defineEmits<{edit:[product:Product]}>()
const detail = ref<Product | null>(null)
const whatsapp = ref<Product | null>(null)
</script>

<template>
  <div class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4">
    <ProductCard
      v-for="product in products"
      :key="product.id"
      :product="product"
      :admin-editable="adminEditable"
      @detail="detail = $event"
      @whatsapp="whatsapp = $event"
      @edit="emit('edit',$event)"
    />
  </div>
  <ProductDetailSheet :open="!!detail" :product="detail" @close="detail = null" />
  <WhatsAppSheet :open="!!whatsapp" :product="whatsapp" @close="whatsapp = null" />
</template>
