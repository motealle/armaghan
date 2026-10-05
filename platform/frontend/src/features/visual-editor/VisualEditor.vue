<script setup lang="ts">
import { computed, ref, toRef } from 'vue'
import { ImagePlus, ListTree, X } from '@lucide/vue'
import { useLocaleStore } from '@/stores/locale'
import { homeImageTarget, homeImageTargets } from '@/features/home-media/targets'
import { useVisualStyleStore } from './store'
import VisualEditorInspector from './VisualEditorInspector.vue'
import VisualEditorTargetChooser from './VisualEditorTargetChooser.vue'
import VisualEditorTargetBrowser from './VisualEditorTargetBrowser.vue'
import VisualEditorSyncPanel from './VisualEditorSyncPanel.vue'
import HomeMediaPanel from '@/features/home-media/HomeMediaPanel.vue'
import { useVisualEditorSelection } from './composables/useVisualEditorSelection'
import { useResizableEditorSheet } from './composables/useResizableEditorSheet'
import { useVisualProfileSync } from './composables/useVisualProfileSync'

const visual=useVisualStyleStore()
const locale=useLocaleStore()
const browserOpen=ref(true)
const enabledRef=toRef(visual,'enabled')
const {selected,candidates,choose,chooseHidden}=useVisualEditorSelection(enabledRef)
const {height,onResizeStart,onResizeMove,onResizeEnd}=useResizableEditorSheet(enabledRef)
const sync=useVisualProfileSync(visual,enabledRef)
const publishedStagingVersion=computed(()=>sync.publications.value[sync.channel.value]?.version)
const homeMediaTarget=computed(()=>homeImageTarget(selected.value?.id??''))
const imageHeading=computed(()=>({fa:'تغییر سریع عکس پنل‌ها',en:'Change panel images',ar:'تغيير صور الأقسام',ku:'گۆڕینی وێنەکان'})[locale.locale])
function chooseImage(item:typeof homeImageTargets[number]){
  choose({id:item.id,label:item.labels[locale.locale],textEditable:false,tag:'media'})
  browserOpen.value=false
}

const hiddenIds=computed(()=>Object.entries(visual.profile.styles)
  .filter(([,style])=>style.hidden)
  .map(([id])=>id),
)

function disableEditor(){
  visual.setEnabled(false)
}
function chooseFromBrowser(candidate:Parameters<typeof choose>[0]){
  choose(candidate)
  browserOpen.value=false
}
</script>

<template>
  <section
    v-if="visual.enabled"
    class="visual-editor-sheet"
    data-visual-editor-ui
    :style="{height:`${height}px`}"
    aria-label="ویرایش دیداری صفحه"
  >
    <button
      type="button"
      class="visual-editor-handle"
      aria-label="تغییر ارتفاع پنل"
      @pointerdown="onResizeStart"
      @pointermove="onResizeMove"
      @pointerup="onResizeEnd"
      @pointercancel="onResizeEnd"
    ><span></span></button>

    <header class="visual-editor-header">
      <div>
        <b>ویرایش دیداری</b>
        <small>{{selected?.label ?? (candidates.length?'انتخاب عنصر':'یک بخش از صفحه را لمس کنید')}}</small>
      </div>
      <span class="visual-editor-autosave" :data-state="sync.state.value">{{sync.statusLabel.value}}</span>
      <button
        type="button"
        class="visual-editor-icon-button"
        :class="{active:browserOpen}"
        aria-label="فهرست عناصر قابل ویرایش"
        title="فهرست عناصر"
        @click="browserOpen=!browserOpen"
      ><ListTree :size="19"/></button>
      <button type="button" class="visual-editor-icon-button" aria-label="خاموش کردن ادیتور" @click="disableEditor">
        <X :size="19"/>
      </button>
    </header>

    <div class="visual-editor-body">
      <section class="mb-3 space-y-2">
        <b class="flex items-center gap-2 text-sm"><ImagePlus :size="18"/>{{imageHeading}}</b>
        <div class="flex flex-wrap gap-2">
          <button v-for="item in homeImageTargets" :key="item.id" type="button" class="mini-action" :aria-pressed="homeMediaTarget===item.target" @click="chooseImage(item)">{{item.labels[locale.locale]}}</button>
        </div>
      </section>
      <HomeMediaPanel v-if="!candidates.length&&selected&&homeMediaTarget" :target="homeMediaTarget" :publication-channel="sync.channel.value"/>
      <label class="form-field mb-3">مقصد انتشار<select v-model="sync.channel.value"><option value="production">دامنه اصلی</option><option value="staging">نسخه آزمایشی</option></select></label>
      <VisualEditorSyncPanel
        :state="sync.state.value"
        :authenticated="sync.authenticated.value"
        :message="sync.message.value"
        :versions="sync.versions.value"
        :published-version="publishedStagingVersion"
        :channel="sync.channel.value"
        :can-publish="sync.canPublish.value"
        @reconnect="sync.connect(true)"
        @publish="sync.publishStaging"
        @restore="sync.restoreVersion"
        @use-server="sync.useServerVersion"
        @keep-local="sync.keepLocalVersion"
      />

      <VisualEditorTargetBrowser
        v-if="browserOpen"
        :selected-id="selected?.id"
        @choose="chooseFromBrowser"
      />

      <VisualEditorTargetChooser
        :candidates="candidates"
        :hidden-ids="hiddenIds"
        @choose="choose"
        @choose-hidden="chooseHidden"
      />

      <VisualEditorInspector v-if="!candidates.length&&selected" :selected="selected"/>

      <section v-else-if="!candidates.length" class="visual-editor-empty">
        <b>صفحه هنوز قابل لمس و اسکرول است.</b>
        <p>روی هر بخش قابل ویرایش بزنید. اگر چند عنصر نزدیک باشند، اول از شما می‌پرسیم کدام را می‌خواهید.</p>
      </section>
    </div>
  </section>
</template>
