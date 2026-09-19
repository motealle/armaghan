<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { ArrowLeft, Boxes, Globe2, Sparkles } from '@lucide/vue'
import SmartImage from '@/components/media/SmartImage.vue'

const slides = [
  { image:'./images/web-stock/hero-brand.webp', kicker:'ARMAGHAN TRADING', title:'پوشاک برای بازار حرفه‌ای، با مسیر سفارش روشن', text:'کاتالوگ، مذاکره و ارتباط فروش در یک تجربه ساده و قابل فهم.', icon:Sparkles },
  { image:'./images/web-stock/hero-production.webp', kicker:'PRODUCTION', title:'از انتخاب محصول تا تولید سفارشی', text:'مشخصات ثابت را واضح ببینید و موارد قابل مذاکره را برای گفتگو آماده کنید.', icon:Boxes },
  { image:'./images/web-stock/hero-export.webp', kicker:'EXPORT', title:'آماده برای ارتباط تجاری بین‌المللی', text:'مسیر سریع برای محصولات، تولید با برند و بسته‌بندی سفارشی.', icon:Globe2 },
]
const current=ref(0)
let timer:number|undefined
const slide=computed(()=>slides[current.value]!)
onMounted(()=>{timer=window.setInterval(()=>current.value=(current.value+1)%slides.length,6500)})
onBeforeUnmount(()=>{if(timer)window.clearInterval(timer)})
</script>

<template>
  <section class="hero-shell relative overflow-hidden rounded-[1.75rem] text-white shadow-xl">
    <SmartImage :src="slide.image" alt="" :label="slide.kicker" aspect="hero" eager />
    <div class="absolute inset-0 z-30 bg-gradient-to-l from-slate-950/80 via-slate-950/52 to-slate-950/18" />
    <div class="absolute inset-0 z-40 flex items-end p-5 md:p-9">
      <div class="max-w-2xl">
        <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[10px] font-extrabold tracking-[.16em]">
          <component :is="slide.icon" :size="15" /> {{ slide.kicker }}
        </div>
        <h1 class="text-3xl font-black leading-[1.45] md:text-5xl">{{ slide.title }}</h1>
        <p class="mt-3 max-w-xl text-sm leading-7 text-white/80 md:text-base">{{ slide.text }}</p>
        <div class="mt-5 flex flex-wrap gap-2">
          <RouterLink to="/products" class="inline-flex min-h-12 items-center gap-2 rounded-xl bg-[var(--c-accent)] px-4 font-extrabold text-slate-900">
            محصولات <ArrowLeft :size="18" />
          </RouterLink>
          <RouterLink to="/production" class="inline-flex min-h-12 items-center rounded-xl border border-white/20 bg-white/10 px-4 font-extrabold">
            سفارش تولید
          </RouterLink>
        </div>
        <div class="mt-5 flex gap-2">
          <button v-for="(_,i) in slides" :key="i" class="h-2 rounded-full transition-all" :class="i===current?'w-8 bg-white':'w-2 bg-white/45'" :aria-label="'اسلاید '+(i+1)" @click="current=i" />
        </div>
      </div>
    </div>
  </section>
</template>
