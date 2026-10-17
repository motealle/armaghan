<script setup lang="ts">
import { computed } from 'vue'
import { useAdminStore } from '@/features/admin/store'
import { filterCatalogProducts } from '@/features/catalog/services/productFilters'
import ProductGrid from '@/features/catalog/components/ProductGrid.vue'
import AboutArmaghanSection from '@/features/home/components/AboutArmaghanSection.vue'
import CapabilitiesSection from '@/features/home/components/CapabilitiesSection.vue'
import HeroSection from '@/features/home/components/HeroSection.vue'
import ProductCategoryBanners from '@/features/home/components/ProductCategoryBanners.vue'
import WhyArmaghanSection from '@/features/home/components/WhyArmaghanSection.vue'
import { useResolvedAppearance } from '@/composables/useResolvedAppearance'
import { useCatalogStore } from '@/stores/catalog'
import { useLocaleStore } from '@/stores/locale'

const catalog=useCatalogStore()
const admin=useAdminStore()
const recommended=computed(()=>filterCatalogProducts(catalog.items,{category:'all',subcategory:'all',availability:'all',query:''},p=>p.name,p=>p.subcategoryName).slice(0,6))
const locale=useLocaleStore()
const {policy}=useResolvedAppearance()
function scrollProducts(){document.getElementById('product-categories')?.scrollIntoView({behavior:window.matchMedia('(prefers-reduced-motion: reduce)').matches?'auto':'smooth',block:'start'})}
</script>

<template>
  <div data-style-id="home.content" data-style-label="محتوای صفحه خانه" class="home-page space-y-5 lg:space-y-8">
    <HeroSection/>
    <button v-if="policy.showProductBanners" type="button" class="catalog-jump w-full" @click="scrollProducts">{{locale.t('productsTitle')}} <span aria-hidden="true">↓</span></button>

    <AboutArmaghanSection v-if="policy.showAbout"/>
    <WhyArmaghanSection v-if="policy.showWhy"/>
    <CapabilitiesSection v-if="policy.showCapabilities"/>
    <ProductCategoryBanners v-if="policy.showProductBanners"/>

    <section v-if="policy.homeProductGrid==='recommended-6'" data-style-id="home.recommended" data-style-label="بخش محصولات پیشنهادی" class="home-section">
      <div class="mb-4 flex items-end justify-between gap-3">
        <div>
          <h2 class="text-xl font-black leading-tight text-[var(--c-text)] lg:text-2xl">{{locale.t('recommended')}}</h2>
          <p class="mt-1 text-sm leading-6 text-[var(--c-muted)]">{{locale.t('favoriteHelp')}}</p>
        </div>
        <RouterLink to="/products" class="shrink-0 text-xs font-extrabold text-[var(--c-primary)]">{{locale.t('allProducts')}}</RouterLink>
      </div>
      <ProductGrid :products="recommended" :admin-orderable="!!admin.identity&&catalog.syncState==='synced'"/>
    </section>
  </div>
</template>
