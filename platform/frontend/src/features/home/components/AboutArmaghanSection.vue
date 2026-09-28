<script setup lang="ts">
import SmartImage from '@/components/media/SmartImage.vue'
import { test26Media } from '@/data/home26'
import { useLocaleStore } from '@/stores/locale'

const locale=useLocaleStore()
</script>

<template>
  <section id="about-armaghan" class="test26-about home-section">
    <div class="test26-section-heading">
      <span>{{locale.t('aboutArmaghanEyebrow')}}</span>
      <h2>{{locale.t('aboutArmaghanTitle')}}</h2>
    </div>
    <div class="test26-about-layout">
      <div class="test26-about-copy">
        <p>{{locale.t('aboutArmaghanText')}}</p>
      </div>
      <div class="test26-about-media">
        <SmartImage
          :src="test26Media.about.image"
          :fallback-src="test26Media.about.fallback"
          :alt="locale.t('aboutArmaghanImageAlt')"
          :label="locale.t('aboutArmaghanImageAlt')"
          aspect="card"
        />
      </div>
    </div>
  </section>
</template>
