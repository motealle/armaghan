<script setup lang="ts">
import { onBeforeUnmount, watch } from 'vue'
import { LogOut, X } from '@lucide/vue'
import { useSessionStore } from '@/stores/session'

const props = defineProps<{ open: boolean; title: string }>()
const emit = defineEmits<{ close: [] }>()
const session = useSessionStore()

function onKey(event: KeyboardEvent) {
  if (props.open && event.key === 'Escape') emit('close')
}

function logoutEverywhere() {
  session.logout()
  emit('close')
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
        <header class="sticky top-0 z-10 flex items-center gap-2 border-b border-slate-100 bg-[var(--c-paper)] px-4 py-3">
          <h2 class="min-w-0 flex-1 truncate text-base font-extrabold">{{ title }}</h2>
          <button
            v-if="session.isAuthenticated"
            class="inline-flex min-h-10 items-center gap-1 rounded-full border border-rose-100 bg-rose-50 px-3 text-xs font-bold text-rose-700"
            @click="logoutEverywhere"
          >
            <LogOut :size="17" /> خروج
          </button>
          <button class="grid h-10 w-10 place-items-center rounded-full border border-slate-200" aria-label="بستن" @click="emit('close')">
            <X :size="20" />
          </button>
        </header>
        <div class="p-4"><slot /></div>
      </section>
    </div>
  </Teleport>
</template>
