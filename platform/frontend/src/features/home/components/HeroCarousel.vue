<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { ArrowLeft, Boxes, Globe2, Sparkles } from '@lucide/vue'
import SmartImage from '@/components/media/SmartImage.vue'
import { digikalaHeroMedia } from '@/data/digikalaMedia.generated'
import { useLocaleStore } from '@/stores/locale'

const locale=useLocaleStore()
const current=ref(0)
const paused=ref(false)
let timer:number|undefined
const reduceMotion=window.matchMedia('(prefers-reduced-motion: reduce)')

const copy={
 fa:[
  ['ARMAGHAN TRADING','پوشاک برای بازار حرفه‌ای، با مسیر سفارش روشن','کاتالوگ، مذاکره و ارتباط فروش در یک تجربه ساده و قابل فهم.'],
  ['PRODUCTION','از انتخاب محصول تا تولید سفارشی','مشخصات ثابت را واضح ببینید و موارد قابل مذاکره را برای گفتگو آماده کنید.'],
  ['EXPORT','آماده برای ارتباط تجاری بین‌المللی','مسیر سریع برای محصولات، تولید با برند و بسته‌بندی سفارشی.'],
 ],
 ar:[
  ['ARMAGHAN TRADING','ملابس للسوق المهني بمسار طلب واضح','كتالوج وتفاوض وتواصل مبيعات في تجربة بسيطة.'],
  ['PRODUCTION','من اختيار المنتج إلى الإنتاج المخصص','شاهد المواصفات الثابتة وحدد البنود القابلة للتفاوض.'],
  ['EXPORT','جاهز للتواصل التجاري الدولي','مسارات واضحة للمنتجات والعلامة التجارية والتغليف.'],
 ],
 en:[
  ['ARMAGHAN TRADING','Apparel for professional markets, with a clear order path','Catalog, negotiation and sales handoff in one focused experience.'],
  ['PRODUCTION','From product selection to custom production','See locked specs clearly and prepare negotiable details before contact.'],
  ['EXPORT','Ready for international commercial conversations','Fast paths for products, private label and custom packaging.'],
 ],
 ku:[
  ['ARMAGHAN TRADING','جل و بەرگ بۆ بازاڕی پیشەیی، بە ڕێگای داواکاری ڕوون','کاتەلۆگ، گفتوگۆ و پەیوەندی فرۆشتن لە ئەزموونێکی سادەدا.'],
  ['PRODUCTION','لە هەڵبژاردنی بەرهەم تا بەرهەمهێنانی تایبەت','تایبەتمەندی جێگیرەکان ببینە و بڕگەی گفتوگۆپێکراو ئامادە بکە.'],
  ['EXPORT','ئامادە بۆ پەیوەندی بازرگانی نێودەوڵەتی','ڕێگای خێرا بۆ بەرهەم، براند و پاکەتکردنی تایبەت.'],
 ],
} as const
const icons=[Sparkles,Boxes,Globe2]
const slides=computed(()=>copy[locale.locale].map((item,index)=>({
 image:digikalaHeroMedia[index] ?? digikalaHeroMedia[0],
 kicker:item[0],title:item[1],text:item[2],icon:icons[index],
})))
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
  <section class="hero-shell relative overflow-hidden rounded-[1.75rem] text-white shadow-xl" @mouseenter="paused=true;start()" @mouseleave="paused=false;start()" @focusin="paused=true;start()" @focusout="paused=false;start()">
    <SmartImage :src="slide.image" alt="" :label="slide.kicker" aspect="hero" eager />
    <div class="absolute inset-0 z-30 bg-gradient-to-l from-slate-950/80 via-slate-950/52 to-slate-950/18" />
    <div class="absolute inset-0 z-40 flex items-end p-5 md:p-9">
      <div class="max-w-2xl">
        <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[10px] font-extrabold tracking-[.16em]">
          <component :is="slide.icon" :size="15"/> {{slide.kicker}}
        </div>
        <h1 class="text-3xl font-black leading-[1.45] md:text-5xl">{{slide.title}}</h1>
        <p class="mt-3 max-w-xl text-sm leading-7 text-white/80 md:text-base">{{slide.text}}</p>
        <div class="mt-5 flex flex-wrap gap-2">
          <RouterLink to="/products" class="inline-flex min-h-12 items-center gap-2 rounded-xl bg-[var(--c-accent)] px-4 font-extrabold text-slate-900">
            {{locale.t('products')}} <ArrowLeft :size="18"/>
          </RouterLink>
          <RouterLink to="/production" class="inline-flex min-h-12 items-center rounded-xl border border-white/20 bg-white/10 px-4 font-extrabold">{{locale.t('production')}}</RouterLink>
        </div>
        <div class="mt-5 flex gap-2">
          <button v-for="(_,i) in slides" :key="i" class="h-2 rounded-full transition-all" :class="i===current?'w-8 bg-white':'w-2 bg-white/45'" :aria-label="'Slide '+(i+1)" @click="current=i"/>
        </div>
      </div>
    </div>
  </section>
</template>
