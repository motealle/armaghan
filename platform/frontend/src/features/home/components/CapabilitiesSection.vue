<script setup lang="ts">
import {useHomeMediaStore} from '@/features/home-media/store'
const homeMedia=useHomeMediaStore();homeMedia.ensure()
import { computed, ref } from 'vue'
import { Pencil, ArrowUpLeft, Factory, FileBadge2, PackageCheck } from '@lucide/vue'
import AdaptivePanel from '@/components/ui/AdaptivePanel.vue'
import SmartImage from '@/components/media/SmartImage.vue'
import { useResolvedAppearance } from '@/composables/useResolvedAppearance'
import { capabilities } from '@/data/home26'
import { useLocaleStore } from '@/stores/locale'
import { useVisualStyleStore } from '@/features/visual-editor/store'
import HomeMediaEditButton from '@/features/home-media/HomeMediaEditButton.vue'

import { useAdminStore } from '@/features/admin/store'
import { capabilityDetails, isShown, showcaseWords } from '../services/showcase'
const admin=useAdminStore()
const emit=defineEmits<{'edit-settings':[]}>()
function editSettings(){selectedId.value=null;emit('edit-settings')}
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

    <button v-if="admin.identity" type="button" class="mini-action mb-3" @click="editSettings"><Pencil :size="16"/>{{showcaseWords(locale.locale).edit}}</button>
    <div data-style-id="home.capabilities.grid" data-style-label="شبکه توانمندی‌ها" class="test26-capability-grid">
      <article v-for="item in capabilities" :key="item.id" :data-style-id="`home.capability.${item.id}`" :data-style-label="`کارت ${locale.t(item.titleKey)}`" class="test26-capability-card">
        <div :data-style-id="`home.capability.${item.id}.media`" :data-style-label="`تصویر ${locale.t(item.titleKey)}`" class="test26-capability-media relative">
          <SmartImage v-if="homeMedia.ready" :src="homeMedia.resolve('capability.'+item.id,item.image)" :srcset="homeMedia.srcset('capability.'+item.id)" sizes="(min-width: 1024px) 33vw, 100vw" :fallback-src="item.fallback" :alt="locale.t(item.titleKey)" :label="locale.t(item.titleKey)" aspect="hero"/>
          <div v-else class="min-h-36 animate-pulse rounded-xl bg-[var(--c-surface-2)]" role="status" aria-label="در حال دریافت تصویر به‌روز"></div>
          <HomeMediaEditButton :target="'capability.'+item.id" :label="locale.t(item.titleKey)"/>
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

    <AdaptivePanel :open="Boolean(selected)" :title="selected?visual.resolveText(`home.capability.${selected.id}.title`,locale.locale,locale.t(selected.titleKey)):''" wide @close="selectedId=null">
      <div v-if="selected" class="test26-capability-detail">
        <SmartImage v-if="homeMedia.ready" :src="homeMedia.resolve('capability.'+selected.id,selected.image)" :srcset="homeMedia.srcset('capability.'+selected.id)" sizes="(min-width: 1024px) 900px, 100vw" :fallback-src="selected.fallback" :alt="locale.t(selected.titleKey)" :label="locale.t(selected.titleKey)" aspect="hero"/>
        <div v-else class="min-h-44 animate-pulse rounded-xl bg-[var(--c-surface-2)]" role="status" aria-label="در حال دریافت تصویر به‌روز"></div>
        <button v-if="admin.identity" type="button" class="mini-action" @click="editSettings"><Pencil :size="16"/>{{showcaseWords(locale.locale).edit}}</button>
        <p :data-style-id="`home.capability.${selected.id}.text`" data-editable-text="true">{{visual.resolveText(`home.capability.${selected.id}.text`,locale.locale,locale.t(selected.summaryKey))}}</p>
        <ul>
          <template v-for="item in capabilityDetails.filter(d=>d.group===selected!.id)" :key="item.id"><li v-if="isShown(visual.profile,item.id,item.defaultHidden)" :data-style-id="item.id" data-editable-text="true">{{visual.resolveText(item.id,locale.locale,locale.t(item.key))}}</li></template>
        </ul>
        <div v-if="selected.id==='trade'&&isShown(visual.profile,'home.capability.trade.detail.2')" data-style-id="home.capability.trade.official.media" class="relative min-h-16 rounded-xl border border-[var(--c-border)] p-3">
          <a v-if="homeMedia.images['document.official']" :href="homeMedia.images['document.official']" target="_blank" rel="noopener"><img :src="homeMedia.images['document.official']" :alt="showcaseWords(locale.locale).official" class="mx-auto max-h-[70vh] w-full object-contain" loading="lazy"></a>
          <HomeMediaEditButton @edit="selectedId=null" target="document.official" :label="showcaseWords(locale.locale).official"/>
        </div>
      </div>
    </AdaptivePanel>
  </section>
</template>
