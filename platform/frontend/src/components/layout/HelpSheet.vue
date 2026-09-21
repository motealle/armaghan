<script setup lang="ts">
import { CircleCheckBig, Heart, MessageCircleMore, Search } from '@lucide/vue'
import BaseSheet from '@/components/ui/BaseSheet.vue'
import { useLocaleStore } from '@/stores/locale'

defineProps<{open:boolean}>()
const emit=defineEmits<{close:[]}>()
const locale=useLocaleStore()
const steps=[
  {key:'helpStep1',icon:Search},
  {key:'helpStep2',icon:Heart},
  {key:'helpStep3',icon:MessageCircleMore},
]
</script>

<template>
  <BaseSheet :open="open" :title="locale.t('helpTitle')" @close="emit('close')">
    <div class="space-y-4">
      <p class="text-sm leading-7 text-[var(--c-muted)]">{{locale.t('helpText')}}</p>
      <div class="grid gap-2">
        <div v-for="item in steps" :key="item.key" class="help-step">
          <span class="help-step-icon"><component :is="item.icon" :size="19"/></span>
          <span>{{locale.t(item.key)}}</span>
        </div>
      </div>
      <div class="rounded-xl border border-[var(--c-border)] bg-[var(--c-surface-2)] p-3 text-xs leading-6 text-[var(--c-muted)]">
        <CircleCheckBig :size="17" class="mb-2 text-[var(--c-secondary)]"/>
        {{locale.t('brandSalesText')}}
      </div>
    </div>
  </BaseSheet>
</template>
