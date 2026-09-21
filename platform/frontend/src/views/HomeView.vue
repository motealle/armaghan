<script setup lang="ts">
import { BadgeCheck, ChevronLeft, MessageCircleMore, Sparkles } from '@lucide/vue'
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
  <div class="home-page space-y-9 lg:space-y-14">
    <HeroCarousel/>

    <section class="home-section">
      <div class="home-section-head mb-4">
        <h2 class="text-xl font-black leading-tight text-[var(--c-text)] lg:text-2xl">{{locale.t('categories')}}</h2>
        <p class="mt-1 text-sm leading-6 text-[var(--c-muted)]">{{locale.t('categoryHelp')}}</p>
      </div>
      <div class="home-category-grid grid grid-cols-3 gap-2.5 md:gap-3">
        <RouterLink
          v-for="category in categories"
          :key="category.code"
          :to="{path:'/products',query:{category:category.code}}"
          class="category-card home-category-card"
        >
          <span class="category-card-media">
            <img :src="category.image" alt="" loading="lazy" decoding="async">
          </span>
          <span class="category-card-footer">
            <span class="category-number">0{{category.code}}</span>
            <b class="category-card-title">{{locale.categoryName(category.code,category.name)}}</b>
            <ChevronLeft :size="16" class="category-chevron" aria-hidden="true"/>
          </span>
        </RouterLink>
      </div>
    </section>

    <section class="home-section">
      <div class="mb-4 flex items-end justify-between gap-3">
        <div>
          <h2 class="text-xl font-black leading-tight text-[var(--c-text)] lg:text-2xl">{{locale.t('recommended')}}</h2>
          <p class="mt-1 text-sm leading-6 text-[var(--c-muted)]">{{locale.t('favoriteHelp')}}</p>
        </div>
        <RouterLink to="/products" class="shrink-0 text-xs font-extrabold text-[var(--c-primary)]">{{locale.t('allProducts')}}</RouterLink>
      </div>
      <ProductGrid :products="catalog.items.slice(0,6)"/>
    </section>

    <section class="home-section home-about-section">
      <h2 class="mb-4 text-xl font-black leading-tight text-[var(--c-text)] lg:text-2xl">{{locale.t('brandIntro')}}</h2>
      <div class="home-about-layout">
        <div class="home-about-media overflow-hidden rounded-2xl border border-[var(--c-border)] bg-[var(--c-surface)] shadow-sm">
          <SmartImage src="./images/final/details/fabric-detail.webp" :alt="locale.t('brandIntro')" :label="locale.t('manufacturer')" aspect="hero"/>
        </div>
        <div class="home-trust-list">
          <article v-for="item in trustItems" :key="item.label" class="home-trust-card rounded-2xl border border-[var(--c-border)] bg-[var(--c-surface)] p-4 shadow-sm">
            <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-[color-mix(in_srgb,var(--c-secondary)_10%,var(--c-surface))] text-[var(--c-secondary)]"><component :is="item.icon" :size="21"/></div>
            <div>
              <b class="text-sm text-[var(--c-text)]">{{locale.t(item.label)}}</b>
              <p class="mt-1.5 text-xs leading-6 text-[var(--c-muted)]">{{locale.t(item.text)}}</p>
            </div>
          </article>
        </div>
      </div>
    </section>
  </div>
</template>
