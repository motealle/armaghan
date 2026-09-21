<script setup lang="ts">
import { BadgeCheck, MessageCircleMore, Sparkles } from '@lucide/vue'
import { categories } from '@/data/catalog'
import { useCatalogStore } from '@/stores/catalog'
import { useLocaleStore } from '@/stores/locale'
import HeroCarousel from '@/features/home/components/HeroCarousel.vue'
import ProductGrid from '@/features/catalog/components/ProductGrid.vue'
import SmartImage from '@/components/media/SmartImage.vue'

const catalog=useCatalogStore()
const locale=useLocaleStore()
const trustItems=[
  {icon:Sparkles,label:'brandCapabilities',text:'brandCapabilitiesText'},
  {icon:BadgeCheck,label:'brandDocuments',text:'brandDocumentsText'},
  {icon:MessageCircleMore,label:'brandSales',text:'brandSalesText'},
]
</script>

<template>
  <div class="space-y-8">
    <HeroCarousel/>

    <section>
      <div class="mb-3">
        <h2 class="text-xl font-black leading-tight text-[var(--c-text)]">{{locale.t('categories')}}</h2>
        <p class="mt-1 text-sm leading-6 text-[var(--c-muted)]">{{locale.t('categoryHelp')}}</p>
      </div>
      <div class="grid grid-cols-3 gap-2">
        <RouterLink v-for="category in categories" :key="category.code" :to="{path:'/products',query:{category:category.code}}" class="overflow-hidden rounded-2xl border border-[var(--c-border)] bg-[var(--c-surface)] shadow-sm">
          <SmartImage :src="category.image" :alt="locale.categoryName(category.code,category.name)" :label="locale.categoryName(category.code,category.name)"/>
          <div class="p-3">
            <div class="flex items-center gap-2"><Sparkles :size="16" class="text-[var(--c-primary)]"/><b class="text-sm">{{locale.categoryName(category.code,category.name)}}</b></div>
            <small class="mt-1 hidden text-[11px] leading-5 text-[var(--c-muted)] sm:block">{{locale.categorySubtitle(category.code,category.subtitle)}}</small>
          </div>
        </RouterLink>
      </div>
    </section>

    <section>
      <div class="mb-3 flex items-end justify-between gap-3">
        <div>
          <h2 class="text-xl font-black leading-tight text-[var(--c-text)]">{{locale.t('recommended')}}</h2>
          <p class="mt-1 text-sm leading-6 text-[var(--c-muted)]">{{locale.t('favoriteHelp')}}</p>
        </div>
        <RouterLink to="/products" class="shrink-0 text-xs font-extrabold text-[var(--c-primary)]">{{locale.t('allProducts')}}</RouterLink>
      </div>
      <ProductGrid :products="catalog.items.slice(0,6)"/>
    </section>

    <section>
      <h2 class="mb-3 text-xl font-black leading-tight text-[var(--c-text)]">{{locale.t('brandIntro')}}</h2>
      <div class="mb-3 overflow-hidden rounded-2xl border border-[var(--c-border)] bg-[var(--c-surface)] shadow-sm">
        <SmartImage :src="catalog.items[0]?.gallery?.[1]" :alt="locale.t('brandIntro')" :label="locale.t('manufacturer')" aspect="hero"/>
      </div>
      <div class="grid gap-3 md:grid-cols-3">
        <article v-for="item in trustItems" :key="item.label" class="rounded-2xl border border-[var(--c-border)] bg-[var(--c-surface)] p-4 shadow-sm">
          <div class="mb-3 grid h-11 w-11 place-items-center rounded-xl bg-[color-mix(in_srgb,var(--c-secondary)_10%,var(--c-surface))] text-[var(--c-secondary)]"><component :is="item.icon" :size="21"/></div>
          <b class="text-sm text-[var(--c-text)]">{{locale.t(item.label)}}</b>
          <p class="mt-2 text-xs leading-6 text-[var(--c-muted)]">{{locale.t(item.text)}}</p>
        </article>
      </div>
    </section>
  </div>
</template>
