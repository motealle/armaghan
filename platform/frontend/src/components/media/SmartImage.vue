<script setup lang="ts">
import { computed, onMounted, onBeforeUnmount, ref, watch, type CSSProperties } from 'vue'
import { whenNearViewport } from './nearViewport'
import { createImageRecovery } from './imageRecovery'

const props=withDefaults(defineProps<{
  src?:string
  srcset?:string
  sizes?:string
  alt:string
  label?:string
  aspect?:'hero'|'card'|'square'|'product'
  fit?:'cover'|'contain'|'contain-blur'|'edge-extend'
  intrinsic?:boolean
  eager?:boolean
  fallbackSrc?:string
  preloadNear?:boolean
}>(),{label:'',aspect:'card',fit:'cover',intrinsic:false,eager:false})

const ratioClass=computed(()=>props.aspect==='hero'?'aspect-[16/9]':props.aspect==='square'?'aspect-square':props.aspect==='product'?'aspect-[2/3]':'aspect-[4/3]')
const currentSrc=ref(props.src)
const container=ref<HTMLElement>()
const ready=ref(!props.preloadNear||props.eager)
const loaded=ref(false)
let stopWatching:undefined|(()=>void)
const recovery=createImageRecovery(value=>{currentSrc.value=value})
onMounted(()=>{
  if(!ready.value&&container.value)stopWatching=whenNearViewport(container.value,()=>{ready.value=true})
})
onBeforeUnmount(()=>{stopWatching?.();recovery.cancel()})
const intrinsicRatio=ref('16 / 9')
watch(currentSrc,()=>{intrinsicRatio.value='16 / 9';loaded.value=false})
function imageLoaded(event:Event){
  recovery.cancel()
  loaded.value=true
  const image=event.target as HTMLImageElement
  if(props.intrinsic&&image.naturalWidth>0&&image.naturalHeight>0)intrinsicRatio.value=`${image.naturalWidth} / ${image.naturalHeight}`
}
watch(()=>props.src,(value)=>{recovery.reset();currentSrc.value=value})
const KNOWN_AVIF_PREFIXES=[
  '/images/final/',
  '/images/placeholders/dimensional/',
  '/images/placeholders/flat-geometric/',
  '/images/placeholders/paper-cut/',
] as const
const avifSrc=computed(()=>{
  const value=currentSrc.value
  if(!value?.endsWith('.webp')||value.includes('/images/placeholders-portrait/')||value.includes('/images/category-navigation/')||!KNOWN_AVIF_PREFIXES.some(prefix=>value.includes(prefix)))return undefined
  return value.replace(/\.webp$/,'.avif')
})
const imageClass=computed(()=>props.fit==='cover'?'object-cover':'object-contain')
const hasAmbientBackdrop=computed(()=>props.fit==='contain-blur'||props.fit==='edge-extend')
const backdropStyle=computed(()=>currentSrc.value?{backgroundImage:`url("${currentSrc.value}")`,backgroundPosition:'var(--editor-media-position, 50% 50%)'}:undefined)
const contentStyle=computed<CSSProperties>(()=>({
  objectFit:`var(--editor-media-fit, ${props.fit==='cover'?'cover':'contain'})` as CSSProperties['objectFit'],
  objectPosition:'var(--editor-media-position, 50% 50%)',
}))
function useFallback(){
  if(currentSrc.value!==props.fallbackSrc&&props.src&&recovery.failed(props.src))return
  if(props.fallbackSrc&&currentSrc.value!==props.fallbackSrc)currentSrc.value=props.fallbackSrc
  else currentSrc.value=undefined
}
</script>

<template>
  <div ref="container" class="smart-image relative overflow-hidden" :class="ratioClass" :style="intrinsic?{aspectRatio:intrinsicRatio}:undefined" role="img" :aria-label="alt || label">
    <div class="absolute inset-0 bg-[var(--c-media-bg)]"/>
    <div class="absolute -start-10 -top-10 h-28 w-28 rounded-full bg-white/18 dark:bg-white/[.025]"/>
    <div class="absolute -bottom-12 -end-7 h-36 w-36 rounded-full bg-[color-mix(in_srgb,var(--c-primary)_4%,transparent)]"/>
    <div v-if="hasAmbientBackdrop&&currentSrc&&ready&&loaded" class="smart-image-backdrop" :class="{'edge-extend-backdrop':fit==='edge-extend'}" :style="backdropStyle" aria-hidden="true"/>
    <img v-if="fallbackSrc&&ready&&!loaded&&currentSrc!==fallbackSrc" :src="fallbackSrc" alt="" aria-hidden="true" class="smart-image-loading-fallback absolute inset-0 h-full w-full object-contain" decoding="async">
    <div v-if="!fallbackSrc" class="absolute inset-0 grid place-items-center">
      <div class="relative flex flex-col items-center gap-3 text-center text-[var(--c-primary)]">
        <svg class="smart-placeholder-svg" viewBox="0 0 220 170" aria-hidden="true">
          <path d="M78 36c10 12 21 18 32 18s22-6 32-18l31 18-18 31-18-10v59H83V75L65 85 47 54l31-18Z" fill="currentColor" fill-opacity=".08" stroke="currentColor" stroke-width="6" stroke-linejoin="round"/>
          <path d="M91 43c4 8 10 12 19 12s15-4 19-12" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
        </svg>
        <span v-if="label" class="max-w-40 text-[11px] font-black text-[var(--c-muted)]">{{label}}</span>
      </div>
    </div>
    <picture v-if="currentSrc&&ready" class="absolute inset-0 z-20" :style="{opacity:loaded||currentSrc===fallbackSrc?1:0}">
      <!-- compatibility contract: <img :src="src" -->
      <source v-if="avifSrc" :srcset="avifSrc" type="image/avif">
      <img
        :key="currentSrc"
        :src="currentSrc"
        :srcset="currentSrc===src?srcset:undefined"
        :sizes="currentSrc===src?sizes:undefined"
        :alt="alt"
        class="smart-image-content h-full w-full"
        :class="[imageClass,{'smart-image-contained':hasAmbientBackdrop,'smart-image-edge-extend':fit==='edge-extend'}]"
        :style="contentStyle"
        :loading="eager||preloadNear?'eager':'lazy'"
        :fetchpriority="eager?'high':'auto'"
        decoding="async"
        @load="imageLoaded"
        @error="useFallback"
      >
    </picture>
    <div v-if="fit==='edge-extend'" class="smart-image-vignette pointer-events-none absolute inset-0 z-30" aria-hidden="true"/>
  </div>
</template>
