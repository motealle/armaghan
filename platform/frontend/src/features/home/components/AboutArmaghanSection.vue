<script setup lang="ts">
import SmartImage from '@/components/media/SmartImage.vue'
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
          src="./images/test26/home/about-armaghan.webp"
          fallback-src="https://placehold.co/1600x1200/E8F2EF/10243E.webp?text=IMAGE+REQUIRED%0AAbout+Armaghan+1600x1200"
          :alt="locale.t('aboutArmaghanImageAlt')"
          :label="locale.t('aboutArmaghanImageAlt')"
          aspect="card"
        />
      </div>
    </div>
  </section>
</template>
