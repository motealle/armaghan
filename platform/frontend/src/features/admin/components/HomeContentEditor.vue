<script setup lang="ts">
import { reactive, ref, watch } from 'vue'
import { RotateCcw, Save } from '@lucide/vue'
import {useVisualStyleStore} from '@/features/visual-editor/store'
import {translationTarget} from '@/features/visual-editor/translationTarget'
import {useVisualProfileSync} from '@/features/visual-editor/composables/useVisualProfileSync'
import VisualEditorSyncPanel from '@/features/visual-editor/VisualEditorSyncPanel.vue'
import { useLocaleStore } from '@/stores/locale'
import type { Locale } from '@/services/localeDetection'

const props=defineProps<{live?:boolean}>()
const locale=useLocaleStore()
const visual=useVisualStyleStore()
const sync=useVisualProfileSync(visual,ref(!!props.live))
function currentText(key:string){return props.live?(visual.textOverride(translationTarget(key),target.value)??locale.baseValue(key,target.value)):(locale.overrides[target.value]?.[key]??locale.baseValue(key,target.value))}
function override(key:string,value:string){if(props.live)visual.setText(translationTarget(key),target.value,value);else locale.setOverride(target.value,key,value)}
function clearOverride(key:string){if(props.live)visual.setText(translationTarget(key),target.value,'');else locale.resetOverride(target.value,key)}
const target=ref<Locale>('fa')
const saved=ref(false)
const fields=[
  {key:'manufacturer',rows:1},
  {key:'heroSingleSlogan',rows:2},
  {key:'aboutArmaghanTitle',rows:1},
  {key:'aboutArmaghanText',rows:5},
  {key:'whyArmaghanTitle',rows:1},
  {key:'whyArmaghanIntro',rows:2},
  {key:'whyCapacityTitle',rows:1},
  {key:'whyCapacityText',rows:3},
  {key:'whyCustomTitle',rows:1},
  {key:'whyCustomText',rows:3},
  {key:'whyDirectTitle',rows:1},
  {key:'whyDirectText',rows:3},
  {key:'whyMarketTitle',rows:1},
  {key:'whyMarketText',rows:3},
  {key:'capabilitiesTitle',rows:1},
  {key:'capabilitiesIntro',rows:3},
  {key:'productBannersTitle',rows:1},
  {key:'productBannersIntro',rows:2},
  {key:'brandIntro',rows:1},
  {key:'brandCapabilities',rows:1},
  {key:'brandCapabilitiesText',rows:3},
  {key:'brandDocuments',rows:1},
  {key:'brandDocumentsText',rows:3},
  {key:'brandSales',rows:1},
  {key:'brandSalesText',rows:3},
] as const
const drafts=reactive<Record<string,string>>({})

function syncDrafts(){
  for(const item of fields)drafts[item.key]=currentText(item.key)
  saved.value=false
}
watch(target,syncDrafts,{immediate:true})
async function saveAll(){
  for(const item of fields)override(item.key,drafts[item.key]??'')
  saved.value=props.live?await sync.saveNow():true
}
async function reset(key:string){
  clearOverride(key)
  drafts[key]=locale.baseValue(key,target.value)
  saved.value=false
  if(props.live)await sync.saveNow()
}
watch(()=>sync.state.value,(state)=>{if(props.live&&state==='synced')syncDrafts()})
</script>

<template>
  <section class="space-y-4">
    <VisualEditorSyncPanel v-if="live" :state="sync.state.value" :authenticated="sync.authenticated.value" :message="sync.message.value" :versions="sync.versions.value" :published-version="sync.publications.value[sync.channel.value]?.version" :channel="sync.channel.value" :can-publish="sync.canPublish.value" @reconnect="sync.connect(true)" @publish="sync.publishStaging" @restore="sync.restoreVersion" @use-server="sync.useServerVersion" @keep-local="sync.keepLocalVersion"/>
    <fieldset class="contents" :disabled="live&&(!sync.authenticated.value||['checking','saving','conflict'].includes(sync.state.value))">

    <div class="flex flex-wrap items-end gap-3">
      <div>
        <h2 class="text-xl font-black text-[var(--c-text)]">{{locale.t('brandIntro')}}</h2>
        <p class="mt-1 text-sm leading-6 text-[var(--c-muted)]">{{locale.t('translationsHelp')}}</p>
      </div>
      <label class="form-field ms-auto min-w-36">{{locale.t('language')}}
        <select v-model="target">
          <option value="fa">فارسی</option><option value="ar">العربية</option><option value="en">English</option><option value="ku">کوردی</option>
        </select>
      </label>
    </div>

    <div class="grid gap-3 lg:grid-cols-2">
      <article v-for="item in fields" :key="item.key" class="admin-surface rounded-2xl p-4">
        <div class="mb-2 flex items-center gap-2">
          <b class="text-sm">{{locale.baseValue(item.key,target)}}</b>
          <code class="ms-auto rounded bg-[var(--c-surface-2)] px-2 py-1 text-[10px] text-[var(--c-muted)]">{{item.key}}</code>
        </div>
        <textarea v-model="drafts[item.key]" :rows="item.rows" class="w-full resize-y rounded-xl border border-[var(--c-border)] bg-[var(--c-surface-2)] px-3 py-2 text-sm outline-none"/>
        <button class="mini-action mt-2" type="button" @click="reset(item.key)"><RotateCcw :size="14"/>{{locale.t('resetTranslation')}}</button>
      </article>
    </div>

    <div class="sticky bottom-2 flex justify-end">
      <button class="inline-flex min-h-11 items-center gap-2 rounded-xl bg-[var(--c-primary)] px-5 text-sm font-black text-white shadow-lg" type="button" @click="saveAll">
        <Save :size="17"/>{{saved?locale.t('save'):locale.t('save')}}
      </button>
    </div>
    </fieldset>
  </section>
</template>
