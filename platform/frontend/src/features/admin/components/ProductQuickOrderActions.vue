<script setup lang="ts">
import { computed } from 'vue'
import ProductMoveButtons from './ProductMoveButtons.vue'
import { useProductQuickOrder } from '../services/productQuickOrder'
import { useAdminStore } from '../store'
import type { OrderMove } from '../services/productOrder'
const props=withDefaults(defineProps<{code:string;disabled?:boolean}>(),{disabled:false})
const emit=defineEmits<{moved:[]}>()
const admin=useAdminStore(),order=useProductQuickOrder()
const position=computed(()=>order.locate(props.code))
async function move(direction:OrderMove){if(await order.move(props.code,direction))emit('moved')}
</script>
<template>
 <ProductMoveButtons v-if="admin.identity" :first="!position||position.index===0" :last="!position||position.index===position.rows.length-1" :disabled="disabled||order.busy||!!order.error||!position" @move="move"/>
</template>
