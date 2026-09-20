<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue'
import { RotateCcw, Save, Search } from '@lucide/vue'
import { useLocaleStore } from '@/stores/locale'
import type { Locale } from '@/services/localeDetection'

const locale=useLocaleStore()
const target=ref<Locale>('fa')
const group=ref('navigation')
const query=ref('')
const selected=ref<string[]>([])
const drafts=reactive<Record<string,string>>({})

const groups=computed(()=>locale.translationGroups)
const keys=computed(()=>{
  const source=groups.value.find(item=>item.id===group.value)?.keys??Object.keys(locale.baseMessages[target.value]??{})
  const needle=query.value.trim().toLowerCase()
  return source.filter(key=>{
    const base=locale.baseMessages[target.value]?.[key]??''
    const custom=locale.overrides[target.value]?.[key]??''
    return !needle||key.toLowerCase().includes(needle)||base.toLowerCase().includes(needle)||custom.toLowerCase().includes(needle)
  })
})
function sync(){
  for(const key of Object.keys(drafts))delete drafts[key]
  for(const key of keys.value)drafts[key]=locale.overrides[target.value]?.[key]??locale.baseMessages[target.value]?.[key]??''
  selected.value=[]
}
watch([target,group,query],sync,{immediate:true})
function save(key:string){locale.setOverride(target.value,key,drafts[key]??'')}
function reset(key:string){
  locale.resetOverride(target.value,key)
  drafts[key]=locale.baseMessages[target.value]?.[key]??''
  selected.value=selected.value.filter(item=>item!==key)
}
function resetSelected(){
  locale.resetOverrides(target.value,selected.value)
  for(const key of selected.value)drafts[key]=locale.baseMessages[target.value]?.[key]??''
  selected.value=[]
}
function toggleAll(){
  selected.value=selected.value.length===keys.value.length?[]:[...keys.value]
}
</script>

<template>
  <section class="space-y-4">
    <div>
      <h2 class="text-xl font-black text-[var(--c-text)]">{{locale.t('translationsTitle')}}</h2>
      <p class="mt-1 text-sm leading-6 text-[var(--c-muted)]">{{locale.t('translationsHelp')}}</p>
    </div>

    <div class="grid gap-2 rounded-2xl border border-[var(--c-border)] bg-[var(--c-surface)] p-3 md:grid-cols-[9rem_12rem_1fr]">
      <label class="form-field">{{locale.t('language')}}
        <select v-model="target"><option value="fa">فارسی</option><option value="ar">العربية</option><option value="en">English</option><option value="ku">کوردی</option></select>
      </label>
      <label class="form-field">{{locale.t('translationGroup')}}
        <select v-model="group"><option v-for="item in groups" :key="item.id" :value="item.id">{{item.label}}</option></select>
      </label>
      <label class="form-field">{{locale.t('translationSearch')}}
        <span class="relative block"><Search :size="16" class="pointer-events-none absolute start-3 top-1/2 -translate-y-1/2 text-[var(--c-muted)]"/><input v-model="query" class="ps-9"/></span>
      </label>
    </div>

    <div v-if="selected.length" class="batch-bar">
      <b>{{selected.length}} {{locale.t('selectedCount')}}</b>
      <button class="ms-auto mini-action" @click="resetSelected"><RotateCcw :size="15"/>{{locale.t('bulkReset')}}</button>
    </div>

    <div class="flex items-center gap-2 text-xs text-[var(--c-muted)]">
      <input type="checkbox" :checked="keys.length>0&&selected.length===keys.length" @change="toggleAll">
      <span>{{locale.t('selectAll')}}</span>
    </div>

    <div class="grid gap-2">
      <article v-for="key in keys" :key="key" class="translation-row">
        <div class="flex items-center gap-2">
          <input v-model="selected" type="checkbox" :value="key">
          <code class="rounded-md bg-[var(--c-surface-2)] px-2 py-1 text-[11px] text-[var(--c-primary)]">{{key}}</code>
          <span v-if="locale.overrides[target]?.[key]" class="ms-auto rounded-full bg-[color-mix(in_srgb,var(--c-secondary)_10%,var(--c-surface))] px-2 py-1 text-[10px] font-bold text-[var(--c-secondary)]">{{locale.t('customValue')}}</span>
        </div>
        <div class="grid gap-2 md:grid-cols-2">
          <label class="form-field">{{locale.t('baseValue')}}<textarea :value="locale.baseMessages[target]?.[key]??''" rows="2" readonly/></label>
          <label class="form-field">{{locale.t('customValue')}}<textarea v-model="drafts[key]" rows="2"/></label>
        </div>
        <div class="flex justify-end gap-2">
          <button class="mini-action" @click="reset(key)"><RotateCcw :size="15"/>{{locale.t('resetTranslation')}}</button>
          <button class="mini-action bg-[var(--c-primary)] text-white" @click="save(key)"><Save :size="15"/>{{locale.t('save')}}</button>
        </div>
      </article>
    </div>
  </section>
</template>
