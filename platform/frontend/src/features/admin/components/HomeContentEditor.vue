<script setup lang="ts">
import { reactive, ref, watch } from 'vue'
import { RotateCcw, Save } from '@lucide/vue'
import { useLocaleStore } from '@/stores/locale'
import type { Locale } from '@/services/localeDetection'

const locale=useLocaleStore()
const target=ref<Locale>('fa')
const saved=ref(false)
const fields=[
  {key:'manufacturer',rows:1},
  {key:'brandIntro',rows:1},
  {key:'brandCapabilities',rows:1},
  {key:'brandCapabilitiesText',rows:3},
  {key:'brandDocuments',rows:1},
  {key:'brandDocumentsText',rows:3},
  {key:'brandSales',rows:1},
  {key:'brandSalesText',rows:3},
] as const
const drafts=reactive<Record<string,string>>({})

function sync(){
  for(const item of fields)drafts[item.key]=locale.overrides[target.value]?.[item.key]??locale.baseValue(item.key,target.value)
  saved.value=false
}
watch(target,sync,{immediate:true})
function saveAll(){
  for(const item of fields)locale.setOverride(target.value,item.key,drafts[item.key]??'')
  saved.value=true
}
function reset(key:string){
  locale.resetOverride(target.value,key)
  drafts[key]=locale.baseValue(key,target.value)
  saved.value=false
}
</script>

<template>
  <section class="space-y-4">
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
  </section>
</template>
