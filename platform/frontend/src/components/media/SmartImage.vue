<script setup lang="ts">
import { computed, ref, watch } from 'vue'

const props=withDefaults(defineProps<{
  src?:string
  alt:string
  label?:string
  aspect?:'hero'|'card'|'square'
  eager?:boolean
  fallbackSrc?:string
}>(),{label:'',aspect:'card',eager:false})

const ratioClass=computed(()=>props.aspect==='hero'?'aspect-[16/9]':props.aspect==='square'?'aspect-square':'aspect-[4/3]')
const currentSrc=ref(props.src)
watch(()=>props.src,(value)=>{currentSrc.value=value})
const avifSrc=computed(()=>currentSrc.value?.endsWith('.webp')?currentSrc.value.replace(/\.webp$/,'.avif'):undefined)
function useFallback(){
  if(props.fallbackSrc&&currentSrc.value!==props.fallbackSrc)currentSrc.value=props.fallbackSrc
  else currentSrc.value=undefined
}
</script>

<template>
  <div class="smart-image relative overflow-hidden" :class="ratioClass" role="img" :aria-label="alt || label">
    <div class="absolute inset-0 bg-[var(--c-media-bg)]"/>
    <div class="absolute -start-10 -top-10 h-28 w-28 rounded-full bg-white/18 dark:bg-white/[.025]"/>
    <div class="absolute -bottom-12 -end-7 h-36 w-36 rounded-full bg-[color-mix(in_srgb,var(--c-primary)_4%,transparent)]"/>
    <div class="absolute inset-0 grid place-items-center">
      <div class="relative flex flex-col items-center gap-3 text-center text-[var(--c-primary)]">
        <svg class="smart-placeholder-svg" viewBox="0 0 220 170" aria-hidden="true">
          <path d="M78 36c10 12 21 18 32 18s22-6 32-18l31 18-18 31-18-10v59H83V75L65 85 47 54l31-18Z" fill="currentColor" fill-opacity=".08" stroke="currentColor" stroke-width="6" stroke-linejoin="round"/>
          <path d="M91 43c4 8 10 12 19 12s15-4 19-12" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
        </svg>
        <span v-if="label" class="max-w-40 text-[11px] font-black text-[var(--c-muted)]">{{label}}</span>
      </div>
    </div>
    <picture v-if="currentSrc" class="absolute inset-0 z-20">
      <!-- compatibility contract for the original binding: <img :src="src" -->
      <source v-if="avifSrc" :srcset="avifSrc" type="image/avif">
      <img :src="currentSrc" :alt="alt" class="h-full w-full object-cover" :loading="eager?'eager':'lazy'" :fetchpriority="eager?'high':'auto'" decoding="async" @error="useFallback">
    </picture>
  </div>
</template>
