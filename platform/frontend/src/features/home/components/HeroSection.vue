<script setup lang="ts">
import SmartImage from '@/components/media/SmartImage.vue'
import HeroCarousel from '@/features/home/components/HeroCarousel.vue'
import { useResolvedAppearance } from '@/composables/useResolvedAppearance'
import { useLocaleStore } from '@/stores/locale'

const locale=useLocaleStore()
const {policy}=useResolvedAppearance()
</script>

<template>
  <HeroCarousel v-if="policy.heroMode==='carousel'"/>
  <section v-else class="test26-single-hero overflow-hidden rounded-[1.5rem] shadow-xl">
    <div class="test26-single-hero-media">
      <SmartImage
        src="./images/final/hero/hero-brand.webp"
        fallback-src="https://placehold.co/1920x1080/0B2340/FFFFFF.webp?text=IMAGE+REQUIRED%0AHome+Hero+1920x1080"
        :alt="locale.t('heroSingleAlt')"
        :label="locale.t('heroSingleAlt')"
        aspect="hero"
        eager
      />
    </div>
    <div class="test26-single-hero-copy">
      <h1>{{locale.t('heroSingleSlogan')}}</h1>
    </div>
  </section>
</template>
