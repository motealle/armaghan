<script setup lang="ts">
import SmartImage from '@/components/media/SmartImage.vue'
import HeroCarousel from '@/features/home/components/HeroCarousel.vue'
import { test26Media } from '@/data/home26'
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
        :src="test26Media.hero.image"
        :fallback-src="test26Media.hero.fallback"
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
