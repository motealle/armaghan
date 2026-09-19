<script setup lang="ts">
import { BadgeCheck, ExternalLink, MessageCircleMore, Sparkles } from '@lucide/vue'
import { categories } from '@/data/catalog'
import { useCatalogStore } from '@/stores/catalog'
import HeroCarousel from '@/features/home/components/HeroCarousel.vue'
import ProductGrid from '@/features/catalog/components/ProductGrid.vue'
import SmartImage from '@/components/media/SmartImage.vue'

const catalog=useCatalogStore()
const trustItems=[
  {icon:Sparkles,label:'توانمندی‌ها'},
  {icon:BadgeCheck,label:'اعتبارات و اسناد'},
  {icon:MessageCircleMore,label:'ارتباط فروش'},
]
</script>

<template>
  <div class="space-y-8">
    <HeroCarousel />

    <aside class="flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-3 text-xs leading-6 text-amber-950">
      <Sparkles :size="18" class="mt-1 shrink-0" />
      <div>
        <b>تصاویر آزمایشی با منبع آزاد</b>
        <p>عکس‌های این نسخه برای شکل‌دادن تجربه بصری از منابع دارای مجوز باز تهیه و روی خود هاست ذخیره می‌شوند؛ این تصاویر محصول یا کارخانه واقعی ارمغان نیستند.</p>
        <RouterLink to="/credits" class="mt-1 inline-flex items-center gap-1 font-extrabold underline underline-offset-4">مشاهده منابع و مجوزها <ExternalLink :size="13" /></RouterLink>
      </div>
    </aside>

    <section>
      <div class="mb-3">
        <h2 class="text-xl font-black">دسته‌بندی محصولات</h2>
        <p class="mt-1 text-xs text-slate-500">سه دسته اصلی، شش زیردسته و مسیر روشن برای مشتری غیرمتخصص.</p>
      </div>
      <div class="grid grid-cols-3 gap-2">
        <RouterLink v-for="category in categories" :key="category.code" :to="{path:'/products',query:{category:category.code}}" class="overflow-hidden rounded-2xl border border-slate-200 bg-[var(--c-paper)] shadow-sm">
          <SmartImage :src="category.image" :alt="category.name" :label="category.name" />
          <div class="p-3">
            <div class="flex items-center gap-2"><Sparkles :size="16" class="text-[var(--c-primary)]" /><b class="text-sm">{{ category.name }}</b></div>
            <small class="mt-1 hidden text-[10px] text-slate-500 sm:block">{{ category.subtitle }}</small>
          </div>
        </RouterLink>
      </div>
    </section>

    <section>
      <div class="mb-3 flex items-end justify-between gap-3">
        <div>
          <h2 class="text-xl font-black">محصولات پیشنهادی</h2>
          <p class="mt-1 text-xs text-slate-500">۱۸ محصول نمونه برای تست مرور، استعلام و سفارش تولید.</p>
        </div>
        <RouterLink to="/products" class="shrink-0 text-xs font-extrabold text-[var(--c-primary)]">همه محصولات</RouterLink>
      </div>
      <ProductGrid :products="catalog.items.slice(0,6)" />
    </section>

    <section>
      <h2 class="mb-3 text-xl font-black">معرفی تولیدکننده</h2>
      <div class="grid gap-3 md:grid-cols-3">
        <article v-for="item in trustItems" :key="item.label" class="rounded-2xl border border-slate-200 bg-[var(--c-paper)] p-4 shadow-sm">
          <div class="mb-3 grid h-11 w-11 place-items-center rounded-xl bg-emerald-50 text-emerald-700"><component :is="item.icon" :size="21" /></div>
          <b class="text-sm">{{ item.label }}</b>
          <p class="mt-2 text-xs leading-6 text-slate-500">اطلاعات تأییدشده کسب‌وکار در نسخه نهایی این بخش تکمیل می‌شود.</p>
        </article>
      </div>
    </section>
  </div>
</template>
