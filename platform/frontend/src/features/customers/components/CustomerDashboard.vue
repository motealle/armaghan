<script setup lang="ts">
import { Check, Clock3, Heart, RotateCcw } from '@lucide/vue'
</script>
<template>
  <div class="grid gap-4 md:grid-cols-2">
    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
      <div class="flex items-center justify-between"><h2 class="font-black">تایملاین سفارش #AR-1042</h2><span class="rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-700">فعال</span></div>
      <div class="mt-4 space-y-4">
        <div v-for="(item,index) in ['ثبت استعلام','تأیید مشخصات','تأیید پیش‌پرداخت','در حال تولید']" :key="item" class="flex items-center gap-3">
          <span class="grid h-8 w-8 place-items-center rounded-full" :class="index<3?'bg-emerald-100 text-emerald-700':'bg-slate-100 text-slate-400'"><Check v-if="index<3" :size="17"/><Clock3 v-else :size="17"/></span><span class="text-sm">{{item}}</span>
        </div>
      </div>
    </article>
    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
      <h2 class="font-black">دسترسی سریع</h2>
      <div class="mt-3 grid gap-2">
        <button class="quick-account"><RotateCcw :size="18"/> سفارش مجدد</button>
        <RouterLink to="/favorites" class="quick-account"><Heart :size="18"/> مطلوب‌ها</RouterLink>
        <button class="quick-account"><Check :size="18"/> تأیید پیش‌پرداخت</button>
      </div>
    </article>
  </div>
</template>
