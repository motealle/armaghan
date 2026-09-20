<script setup lang="ts">
import { computed } from 'vue'

const props=withDefaults(defineProps<{
  src?:string
  alt:string
  label?:string
  aspect?:'hero'|'card'|'square'
  eager?:boolean
}>(),{label:'تصویر محصول',aspect:'card',eager:false})

const ratioClass=computed(()=>props.aspect==='hero'?'aspect-[16/9]':props.aspect==='square'?'aspect-square':'aspect-[4/3]')
</script>

<template>
  <div class="smart-image relative overflow-hidden" :class="ratioClass" role="img" :aria-label="alt || label">
    <div class="absolute inset-0 bg-gradient-to-br from-[color-mix(in_srgb,var(--c-soft)_72%,var(--c-surface))] via-[var(--c-surface-2)] to-[color-mix(in_srgb,var(--c-primary)_8%,var(--c-surface))]" />
    <div class="absolute -start-10 -top-10 h-28 w-28 rounded-full bg-white/30 dark:bg-white/5" />
    <div class="absolute -bottom-12 -end-7 h-36 w-36 rounded-full bg-[color-mix(in_srgb,var(--c-primary)_8%,transparent)]" />
    <div class="absolute inset-0 grid place-items-center">
      <div class="relative flex flex-col items-center gap-3 text-center text-[var(--c-primary)]">
        <svg class="smart-placeholder-svg" viewBox="0 0 220 170" aria-hidden="true">
          <path d="M78 36c10 12 21 18 32 18s22-6 32-18l31 18-18 31-18-10v59H83V75L65 85 47 54l31-18Z" fill="currentColor" fill-opacity=".10" stroke="currentColor" stroke-width="6" stroke-linejoin="round"/>
          <path d="M91 43c4 8 10 12 19 12s15-4 19-12" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
        </svg>
        <span class="max-w-36 text-[10px] font-black text-[var(--c-muted)]">{{label}}</span>
      </div>
    </div>
  </div>
</template>
