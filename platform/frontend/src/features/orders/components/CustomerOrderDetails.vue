<script setup lang="ts">
import OrderProgress from './OrderProgress.vue'
import OrderCommercialPanel from './OrderCommercialPanel.vue'
import {useLocaleStore} from '@/stores/locale'
import type {TrackedOrder} from '../services/trackingApi'
defineProps<{order:TrackedOrder}>()
const locale=useLocaleStore()
const labels={fa:['تأیید فاکتور','تأیید پیش‌پرداخت'],ar:['تأكيد الفاتورة','تأكيد الدفعة'],ku:['پشتڕاستکردنەوەی فاکتۆر','پشتڕاستکردنەوەی پێشەکی'],en:['Invoice confirmed','Deposit confirmed']}
function date(value:string|null){return value?'✓ '+new Date(value).toLocaleDateString(locale.locale==='ku'?'ar':locale.locale):'—'}
</script>
<template><div class="space-y-3"><p class="text-xs">{{labels[locale.locale][0]}}: {{date(order.invoice_confirmed_at)}} · {{labels[locale.locale][1]}}: {{date(order.deposit_confirmed_at)}}</p><OrderProgress :order="order"/><OrderCommercialPanel :order="order"/></div></template>
