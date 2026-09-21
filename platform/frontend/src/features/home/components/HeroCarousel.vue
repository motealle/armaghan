<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { ArrowLeft, Boxes, Globe2, Sparkles } from '@lucide/vue'
import SmartImage from '@/components/media/SmartImage.vue'
import { useLocaleStore } from '@/stores/locale'

const locale=useLocaleStore()
const current=ref(0)
const paused=ref(false)
let timer:number|undefined
const reduceMotion=window.matchMedia('(prefers-reduced-motion: reduce)')
const icons=[Sparkles,Boxes,Globe2]
const media=[
  './images/final/hero/hero-brand.webp',
  './images/final/hero/hero-production.webp',
  './images/final/hero/hero-export.webp',
]

const slides=computed(()=>[
  {kicker:locale.t('hero1Kicker'),title:locale.t('hero1Title'),text:locale.t('hero1Text'),icon:icons[0],image:media[0]},
  {kicker:locale.t('hero2Kicker'),title:locale.t('hero2Title'),text:locale.t('hero2Text'),icon:icons[1],image:media[1]},
  {kicker:locale.t('hero3Kicker'),title:locale.t('hero3Title'),text:locale.t('hero3Text'),icon:icons[2],image:media[2]},
])
const slide=computed(()=>slides.value[current.value]!)

function start(){
  if(timer)window.clearInterval(timer)
  if(paused.value||reduceMotion.matches||document.hidden)return
  timer=window.setInterval(()=>current.value=(current.value+1)%slides.value.length,6500)
}
function visibility(){start()}
onMounted(()=>{start();document.addEventListener('visibilitychange',visibility);reduceMotion.addEventListener?.('change',start)})
onBeforeUnmount(()=>{if(timer)window.clearInterval(timer);document.removeEventListener('visibilitychange',visibility);reduceMotion.removeEventListener?.('change',start)})
</script>

<template>
  <section class="hero-shell overflow-hidden rounded-[1.5rem] text-white shadow-xl" @mouseenter="paused=true;start()" @mouseleave="paused=false;start()" @focusin="paused=true;start()" @focusout="paused=false;start()">
    <div class="hero-media-pane relative">
      <SmartImage :src="slide.image" alt="" :label="slide.kicker" aspect="hero" eager/>
      <div class="hero-media-shade absolute inset-0 z-30"/>
    </div>

    <div class="hero-copy-pane">
      <div class="hero-copy-inner">
        <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-white/14 bg-white/8 px-3 py-1 text-[10px] font-extrabold tracking-[.12em]">
          <component :is="slide.icon" :size="15"/> {{slide.kicker}}
        </div>
        <h1 class="hero-title text-[1.75rem] font-black leading-[1.42] md:text-[2.35rem] lg:text-[clamp(2.15rem,3.1vw,3.4rem)]">{{slide.title}}</h1>
        <p class="hero-text mt-3 max-w-xl text-sm leading-7 text-white/82 md:text-base">{{slide.text}}</p>
        <div class="mt-5 flex flex-wrap gap-2">
          <RouterLink to="/products" class="inline-flex min-h-12 items-center gap-2 rounded-xl bg-[var(--c-accent)] px-4 font-extrabold text-slate-900">
            {{locale.t('products')}} <ArrowLeft :size="18"/>
          </RouterLink>
          <RouterLink to="/production" class="inline-flex min-h-12 items-center rounded-xl border border-white/20 bg-white/8 px-4 font-extrabold">{{locale.t('production')}}</RouterLink>
        </div>
        <div class="mt-5 flex gap-2">
          <button v-for="(_,i) in slides" :key="i" class="h-2 rounded-full transition-all" :class="i===current?'w-8 bg-white':'w-2 bg-white/45'" :aria-label="`${i+1}`" @click="current=i"/>
        </div>
      </div>
    </div>
  </section>
</template>
