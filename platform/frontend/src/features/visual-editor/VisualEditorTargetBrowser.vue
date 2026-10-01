<script setup lang="ts">
import { computed } from 'vue'
import { Eye, EyeOff } from '@lucide/vue'
import { visualTargetGroups, type VisualTargetDefinition } from './targetRegistry'
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

function choose(target:VisualTargetDefinition){
  emit('choose',{
    id:target.id,
    label:target.label,
    textEditable:Boolean(target.textEditable),
    tag:target.kind,
  })
}

function toggleVisibility(target:VisualTargetDefinition){
  const hidden=hiddenIds.value.has(target.id)
  visual.patchStyle(target.id,{hidden:!hidden})
}
</script>

<template>
  <section class="visual-editor-target-browser">
    <div class="visual-editor-browser-heading">
      <b>انتخاب منظم عناصر</b>
      <small>برای انتخاب دقیق، یا روی صفحه لمس کنید یا از این فهرست استفاده کنید.</small>
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
            type="button"
            class="visual-editor-target-visibility"
            :aria-label="hiddenIds.has(target.id)?'نمایش عنصر':'مخفی کردن عنصر'"
            :title="hiddenIds.has(target.id)?'نمایش عنصر':'مخفی کردن عنصر'"
            @click="toggleVisibility(target)"
          >
            <Eye v-if="hiddenIds.has(target.id)" :size="17"/>
            <EyeOff v-else :size="17"/>
          </button>
        </div>
      </div>
    </details>
  </section>
</template>
