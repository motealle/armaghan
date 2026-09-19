<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { ExternalLink, Image as ImageIcon, ShieldCheck } from '@lucide/vue'

interface Credit {
  slot: string
  file: string
  commons_title: string
  source_page: string
  creator: string
  license: string
  license_url: string
  derivative_note: string
}

const items=ref<Credit[]>([])
const loading=ref(true)
const failed=ref(false)

onMounted(async()=>{
  try{
    const response=await fetch('./images/web-stock/credits.json',{cache:'no-store'})
    if(!response.ok)throw new Error('credits unavailable')
    items.value=await response.json() as Credit[]
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
      <div class="mb-3 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-white/10"><ShieldCheck :size="23" /></div>
      <h1 class="text-2xl font-black">منابع تصاویر آزمایشی</h1>
      <p class="mt-2 max-w-2xl text-sm leading-7 text-white/70">این تصاویر فقط برای تست تجربه بصری تهیه شده‌اند، روی خود هاست نگهداری می‌شوند و نماینده محصول، کارخانه، مشتری یا گواهی واقعی ارمغان نیستند.</p>
    </header>

    <div v-if="loading" class="grid gap-3 md:grid-cols-2">
      <div v-for="i in 6" :key="i" class="skeleton h-36 rounded-2xl" />
    </div>
    <div v-else-if="failed || !items.length" class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">
      اطلاعات attribution هنوز توسط pipeline تصاویر وارد نشده است.
    </div>
    <div v-else class="grid gap-3 md:grid-cols-2">
      <article v-for="item in items" :key="item.slot" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-start gap-3">
          <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-indigo-50 text-[var(--c-primary)]"><ImageIcon :size="19" /></div>
          <div class="min-w-0">
            <b class="block text-sm">{{ item.commons_title }}</b>
            <div class="mt-1 text-xs text-slate-500">{{ item.creator }}</div>
            <div class="mt-2 inline-flex rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-700">{{ item.license }}</div>
          </div>
        </div>
        <p class="mt-3 text-[11px] leading-5 text-slate-500">{{ item.derivative_note }}</p>
        <div class="mt-3 flex flex-wrap gap-2 text-xs">
          <a :href="item.source_page" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 font-bold text-[var(--c-primary)]">صفحه منبع <ExternalLink :size="12" /></a>
          <a v-if="item.license_url" :href="item.license_url" target="_blank" rel="noopener noreferrer" class="font-bold text-slate-600">متن مجوز</a>
        </div>
      </article>
    </div>
  </section>
</template>
