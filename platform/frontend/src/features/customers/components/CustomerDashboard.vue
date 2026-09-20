<script setup lang="ts">
import { Check, Clock3, Heart, RotateCcw } from '@lucide/vue'
import { useLocaleStore } from '@/stores/locale'
const locale=useLocaleStore()
const timelineKeys=['inquiryRegistered','specsApproved','prepaymentApproved','inProduction']
</script>
<template>
  <div class="grid gap-4 md:grid-cols-2">
    <article class="rounded-2xl border border-[var(--c-border)] bg-[var(--c-surface)] p-4 shadow-sm">
      <div class="flex items-center justify-between"><h2 class="font-black text-[var(--c-text)]">{{locale.t('timeline')}}</h2><span class="rounded-full bg-[color-mix(in_srgb,var(--c-secondary)_10%,var(--c-surface))] px-2 py-1 text-[10px] font-bold text-[var(--c-secondary)]">{{locale.t('active')}}</span></div>
      <div class="mt-4 space-y-4">
        <div v-for="(key,index) in timelineKeys" :key="key" class="flex items-center gap-3">
          <span class="grid h-8 w-8 place-items-center rounded-full" :class="index<3?'bg-[color-mix(in_srgb,var(--c-secondary)_12%,var(--c-surface))] text-[var(--c-secondary)]':'bg-[var(--c-surface-2)] text-[var(--c-muted)]'"><Check v-if="index<3" :size="17"/><Clock3 v-else :size="17"/></span>
          <span class="text-sm text-[var(--c-text)]">{{locale.t(key)}}</span>
        </div>
      </div>
    </article>
    <article class="rounded-2xl border border-[var(--c-border)] bg-[var(--c-surface)] p-4 shadow-sm">
      <h2 class="font-black text-[var(--c-text)]">{{locale.t('quickAccess')}}</h2>
      <div class="mt-3 grid gap-2">
        <button class="quick-account"><RotateCcw :size="18"/>{{locale.t('repeatOrder')}}</button>
        <RouterLink to="/favorites" class="quick-account"><Heart :size="18"/>{{locale.t('favorites')}}</RouterLink>
        <button class="quick-account"><Check :size="18"/>{{locale.t('approvePrepayment')}}</button>
      </div>
    </article>
  </div>
</template>
