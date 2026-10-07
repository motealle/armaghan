<script setup lang="ts">
import { ChevronDown, Heart, MessageCircleMore, Search } from '@lucide/vue'
import { computed, ref } from 'vue'
import { helpSections, searchHelp } from '@/features/help/guide'
import BaseSheet from '@/components/ui/BaseSheet.vue'
import { useLocaleStore } from '@/stores/locale'

defineProps<{open:boolean}>()
const emit=defineEmits<{close:[]}>()
const locale=useLocaleStore()
const query=ref('')
const section=ref('all')
const results=computed(()=>searchHelp(query.value,section.value))
const words=computed(()=>({
  fa:{intro:'راهنمای استفاده و همکاری با ارمغان؛ موضوع را انتخاب کنید یا پرسش خود را جست‌وجو کنید.',search:'جست‌وجو در راهنما',all:'همه موضوع‌ها',section:'موضوع راهنما',empty:'پاسخی پیدا نشد؛ عبارت یا موضوع انتخاب‌شده را تغییر دهید.',count:'پرسش',language:''},
  en:{intro:'Choose a topic or search the guide.',search:'Search the guide',all:'All topics',section:'Guide topic',empty:'No answer found. Try another query or topic.',count:'questions',language:'The full guide is currently available in Persian.'},
  ar:{intro:'اختر موضوعاً أو ابحث في الدليل.',search:'البحث في الدليل',all:'جميع المواضيع',section:'موضوع الدليل',empty:'لم يتم العثور على إجابة. غيّر البحث أو الموضوع.',count:'أسئلة',language:'الدليل الكامل متاح حالياً باللغة الفارسية.'},
  ku:{intro:'بابەتێک هەڵبژێرە یان لە ڕێبەرەکە بگەڕێ.',search:'گەڕان لە ڕێبەر',all:'هەموو بابەتەکان',section:'بابەتی ڕێبەر',empty:'وەڵام نەدۆزرایەوە؛ گەڕان یان بابەت بگۆڕە.',count:'پرسیار',language:'ڕێبەری تەواو ئێستا بە فارسی بەردەستە.'},
})[locale.locale])
const steps=[
  {key:'helpStep1',icon:Search},
  {key:'helpStep2',icon:Heart},
  {key:'helpStep3',icon:MessageCircleMore},
]
</script>

<template>
  <BaseSheet :open="open" :title="locale.t('helpTitle')" @close="emit('close')">
    <div class="space-y-4">
      <p class="text-sm leading-7 text-[var(--c-muted)]">{{words.intro}}</p>
      <div v-if="locale.locale!=='fa'" class="grid gap-2">
        <div v-for="item in steps" :key="item.key" class="help-step">
          <span class="help-step-icon"><component :is="item.icon" :size="19"/></span>
          <span>{{locale.t(item.key)}}</span>
        </div>
      </div>
      <p v-if="words.language" class="text-xs leading-6 text-[var(--c-muted)]">{{words.language}}</p>
      <label class="flex min-h-11 items-center gap-2 rounded-xl border border-[var(--c-border)] px-3">
        <Search :size="18" aria-hidden="true"/>
        <input v-model="query" type="search" :aria-label="words.search" :placeholder="words.search" class="min-w-0 flex-1 bg-transparent py-3 text-sm outline-none" maxlength="100">
      </label>
      <select v-model="section" :aria-label="words.section" class="min-h-11 w-full rounded-xl border border-[var(--c-border)] bg-[var(--c-surface)] px-3 text-sm">
        <option value="all">{{words.all}}</option>
        <option v-for="topic in helpSections" :key="topic.id" :value="topic.id">{{topic.title}}</option>
      </select>
      <p class="text-xs text-[var(--c-muted)]" role="status">{{results.reduce((total,topic)=>total+topic.items.length,0)}} {{words.count}}</p>
      <p v-if="!results.length" class="py-4 text-sm text-[var(--c-muted)]">{{words.empty}}</p>
      <div dir="rtl" lang="fa" class="space-y-5 text-start">
        <section v-for="topic in results" :key="topic.id" :aria-labelledby="`help-topic-${topic.id}`">
          <h3 :id="`help-topic-${topic.id}`" class="mb-2 text-sm font-bold text-[var(--c-secondary)]">{{topic.title}}</h3>
          <div class="overflow-hidden rounded-xl border border-[var(--c-border)]">
            <details v-for="item in topic.items" :key="item.id" class="group border-b border-[var(--c-border)] last:border-b-0">
              <summary class="flex min-h-11 cursor-pointer list-none items-center justify-between gap-3 px-3 py-3 text-sm font-bold outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[var(--c-primary)] [&::-webkit-details-marker]:hidden">
                <span>{{item.question}}</span><ChevronDown :size="17" class="shrink-0 transition-transform group-open:rotate-180" aria-hidden="true"/>
              </summary>
              <p class="px-3 pb-4 text-sm leading-7 text-[var(--c-muted)]">{{item.answer}}</p>
            </details>
          </div>
        </section>
      </div>
    </div>
  </BaseSheet>
</template>
