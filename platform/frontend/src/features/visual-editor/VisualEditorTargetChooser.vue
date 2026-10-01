<script setup lang="ts">
import type { EditableCandidate } from './selection'

defineProps<{
  candidates:EditableCandidate[]
  hiddenIds:string[]
}>()

const emit=defineEmits<{
  choose:[candidate:EditableCandidate]
  chooseHidden:[id:string]
}>()
</script>

<template>
  <section v-if="hiddenIds.length" class="mb-3">
    <div class="mb-1 text-[11px] font-black text-[var(--c-muted)]">عناصر مخفی‌شده</div>
    <div class="flex gap-2 overflow-x-auto pb-1">
      <button
        v-for="id in hiddenIds"
        :key="id"
        type="button"
        class="min-h-11 shrink-0 rounded-xl border border-[var(--c-border)] bg-[var(--c-surface)] px-3 text-xs font-bold"
        @click="emit('chooseHidden',id)"
      >{{id}}</button>
    </div>
  </section>

  <section v-if="candidates.length" class="visual-editor-candidates">
    <b>کدام بخش را می‌خواهید؟</b>
    <p>چند عنصر نزدیک به لمس شما پیدا شد.</p>
    <div>
      <button v-for="candidate in candidates" :key="candidate.id" type="button" @click="emit('choose',candidate)">
        <span>{{candidate.label}}</span>
        <small>{{candidate.tag}} · {{candidate.id}}</small>
      </button>
    </div>
  </section>
</template>
