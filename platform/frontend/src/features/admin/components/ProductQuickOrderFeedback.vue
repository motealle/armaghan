<script setup lang="ts">
import { RefreshCw } from '@lucide/vue'
import { useProductQuickOrder } from '../services/productQuickOrder'
import { useLocaleStore } from '@/stores/locale'
const emit=defineEmits<{reloaded:[]}>()
async function reload(){await order.reload();emit('reloaded')}
const order=useProductQuickOrder(),locale=useLocaleStore()
</script>
<template>
 <div v-if="order.busy||order.error||order.note" class="mb-2 flex flex-wrap items-center gap-2 text-xs leading-6" :aria-busy="order.busy">
  <p v-if="order.error" role="alert" class="auth-error">{{locale.t(order.error)}}</p>
  <p v-else role="status">{{locale.t(order.busy?'adminLoading':order.note)}}</p>
  <button v-if="order.error" type="button" class="mini-action" :disabled="order.busy" @click="reload"><RefreshCw :size="15"/>{{locale.t('adminReload')}}</button>
 </div>
</template>
