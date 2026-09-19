<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { ExternalLink, Image as ImageIcon, ShieldCheck } from '@lucide/vue'

interface DigikalaProductCredit{
  prototype_code:string
  digikala_product_id:number
  source_product_url:string
  title_fa:string
  query:string
  images:Array<{file:string;url:string;bytes:number;content_type:string}>
}
interface DigikalaManifest{
  source:string
  authorization_basis:string
  fetched_at:string
  products:DigikalaProductCredit[]
}
const manifest=ref<DigikalaManifest|null>(null)
const loading=ref(true)
const failed=ref(false)

onMounted(async()=>{
  try{
    const response=await fetch('./images/digikala/manifest.json',{cache:'no-store'})
    if(!response.ok)throw new Error('Digikala manifest unavailable')
    manifest.value=await response.json() as DigikalaManifest
  }catch{
    failed.value=true
  }finally{
    loading.value=false
  }
})
</script>

<template>
  <section class="space-y-5">
    <header class="rounded-3xl bg-slate-950 p-5 text-white md:p-7">
      <div class="mb-3 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-white/10"><ShieldCheck :size="23"/></div>
      <h1 class="text-2xl font-black">منابع تصاویر آزمایشی</h1>
      <p class="mt-2 max-w-2xl text-sm leading-7 text-white/70">
        تصاویر محصول این نسخه از کاتالوگ دیجی‌کالا گرفته شده‌اند و فقط در نمونه‌ی پروتوتایپ استفاده می‌شوند. مبنای استفاده، مجوز/قراردادی است که مالک پروژه اعلام کرده است؛ این صفحه برای نگهداری provenance فنی منبع تصاویر است، نه ارزیابی حقوقی مستقل.
      </p>
    </header>

    <div v-if="loading" class="grid gap-3 md:grid-cols-2"><div v-for="i in 6" :key="i" class="skeleton h-36 rounded-2xl"/></div>
    <div v-else-if="failed || !manifest?.products?.length" class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">
      manifest دیجی‌کالا در این build در دسترس نیست؛ fallback محلی برای توسعه استفاده شده است.
    </div>
    <div v-else class="grid gap-3 md:grid-cols-2">
      <article v-for="item in manifest.products" :key="item.prototype_code" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-start gap-3">
          <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-indigo-50 text-[var(--c-primary)]"><ImageIcon :size="19"/></div>
          <div class="min-w-0">
            <b class="block text-sm">{{item.title_fa || ('محصول دیجی‌کالا '+item.digikala_product_id)}}</b>
            <div class="mt-1 text-xs text-slate-500">کد تست {{item.prototype_code}} · دو تصویر محلی</div>
            <div class="mt-2 inline-flex rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-700">Digikala prototype source</div>
          </div>
        </div>
        <div class="mt-3 flex flex-wrap gap-2 text-xs">
          <a :href="item.source_product_url" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 font-bold text-[var(--c-primary)]">محصول منبع <ExternalLink :size="12"/></a>
          <span class="text-slate-500">{{item.images.length}} فایل ذخیره‌شده</span>
        </div>
      </article>
    </div>
  </section>
</template>
