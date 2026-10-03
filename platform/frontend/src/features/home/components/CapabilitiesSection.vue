<script setup lang="ts">
import { computed, ref } from 'vue'
import { ArrowUpLeft, Factory, FileBadge2, PackageCheck } from '@lucide/vue'
import AdaptivePanel from '@/components/ui/AdaptivePanel.vue'
import SmartImage from '@/components/media/SmartImage.vue'
import { useResolvedAppearance } from '@/composables/useResolvedAppearance'
import { capabilities } from '@/data/home26'
import { useLocaleStore } from '@/stores/locale'
import { useVisualStyleStore } from '@/features/visual-editor/store'

const locale=useLocaleStore()
const visual=useVisualStyleStore()
const {policy}=useResolvedAppearance()
const selectedId=ref<string|null>(null)
const icons={production:Factory,export:PackageCheck,trade:FileBadge2}
const selected=computed(()=>capabilities.find(item=>item.id===selectedId.value)??null)
</script>

<template>
  <section data-style-id="home.capabilities" data-style-label="بخش توانمندی‌ها" class="home-section test26-capabilities">
    <div v-show="visual.profile.styles['home.capabilities.heading']?.hidden===false||policy.showCapabilitiesIntro" data-style-id="home.capabilities.heading" data-style-label="سربرگ توانمندی‌ها" class="test26-section-heading">
      <span data-style-id="home.capabilities.eyebrow" data-style-label="بالانویس توانمندی‌ها" data-editable-text="true">{{visual.resolveText('home.capabilities.eyebrow',locale.locale,locale.t('capabilitiesEyebrow'))}}</span>
      <h2 data-style-id="home.capabilities.title" data-style-label="عنوان توانمندی‌ها" data-editable-text="true">{{visual.resolveText('home.capabilities.title',locale.locale,locale.t('capabilitiesTitle'))}}</h2>
      <p data-style-id="home.capabilities.intro" data-style-label="مقدمه توانمندی‌ها" data-editable-text="true">{{visual.resolveText('home.capabilities.intro',locale.locale,locale.t('capabilitiesIntro'))}}</p>
    </div>

    <div data-style-id="home.capabilities.grid" data-style-label="شبکه توانمندی‌ها" class="test26-capability-grid">
      <article v-for="item in capabilities" :key="item.id" :data-style-id="`home.capability.${item.id}`" :data-style-label="`کارت ${locale.t(item.titleKey)}`" class="test26-capability-card">
        <div class="test26-capability-media">
          <SmartImage :src="item.image" :fallback-src="item.fallback" :alt="locale.t(item.titleKey)" :label="locale.t(item.titleKey)" aspect="hero"/>
        </div>
        <div :data-style-id="`home.capability.${item.id}.body`" :data-style-label="`پنل متن ${locale.t(item.titleKey)}`" class="test26-capability-body">
          <div class="test26-capability-icon"><component :is="icons[item.id]" :size="22"/></div>
          <h3
            :data-style-id="`home.capability.${item.id}.title`"
            :data-style-label="`عنوان ${locale.t(item.titleKey)}`"
            data-editable-text="true"
          >{{visual.resolveText(`home.capability.${item.id}.title`,locale.locale,locale.t(item.titleKey))}}</h3>
          <p
            :data-style-id="`home.capability.${item.id}.text`"
            :data-style-label="`متن ${locale.t(item.titleKey)}`"
            data-editable-text="true"
          >{{visual.resolveText(`home.capability.${item.id}.text`,locale.locale,locale.t(item.summaryKey))}}</p>
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
