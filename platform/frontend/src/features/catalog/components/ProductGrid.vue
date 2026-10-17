<script setup lang="ts">
import { ref, computed, watch, onBeforeUnmount } from 'vue'
import type { AdminProduct } from '@/features/admin/services/adminApi'
import type { Product } from '@/types/domain'
import ProductQuickOrderFeedback from '@/features/admin/components/ProductQuickOrderFeedback.vue'
import ProductCard from './ProductCard.vue'
import ProductDetailSheet from './ProductDetailSheet.vue'
import WhatsAppSheet from './WhatsAppSheet.vue'

const props=withDefaults(defineProps<{ products: Product[]; adminEditable?: boolean;adminOrderable?:boolean }>(),{adminEditable:false,adminOrderable:false})
const emit=defineEmits<{edit:[product:Product];'specifications-saved':[id:number,product:AdminProduct];'detail-open':[open:boolean]}>()
const detail = ref<Product | null>(null)
watch(detail,value=>emit('detail-open',Boolean(value)))
onBeforeUnmount(()=>emit('detail-open',false))
const selectedDetail=computed(()=>props.products.find(p=>p.id===detail.value?.id)??detail.value)
const whatsapp = ref<Product | null>(null)
</script>

<template>
  <ProductQuickOrderFeedback v-if="adminOrderable"/>
  <div class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4">
    <ProductCard
      v-for="product in products"
      :key="product.id"
      :product="product"
      :admin-editable="adminEditable"
      :admin-orderable="adminOrderable"
      @detail="detail = $event"
      @whatsapp="whatsapp = $event"
      @edit="emit('edit',$event)"
    />
  </div>
  <ProductDetailSheet :open="!!detail" :product="selectedDetail" :admin-editable="adminEditable" @saved="emit('specifications-saved',detail!.id,$event)" @close="detail = null" />
  <WhatsAppSheet :open="!!whatsapp" :product="whatsapp" @close="whatsapp = null" />
</template>
