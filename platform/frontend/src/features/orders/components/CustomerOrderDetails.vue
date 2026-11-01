<script setup lang="ts">
import OrderProgress from './OrderProgress.vue'
import OrderCommercialPanel from './OrderCommercialPanel.vue'
import type {TrackedOrder} from '../services/trackingApi'
defineProps<{order:TrackedOrder}>()
</script>
<template><div class="space-y-3"><OrderProgress :order="order"/><OrderCommercialPanel :order="order"/></div></template>
