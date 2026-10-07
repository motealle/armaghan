<script setup lang="ts">
import { computed, onMounted, onBeforeUnmount, ref, watch, type CSSProperties } from 'vue'
import { whenNearViewport } from './nearViewport'
import { createImageRecovery } from './imageRecovery'
import { acquirePhotoSlot } from './photoRequestQueue'
import { RotateCw } from '@lucide/vue'

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
  interactiveLoading?:boolean
  loadingLabel?:string
  retryLabel?:string
}>(),{label:'',aspect:'card',fit:'cover',intrinsic:false,eager:false})

const ratioClass=computed(()=>props.aspect==='hero'?'aspect-[16/9]':props.aspect==='square'?'aspect-square':props.aspect==='product'?'aspect-[2/3]':'aspect-[4/3]')
const currentSrc=ref(props.src)
const container=ref<HTMLElement>()
const ready=ref(!props.preloadNear||props.eager)
const loaded=ref(false)
const failed=ref(false)
const hasRealPhoto=computed(()=>Boolean(props.src&&props.src!==props.fallbackSrc))
const photoLoading=computed(()=>props.interactiveLoading&&hasRealPhoto.value&&ready.value&&!loaded.value&&!failed.value)
let stopWatching:undefined|(()=>void)
let mounted=false
let releaseSlot:undefined|(()=>void)
let networkTimer:ReturnType<typeof setTimeout>|undefined
const requestAllowed=ref(!props.src?.includes('/backend/api/catalog/media/'))
function finishRequest(){
  if(networkTimer!==undefined)clearTimeout(networkTimer)
  networkTimer=undefined
  releaseSlot?.();releaseSlot=undefined
}
function schedulePhoto(){
  finishRequest()
  if(!ready.value||!currentSrc.value?.includes('/backend/api/catalog/media/')){requestAllowed.value=true;return}
  requestAllowed.value=false
  releaseSlot=acquirePhotoSlot(()=>{
    requestAllowed.value=true
    networkTimer=setTimeout(useFallback,25000)
  })
}
const recovery=createImageRecovery(value=>{currentSrc.value=value})
onMounted(()=>{
  mounted=true
  if(!ready.value&&container.value)stopWatching=whenNearViewport(container.value,()=>{ready.value=true})
  schedulePhoto()
})
watch([currentSrc,ready],()=>{if(mounted)schedulePhoto()})
onBeforeUnmount(()=>{mounted=false;stopWatching?.();recovery.cancel();finishRequest()})
const intrinsicRatio=ref('16 / 9')
watch(currentSrc,()=>{intrinsicRatio.value='16 / 9';loaded.value=false})
function imageLoaded(event:Event){
  finishRequest()
  recovery.cancel()
  loaded.value=true
  const image=event.target as HTMLImageElement
  if(props.intrinsic&&image.naturalWidth>0&&image.naturalHeight>0)intrinsicRatio.value=`${image.naturalWidth} / ${image.naturalHeight}`
}
watch(()=>props.src,(value)=>{recovery.reset();failed.value=false;currentSrc.value=value})
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
  finishRequest()
  if(currentSrc.value?.includes('/backend/api/catalog/media/'))requestAllowed.value=false
  if(currentSrc.value!==props.fallbackSrc&&props.src&&recovery.failed(props.src))return
  if(hasRealPhoto.value)failed.value=true
  if(props.fallbackSrc&&currentSrc.value!==props.fallbackSrc)currentSrc.value=props.fallbackSrc
  else currentSrc.value=undefined
}
function retryImage(){
  if(!hasRealPhoto.value||!props.src)return
  failed.value=false
  loaded.value=false
  recovery.retryNow(props.src)
}
</script>

<template>
  <div ref="container" class="smart-image relative overflow-hidden" :class="ratioClass" :style="intrinsic?{aspectRatio:intrinsicRatio}:undefined" :role="interactiveLoading?'group':'img'" :aria-label="alt || label" :aria-busy="photoLoading||undefined">
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
    <picture v-if="currentSrc&&ready&&requestAllowed" class="absolute inset-0 z-20" :style="{opacity:loaded||currentSrc===fallbackSrc?1:0}">
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
    <div v-if="photoLoading" class="photo-loading-state pointer-events-none absolute inset-0 z-40 grid place-items-center" role="status" :aria-label="loadingLabel||'در حال دریافت عکس'">
      <span class="photo-loading-ring" aria-hidden="true"/>
    </div>
    <div v-if="interactiveLoading&&hasRealPhoto&&failed" class="absolute inset-0 z-40 grid place-items-center pointer-events-none">
      <button type="button" class="photo-retry-button pointer-events-auto" :aria-label="retryLabel||'تلاش دوباره برای دریافت عکس'" :title="retryLabel||'تلاش دوباره برای دریافت عکس'" @click.stop.prevent="retryImage">
        <RotateCw :size="25" :stroke-width="2" aria-hidden="true"/>
      </button>
    </div>
    <div v-if="fit==='edge-extend'" class="smart-image-vignette pointer-events-none absolute inset-0 z-30" aria-hidden="true"/>
  </div>
</template>

<style scoped>
.photo-loading-ring{width:34px;height:34px;border:3px solid rgba(255,255,255,.75);border-top-color:var(--role-brand-blue,#0714C2);border-right-color:var(--role-brand-blue,#0714C2);border-radius:50%;box-shadow:0 1px 3px #0003;animation:photo-ring-turn .8s linear infinite}
.photo-retry-button{display:grid;place-items:center;width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,.94);color:var(--role-brand-blue,#0714C2);box-shadow:0 2px 8px #0003}
.photo-retry-button:focus-visible{outline:3px solid var(--role-brand-blue,#0714C2);outline-offset:4px}
@keyframes photo-ring-turn{to{transform:rotate(360deg)}}
@media(prefers-reduced-motion:reduce){.photo-loading-ring{animation:none}}
</style>
