<script setup lang="ts">
import { computed } from 'vue'
import { Eye, EyeOff, LockKeyhole } from '@lucide/vue'
import {
  visualTargetById,
  visualTargetGroups,
  type VisualTargetDefinition,
} from './targetRegistry'
import type { EditableCandidate } from './selection'
import { useVisualStyleStore } from './store'

const props=defineProps<{selectedId?:string}>()
const emit=defineEmits<{choose:[candidate:EditableCandidate]}>()
const visual=useVisualStyleStore()

const hiddenIds=computed(()=>new Set(
  Object.entries(visual.profile.styles)
    .filter(([,style])=>style.hidden)
    .map(([id])=>id),
))

const orphanHiddenTargets=computed<VisualTargetDefinition[]>(()=>
  [...hiddenIds.value]
    .filter(id=>!visualTargetById.has(id))
    .map(id=>({
      id,
      label:`عنصر مخفی ${id}`,
      kind:'panel',
    })),
)

function choose(target:VisualTargetDefinition){
  emit('choose',{
    id:target.id,
    label:target.label,
    textEditable:Boolean(target.textEditable),
    tag:target.kind,
  })
}

function toggleVisibility(target:VisualTargetDefinition){
  if(target.hideable===false)return
  const hidden=hiddenIds.value.has(target.id)
  visual.patchStyle(target.id,{hidden:!hidden})
}
</script>

<template>
  <section class="visual-editor-target-browser">
    <div class="visual-editor-browser-heading">
      <b>انتخاب منظم عناصر</b>
      <small>بخش، پنل، عنوان یا متن را دقیق انتخاب کنید؛ عنصر مخفی هم از همین فهرست قابل بازیابی است.</small>
    </div>

    <details v-for="group in visualTargetGroups" :key="group.id" class="visual-editor-target-group">
      <summary>{{group.label}}</summary>
      <div class="visual-editor-target-list">
        <div
          v-for="target in group.targets"
          :key="target.id"
          class="visual-editor-target-row"
          :class="{active:selectedId===target.id}"
        >
          <button type="button" class="visual-editor-target-select" @click="choose(target)">
            <b>{{target.label}}</b>
            <small>{{target.kind}} · {{target.id}}</small>
          </button>
          <button
            v-if="target.hideable!==false"
            type="button"
            class="visual-editor-target-visibility"
            :aria-label="hiddenIds.has(target.id)?'نمایش عنصر':'مخفی کردن عنصر'"
            :title="hiddenIds.has(target.id)?'نمایش عنصر':'مخفی کردن عنصر'"
            @click="toggleVisibility(target)"
          >
            <Eye v-if="hiddenIds.has(target.id)" :size="17"/>
            <EyeOff v-else :size="17"/>
          </button>
          <span v-else class="visual-editor-target-fixed" title="این ریشه برای جلوگیری از ناپدیدشدن کل ادیتور قابل مخفی‌سازی نیست">
            <LockKeyhole :size="16"/>
          </span>
        </div>
      </div>
    </details>

    <details v-if="orphanHiddenTargets.length" class="visual-editor-target-group" open>
      <summary>عناصر مخفی دیگر</summary>
      <div class="visual-editor-target-list">
        <div v-for="target in orphanHiddenTargets" :key="target.id" class="visual-editor-target-row">
          <button type="button" class="visual-editor-target-select" @click="choose(target)">
            <b>{{target.label}}</b>
            <small>{{target.id}}</small>
          </button>
          <button type="button" class="visual-editor-target-visibility" aria-label="نمایش عنصر" @click="toggleVisibility(target)">
            <Eye :size="17"/>
          </button>
        </div>
      </div>
    </details>
  </section>
</template>
