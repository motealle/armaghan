<script setup lang="ts">
import { computed } from 'vue'
import { ArrowRight, Box, RotateCcw, Tag, WandSparkles } from '@lucide/vue'
import { categories } from '@/data/catalog'
import { requestPathTitle } from '@/services/whatsapp'
import { useOrderWizardStore } from '@/stores/orderWizard'
import WhatsAppIcon from '@/components/icons/WhatsAppIcon.vue'
import { useLocaleStore } from '@/stores/locale'

const wizard=useOrderWizardStore()
const locale=useLocaleStore()
const paths=computed(()=>[
  {id:'custom' as const,label:locale.t('customProduction'),desc:locale.t('customProductionDesc'),icon:WandSparkles},
  {id:'brand' as const,label:locale.t('privateLabel'),desc:locale.t('privateLabelDesc'),icon:Tag},
  {id:'packaging' as const,label:locale.t('customPackaging'),desc:locale.t('customPackagingDesc'),icon:Box},
])
const title=computed(()=>wizard.path?requestPathTitle(wizard.path,locale.locale):locale.t('productionTitle'))
const lockedSpecs=computed(()=>wizard.specSource?.specs.locked.map(locale.specLabel)??[])
const negotiableSpecs=computed(()=>wizard.specSource?.specs.negotiable.map(locale.specLabel)??[])
</script>

<template>
  <section class="rounded-3xl border border-[var(--c-border)] bg-[var(--c-surface)] p-4 shadow-sm md:p-5">
    <div class="mb-4 flex items-center gap-2">
      <div>
        <h2 class="text-xl font-extrabold leading-tight text-[var(--c-text)]">{{title}}</h2>
        <p class="mt-1 text-sm leading-6 text-[var(--c-muted)]">{{locale.t('wizardHelp')}}</p>
      </div>
      <button v-if="wizard.step>0" class="ms-auto inline-flex min-h-10 items-center gap-1 rounded-xl border border-[var(--c-border)] bg-[var(--c-surface)] px-3 text-xs font-bold text-[var(--c-text)]" @click="wizard.reset">
        <RotateCcw :size="16"/> {{locale.t('startOver')}}
      </button>
    </div>

    <div class="mb-5 grid grid-cols-4 gap-1.5">
      <i v-for="step in 4" :key="step" class="h-1.5 rounded-full" :class="step-1<=wizard.step?'bg-[var(--c-primary)]':'bg-[var(--c-border)]'"/>
    </div>

    <div v-if="wizard.step===0" class="grid gap-3 md:grid-cols-3">
      <button v-for="item in paths" :key="item.id" class="wizard-option" @click="wizard.choosePath(item.id)">
        <span class="wizard-option-icon"><component :is="item.icon" :size="22"/></span>
        <span><b class="block text-sm text-[var(--c-text)]">{{item.label}}</b><small class="mt-1 block text-[11px] leading-5 text-[var(--c-muted)]">{{item.desc}}</small></span>
      </button>
    </div>

    <div v-else-if="wizard.step===1">
      <h3 class="mb-3 text-sm font-extrabold text-[var(--c-text)]">{{locale.t('chooseMainCategory')}}</h3>
      <div class="grid grid-cols-3 gap-2">
        <button v-for="category in categories" :key="category.code" class="wizard-option block p-4" @click="wizard.chooseCategory(category.code)">
          <span class="text-[10px] font-extrabold text-[var(--c-primary)]">0{{category.code}}</span>
          <b class="mt-2 block text-sm text-[var(--c-text)]">{{locale.categoryName(category.code,category.name)}}</b>
          <small class="mt-1 block text-[10px] leading-5 text-[var(--c-muted)]">{{locale.categorySubtitle(category.code,category.subtitle)}}</small>
        </button>
      </div>
    </div>

    <div v-else-if="wizard.step===2&&wizard.category">
      <h3 class="mb-3 text-sm font-extrabold text-[var(--c-text)]">{{locale.t('chooseSubcategory')}}</h3>
      <div class="grid gap-2 sm:grid-cols-2">
        <button v-for="subcategory in wizard.category.subcategories" :key="subcategory.code" class="wizard-option block p-4" @click="wizard.chooseSubcategory(subcategory.code)">
          <b class="text-sm text-[var(--c-text)]">{{subcategory.code}} · {{locale.subcategoryName(subcategory.code,subcategory.name)}}</b>
          <small class="mt-1 block text-[var(--c-muted)]">{{locale.categoryName(wizard.category.code,wizard.category.name)}}</small>
        </button>
      </div>
    </div>

    <div v-else-if="wizard.step===3&&wizard.specSource" class="space-y-4">
      <div>
        <h3 class="mb-3 text-sm font-extrabold text-[var(--c-text)]">{{locale.t('subcategorySpecs')}}</h3>
        <div class="grid grid-cols-2 gap-2">
          <div v-for="item in lockedSpecs" :key="'l-'+item" class="spec-tile locked">
            <b class="text-xs">{{item}}</b><small class="mt-1 block text-[10px]">🔒 {{locale.t('locked')}}</small>
          </div>
          <div v-for="item in negotiableSpecs" :key="'n-'+item" class="spec-tile negotiable">
            <b class="text-xs">{{item}}</b><small class="mt-1 block text-[10px]">{{locale.t('negotiable')}}</small>
          </div>
        </div>
      </div>

      <label class="form-field">
        {{locale.t('sellerNote')}}
        <textarea v-model="wizard.note" rows="3"/>
      </label>

      <div class="message-preview">
        <div class="mb-2 text-xs font-extrabold text-[var(--c-secondary)]">{{locale.t('messagePreview')}}</div>
        <pre class="whitespace-pre-wrap font-sans text-xs leading-6 text-[var(--c-text)]">{{wizard.preview}}</pre>
      </div>

      <a :href="wizard.whatsapp" target="_blank" rel="noopener" class="wa-primary flex min-h-14 items-center justify-center gap-2 rounded-xl px-4 text-sm font-extrabold">
        <WhatsAppIcon :size="28" tone="white"/> {{locale.t('continueWhatsApp')}}
      </a>
    </div>

    <button v-if="wizard.step>0" class="mt-4 inline-flex min-h-10 items-center gap-2 rounded-xl border border-[var(--c-border)] bg-[var(--c-surface)] px-3 text-xs font-bold text-[var(--c-text)]" @click="wizard.back">
      <ArrowRight :size="16"/> {{locale.t('back')}}
    </button>
  </section>
</template>
