<script setup lang="ts">
import { onBeforeUnmount, watch } from 'vue'
import { X } from '@lucide/vue'

const props = defineProps<{ open: boolean; title: string }>()
const emit = defineEmits<{ close: [] }>()

function onKey(event: KeyboardEvent) {
  if (props.open && event.key === 'Escape') emit('close')
}

watch(() => props.open, (open) => {
  document.body.style.overflow = open ? 'hidden' : ''
  if (open) window.addEventListener('keydown', onKey)
  else window.removeEventListener('keydown', onKey)
}, { immediate: true })

onBeforeUnmount(() => {
  document.body.style.overflow = ''
  window.removeEventListener('keydown', onKey)
})
</script>

<template>
  <Teleport to="body">
    <div v-if="open" class="sheet-layer" role="presentation">
      <button class="sheet-backdrop" aria-label="بستن" @click="emit('close')" />
      <section class="sheet-panel" role="dialog" aria-modal="true" :aria-label="title">
        <div class="mx-auto mt-2 h-1 w-11 rounded-full bg-slate-300 md:hidden" />
        <header class="sticky top-0 z-10 flex items-center gap-3 border-b border-slate-100 bg-[var(--c-paper)] px-4 py-3">
          <h2 class="text-base font-extrabold">{{ title }}</h2>
          <button class="ms-auto grid h-10 w-10 place-items-center rounded-full border border-slate-200" aria-label="بستن" @click="emit('close')">
            <X :size="20" />
          </button>
        </header>
        <div class="p-4"><slot /></div>
      </section>
    </div>
  </Teleport>
</template>
