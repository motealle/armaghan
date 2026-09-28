<script setup lang="ts">
import { ArrowUpLeft } from '@lucide/vue'
import SmartImage from '@/components/media/SmartImage.vue'
import { categories } from '@/data/catalog'
import { productBannerMedia } from '@/data/home26'
import { useLocaleStore } from '@/stores/locale'

const locale=useLocaleStore()
</script>

<template>
  <section class="home-section test26-product-banners">
    <div class="test26-section-heading">
      <span>{{locale.t('productBannersEyebrow')}}</span>
      <h2>{{locale.t('productBannersTitle')}}</h2>
      <p>{{locale.t('productBannersIntro')}}</p>
    </div>
    <div class="test26-product-banner-list">
      <RouterLink
        v-for="category in categories"
        :key="category.code"
        :to="{path:'/products',query:{category:category.code}}"
        class="test26-product-banner"
      >
        <SmartImage
          :src="productBannerMedia[category.code]?.image"
          :fallback-src="productBannerMedia[category.code]?.fallback"
          :alt="locale.categoryName(category.code,category.name)"
          :label="locale.categoryName(category.code,category.name)"
          aspect="hero"
        />
        <span class="test26-product-banner-shade"/>
        <span class="test26-product-banner-copy">
          <b>{{locale.categoryName(category.code,category.name)}}</b>
          <small>{{locale.categorySubtitle(category.code,category.subtitle)}}</small>
        </span>
        <span class="test26-product-banner-arrow"><ArrowUpLeft :size="20"/></span>
      </RouterLink>
    </div>
  </section>
</template>
