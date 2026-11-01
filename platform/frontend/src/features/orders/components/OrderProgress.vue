<script setup lang="ts">
import {useLocaleStore} from '@/stores/locale'
import {orderStages,stageLabels,type TrackedOrder} from '../services/trackingApi'
defineProps<{order:TrackedOrder;admin?:boolean}>()
const locale=useLocaleStore()
function label(value:string){return stageLabels[locale.locale][orderStages.indexOf(value as typeof orderStages[number])]??value}
const privateLabel={fa:'یادداشت داخلی',ar:'ملاحظة داخلية',ku:'تێبینی ناوخۆیی',en:'Internal note'}
</script>
<template><div class="space-y-3">
      <ol class="grid grid-cols-2 gap-2 sm:grid-cols-3"><li v-for="(item,index) in orderStages.slice(0,9)" :key="item" class="rounded-xl border border-[var(--c-border)] p-2 text-xs" :class="{'font-bold text-[var(--c-secondary)]':(item===order.stage||order.events.some(event=>event.stage===item))&&order.stage!=='cancelled'}">{{index+1}} · {{label(item)}} <span v-if="item===order.stage">●</span></li></ol>
      <ol class="space-y-2 border-s-2 border-[var(--c-secondary)] ps-3"><li v-for="event in order.events" :key="event.id" class="rounded-xl bg-[var(--c-surface-2)] p-3 text-sm"><b>{{label(event.stage)}}</b><time class="mx-2 text-xs">{{new Date(event.created_at).toLocaleString(locale.locale==='ku'?'ar':locale.locale)}}</time><small v-if="admin&&!event.visible_to_customer">{{privateLabel[locale.locale]}}</small><p class="whitespace-pre-wrap">{{event.note}}</p></li></ol>
</div></template>
