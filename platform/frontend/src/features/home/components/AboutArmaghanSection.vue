<script setup lang="ts">
import SmartImage from '@/components/media/SmartImage.vue'
import { test26Media } from '@/data/home26'
import { useLocaleStore } from '@/stores/locale'
import { useVisualStyleStore } from '@/features/visual-editor/store'

const locale=useLocaleStore()
const visual=useVisualStyleStore()
</script>

<template>
  <section id="about-armaghan" data-style-id="home.about" data-style-label="بخش درباره ارمغان" class="test26-about home-section">
    <div data-style-id="home.about.heading" data-style-label="سربرگ درباره ارمغان" class="test26-section-heading">
      <span data-style-id="home.about.eyebrow" data-style-label="بالانویس درباره ارمغان" data-editable-text="true">{{visual.resolveText('home.about.eyebrow',locale.locale,locale.t('aboutArmaghanEyebrow'))}}</span>
      <h2 data-style-id="home.about.title" data-style-label="عنوان درباره ارمغان" data-editable-text="true">{{visual.resolveText('home.about.title',locale.locale,locale.t('aboutArmaghanTitle'))}}</h2>
    </div>
    <div class="test26-about-layout">
      <div data-style-id="home.about.copy" data-style-label="پنل متن درباره ارمغان" class="test26-about-copy">
        <p data-style-id="home.about.text" data-style-label="متن درباره ارمغان" data-editable-text="true">{{visual.resolveText('home.about.text',locale.locale,locale.t('aboutArmaghanText'))}}</p>
      </div>
      <div data-style-id="home.about.media" data-style-label="تصویر درباره ارمغان" class="test26-about-media">
        <SmartImage
          :src="test26Media.about.image"
          :fallback-src="test26Media.about.fallback"
          :alt="locale.t('aboutArmaghanImageAlt')"
          :label="locale.t('aboutArmaghanImageAlt')"
          aspect="hero"
        />
      </div>
    </div>
  </section>
</template>
