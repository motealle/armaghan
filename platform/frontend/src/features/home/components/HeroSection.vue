<script setup lang="ts">
import {useHomeMediaStore} from '@/features/home-media/store'
const homeMedia=useHomeMediaStore();homeMedia.ensure()
import SmartImage from '@/components/media/SmartImage.vue'
import HeroCarousel from '@/features/home/components/HeroCarousel.vue'
import { test26Media } from '@/data/home26'
import { useResolvedAppearance } from '@/composables/useResolvedAppearance'
import { useLocaleStore } from '@/stores/locale'
import { useVisualStyleStore } from '@/features/visual-editor/store'

const locale=useLocaleStore()
const visual=useVisualStyleStore()
const {policy}=useResolvedAppearance()
</script>

<template>
  <HeroCarousel v-if="policy.heroMode==='carousel'"/>
  <section v-else data-style-id="hero.shell" data-style-label="قاب هیرو" class="test26-single-hero overflow-hidden rounded-[1.5rem] shadow-xl">
    <div data-style-id="hero.media" data-style-label="تصویر هیرو" class="test26-single-hero-media">
      <SmartImage
        :src="homeMedia.resolve('hero',test26Media.hero.image)"
        :fallback-src="test26Media.hero.fallback"
        :alt="locale.t('heroSingleAlt')"
        :label="locale.t('heroSingleAlt')"
        aspect="hero"
        eager
      />
    </div>
    <div data-style-id="hero.caption" data-style-label="نوار متن هیرو" class="test26-single-hero-copy">
      <h1 data-style-id="hero.title" data-style-label="عنوان هیرو" data-editable-text="true">{{visual.resolveText('hero.title',locale.locale,locale.t('heroSingleSlogan'))}}</h1>
    </div>
  </section>
</template>
