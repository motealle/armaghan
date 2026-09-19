<script setup lang="ts">
import OrderWizard from '@/features/orders/components/OrderWizard.vue'
import { useLocaleStore } from '@/stores/locale'
const locale=useLocaleStore()
</script>
<template><section><div class="mb-4"><h1 class="text-2xl font-black">{{locale.t('productionTitle')}}</h1><p class="mt-1 text-sm text-slate-500">{{locale.t('productionHelp')}}</p></div><OrderWizard/></section></template>
