<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { ChevronLeft, ChevronRight, Pencil, Play } from '@lucide/vue'
import { useLocaleStore } from '@/stores/locale'
import { useAdminStore } from '@/features/admin/store'
import { useVisualStyleStore } from '@/features/visual-editor/store'
import { useHomeMediaStore } from '@/features/home-media/store'
import { capabilities } from '@/data/home26'
import { clips, isShown, videoFile, videoEmbed, showcaseWords } from '../services/showcase'
const emit=defineEmits<{'edit-settings':[]}>()
const locale=useLocaleStore(),admin=useAdminStore(),visual=useVisualStyleStore(),homeMedia=useHomeMediaStore()
const words=computed(()=>showcaseWords(locale.locale)),index=ref(0),started=ref(false),local=ref(false),failed=ref(false)
const selected=computed(()=>clips[index.value]!)
const source=computed(()=>videoFile(visual.profile,selected.value.id,selected.value.file))
const embed=computed(()=>local.value?null:videoEmbed(visual.profile,selected.value.id))
watch(()=>locale.locale,value=>{index.value=Math.max(0,clips.findIndex(c=>c.id===value))},{immediate:true})
watch([index,source,embed],()=>{started.value=false;failed.value=false})
function select(i:number){local.value=false;index.value=(i+clips.length)%clips.length;started.value=false;failed.value=false}
</script>
<template>
 <section v-if="isShown(visual.profile,'home.videos')" data-style-id="home.videos" class="home-section space-y-3" aria-roledescription="carousel" :aria-label="words.title">
  <div class="flex flex-wrap items-center justify-between gap-2"><h2 data-style-id="home.videos.title" data-editable-text="true" class="text-xl font-black">{{visual.resolveText('home.videos.title',locale.locale,words.title)}}</h2><button v-if="admin.identity" type="button" class="mini-action" @click="emit('edit-settings')"><Pencil :size="16"/>{{words.edit}}</button></div>
  <div class="overflow-hidden rounded-2xl border border-[var(--c-border)] bg-[var(--c-surface)]">
   <div class="relative aspect-video bg-slate-950" data-video-frame>
    <template v-if="embed">
     <iframe v-if="started" :key="embed" :src="embed" :title="words.title+' · '+selected.label" class="absolute inset-0 h-full w-full border-0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"/>
     <button v-else type="button" class="absolute inset-0 flex items-center justify-center gap-3 text-lg font-bold text-white" @click="started=true"><Play :size="40"/>{{words.play}} · {{selected.label}}</button>
    </template>
    <video v-else :key="source" :src="source" :poster="homeMedia.resolve('capability.production',capabilities[0]!.image)" class="absolute inset-0 h-full w-full object-contain" controls playsinline preload="none" :aria-label="words.play+' · '+selected.label" @error="failed=true"/>
   </div>
   <div class="flex items-center gap-2 p-2 sm:p-3">
    <button type="button" class="mini-action !min-h-12 !px-2" :aria-label="words.previous" @click="select(index-1)"><ChevronRight :size="20"/></button>
    <div class="flex min-w-0 flex-1 overflow-hidden rounded-xl border border-[var(--c-border)]" role="group" :aria-label="locale.t('language')"><button v-for="(clip,i) in clips" :key="clip.id" type="button" class="min-h-12 min-w-0 flex-1 px-1 text-sm font-bold" :class="index===i?'bg-[var(--c-primary)] text-white':'bg-[var(--c-surface-2)]'" :aria-pressed="index===i" @click="select(i)">{{clip.label}}</button></div>
    <button type="button" class="mini-action !min-h-12 !px-2" :aria-label="words.next" @click="select(index+1)"><ChevronLeft :size="20"/></button>
   </div>
   <p class="sr-only" aria-live="polite">{{index+1}} / 3 · {{selected.label}}</p>
   <div v-if="embed||failed" class="flex flex-wrap items-center gap-2 px-3 pb-3"><p v-if="failed" role="alert" class="text-sm">{{words.error}}</p><button v-if="embed" type="button" class="mini-action" @click="local=true">{{words.local}}</button><a :href="source" class="mini-action" target="_blank" rel="noopener">{{words.direct}}</a></div>
  </div>
 </section>
</template>
