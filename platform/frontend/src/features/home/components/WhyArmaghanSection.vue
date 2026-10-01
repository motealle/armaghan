<script setup lang="ts">
import { useLocaleStore } from '@/stores/locale'
import { useVisualStyleStore } from '@/features/visual-editor/store'

const locale=useLocaleStore()
const visual=useVisualStyleStore()
const values=[
  ['whyCapacityTitle','whyCapacityText'],
  ['whyCustomTitle','whyCustomText'],
  ['whyDirectTitle','whyDirectText'],
  ['whyMarketTitle','whyMarketText'],
] as const
</script>

<template>
  <section data-style-id="home.why" data-style-label="بخش چرا ارمغان" class="home-section test26-why">
    <div data-style-id="home.why.heading" data-style-label="سربرگ چرا ارمغان" class="test26-section-heading">
      <span data-style-id="home.why.eyebrow" data-style-label="بالانویس چرا ارمغان" data-editable-text="true">{{visual.resolveText('home.why.eyebrow',locale.locale,locale.t('whyArmaghanEyebrow'))}}</span>
      <h2 data-style-id="home.why.title" data-style-label="عنوان چرا ارمغان" data-editable-text="true">{{visual.resolveText('home.why.title',locale.locale,locale.t('whyArmaghanTitle'))}}</h2>
      <p data-style-id="home.why.intro" data-style-label="مقدمه چرا ارمغان" data-editable-text="true">{{visual.resolveText('home.why.intro',locale.locale,locale.t('whyArmaghanIntro'))}}</p>
    </div>
    <ol data-style-id="home.why.list" data-style-label="فهرست دلایل" class="test26-why-list">
      <li v-for="(item,index) in values" :key="item[0]" :data-style-id="`home.why.item.${index+1}`" :data-style-label="`کارت دلیل ${index+1}`">
        <span class="test26-why-number">{{index+1}}</span>
        <div>
          <h3>{{locale.t(item[0])}}</h3>
          <p>{{locale.t(item[1])}}</p>
        </div>
      </li>
    </ol>
  </section>
</template>
