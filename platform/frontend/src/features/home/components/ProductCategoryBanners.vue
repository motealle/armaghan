<script setup lang="ts">
import { ArrowUpLeft } from '@lucide/vue'
import SmartImage from '@/components/media/SmartImage.vue'
import { categories } from '@/data/catalog'
import { productBannerMedia } from '@/data/home26'
import { useLocaleStore } from '@/stores/locale'
import { useVisualStyleStore } from '@/features/visual-editor/store'

const locale=useLocaleStore()
const visual=useVisualStyleStore()
</script>

<template>
  <section data-style-id="home.product-banners" data-style-label="بخش بنرهای محصول" class="home-section test26-product-banners">
    <div data-style-id="home.product-banners.heading" data-style-label="سربرگ بنرهای محصول" class="test26-section-heading">
      <span data-style-id="home.product-banners.eyebrow" data-style-label="بالانویس بنرهای محصول" data-editable-text="true">{{visual.resolveText('home.product-banners.eyebrow',locale.locale,locale.t('productBannersEyebrow'))}}</span>
      <h2 data-style-id="home.product-banners.title" data-style-label="عنوان بنرهای محصول" data-editable-text="true">{{visual.resolveText('home.product-banners.title',locale.locale,locale.t('productBannersTitle'))}}</h2>
      <p data-style-id="home.product-banners.intro" data-style-label="مقدمه بنرهای محصول" data-editable-text="true">{{visual.resolveText('home.product-banners.intro',locale.locale,locale.t('productBannersIntro'))}}</p>
    </div>
    <div class="test26-product-banner-list">
      <RouterLink
        v-for="category in categories"
        :key="category.code"
        :to="{path:'/products',query:{category:category.code}}"
        class="test26-product-banner"
        :data-style-id="`home.product-banner.${category.code}`"
        :data-style-label="`بنر ${locale.categoryName(category.code,category.name)}`"
      >
        <SmartImage
          :src="productBannerMedia[category.code]?.image"
          :fallback-src="productBannerMedia[category.code]?.fallback"
          :alt="locale.categoryName(category.code,category.name)"
          :label="locale.categoryName(category.code,category.name)"
          aspect="hero"
        />
        <span class="test26-product-banner-shade"/>
        <span :data-style-id="`home.product-banner.${category.code}.copy`" :data-style-label="`متن بنر ${locale.categoryName(category.code,category.name)}`" class="test26-product-banner-copy">
          <b>{{locale.categoryName(category.code,category.name)}}</b>
          <small>{{locale.categorySubtitle(category.code,category.subtitle)}}</small>
        </span>
        <span class="test26-product-banner-arrow"><ArrowUpLeft :size="20"/></span>
      </RouterLink>
    </div>
  </section>
</template>
