<script setup lang="ts">
import { computed, ref } from 'vue'
import { ArrowUpLeft, Factory, FileBadge2, PackageCheck } from '@lucide/vue'
import AdaptivePanel from '@/components/ui/AdaptivePanel.vue'
import SmartImage from '@/components/media/SmartImage.vue'
import { capabilities } from '@/data/home26'
import { useLocaleStore } from '@/stores/locale'

const locale=useLocaleStore()
const selectedId=ref<string|null>(null)
const icons={production:Factory,export:PackageCheck,trade:FileBadge2}
const selected=computed(()=>capabilities.find(item=>item.id===selectedId.value)??null)
</script>

<template>
  <section class="home-section test26-capabilities">
    <div class="test26-section-heading">
      <span>{{locale.t('capabilitiesEyebrow')}}</span>
      <h2>{{locale.t('capabilitiesTitle')}}</h2>
      <p>{{locale.t('capabilitiesIntro')}}</p>
    </div>

    <div class="test26-capability-grid">
      <article v-for="item in capabilities" :key="item.id" class="test26-capability-card">
        <div class="test26-capability-media">
          <SmartImage :src="item.image" :fallback-src="item.fallback" :alt="locale.t(item.titleKey)" :label="locale.t(item.titleKey)" aspect="hero"/>
        </div>
        <div class="test26-capability-body">
          <div class="test26-capability-icon"><component :is="icons[item.id]" :size="22"/></div>
          <h3>{{locale.t(item.titleKey)}}</h3>
          <p>{{locale.t(item.summaryKey)}}</p>
          <button type="button" class="test26-text-link" @click="selectedId=item.id">
            {{locale.t('capabilityMore')}} <ArrowUpLeft :size="16"/>
          </button>
        </div>
      </article>
    </div>

    <AdaptivePanel :open="Boolean(selected)" :title="selected?locale.t(selected.titleKey):''" wide @close="selectedId=null">
      <div v-if="selected" class="test26-capability-detail">
        <SmartImage :src="selected.image" :fallback-src="selected.fallback" :alt="locale.t(selected.titleKey)" :label="locale.t(selected.titleKey)" aspect="hero"/>
        <p>{{locale.t(selected.summaryKey)}}</p>
        <ul>
          <li v-for="key in selected.detailKeys" :key="key">{{locale.t(key)}}</li>
        </ul>
      </div>
    </AdaptivePanel>
  </section>
</template>
