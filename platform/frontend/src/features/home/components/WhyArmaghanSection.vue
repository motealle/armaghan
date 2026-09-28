<script setup lang="ts">
import { useLocaleStore } from '@/stores/locale'

const locale=useLocaleStore()
const values=[
  ['whyCapacityTitle','whyCapacityText'],
  ['whyCustomTitle','whyCustomText'],
  ['whyDirectTitle','whyDirectText'],
  ['whyMarketTitle','whyMarketText'],
] as const
</script>

<template>
  <section class="home-section test26-why">
    <div class="test26-section-heading">
      <span>{{locale.t('whyArmaghanEyebrow')}}</span>
      <h2>{{locale.t('whyArmaghanTitle')}}</h2>
      <p>{{locale.t('whyArmaghanIntro')}}</p>
    </div>
    <ol class="test26-why-list">
      <li v-for="(item,index) in values" :key="item[0]">
        <span class="test26-why-number">{{index+1}}</span>
        <div>
          <h3>{{locale.t(item[0])}}</h3>
          <p>{{locale.t(item[1])}}</p>
        </div>
      </li>
    </ol>
  </section>
</template>
