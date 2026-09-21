<script setup lang="ts">
import OrderWizard from '@/features/orders/components/OrderWizard.vue'
import { useLocaleStore } from '@/stores/locale'
const locale=useLocaleStore()
</script>
<template>
  <section>
    <div class="mb-4"><h1 class="text-[1.75rem] font-black leading-tight text-[var(--c-text)]">{{locale.t('productionTitle')}}</h1><p class="mt-1 text-sm leading-6 text-[var(--c-muted)]">{{locale.t('productionHelp')}}</p></div>
    <OrderWizard/>
  </section>
</template>
