<script setup lang="ts">
import { nextTick, onBeforeUnmount, ref, watch } from 'vue'
import { LogOut, X } from '@lucide/vue'
import { useSessionStore } from '@/stores/session'

const props = defineProps<{ open: boolean; title: string }>()
const emit = defineEmits<{ close: [] }>()
const session = useSessionStore()
const panelRef = ref<HTMLElement | null>(null)
let previousFocus: HTMLElement | null = null

const FOCUSABLE_SELECTOR = [
  'a[href]',
  'button:not([disabled])',
  'input:not([disabled])',
  'select:not([disabled])',
  'textarea:not([disabled])',
  '[tabindex]:not([tabindex="-1"])',
].join(',')

function focusableControls() {
  return panelRef.value ? Array.from(panelRef.value.querySelectorAll<HTMLElement>(FOCUSABLE_SELECTOR)) : []
}

function focusFirstControl() {
  const controls = focusableControls()
  ;(controls[0] ?? panelRef.value)?.focus({ preventScroll: true })
}

function onKey(event: KeyboardEvent) {
  if (!props.open) return
  if (event.key === 'Escape') {
    event.preventDefault()
    emit('close')
    return
  }
  if (event.key !== 'Tab') return

  const controls = focusableControls()
  if (!controls.length) {
    event.preventDefault()
    panelRef.value?.focus({ preventScroll: true })
    return
  }

  const first = controls[0]!
  const last = controls[controls.length - 1]!
  const active = document.activeElement
  if (event.shiftKey && (active === first || active === panelRef.value)) {
    event.preventDefault()
    last.focus()
  } else if (!event.shiftKey && active === last) {
    event.preventDefault()
    first.focus()
  }
}

function logoutEverywhere() {
  session.logout()
  emit('close')
}

watch(() => props.open, async (open, wasOpen) => {
  document.body.style.overflow = open ? 'hidden' : ''
  if (open) {
    previousFocus = document.activeElement instanceof HTMLElement ? document.activeElement : null
    window.addEventListener('keydown', onKey)
    await nextTick()
    focusFirstControl()
  } else {
    window.removeEventListener('keydown', onKey)
    if (wasOpen) {
      await nextTick()
      previousFocus?.focus({ preventScroll: true })
      previousFocus = null
    }
  }
}, { immediate: true })

onBeforeUnmount(() => {
  document.body.style.overflow = ''
  window.removeEventListener('keydown', onKey)
  if (props.open) previousFocus?.focus({ preventScroll: true })
})
</script>

<template>
  <Teleport to="body">
    <div v-if="open" class="sheet-layer" role="presentation">
      <button class="sheet-backdrop" aria-label="بستن" tabindex="-1" @click="emit('close')" />
      <section
        ref="panelRef"
        class="sheet-panel"
        role="dialog"
        aria-modal="true"
        aria-labelledby="sheet-title"
        tabindex="-1"
      >
        <div class="mx-auto mt-2 h-1 w-11 rounded-full bg-slate-300 md:hidden" />
        <header class="sticky top-0 z-10 flex items-center gap-2 border-b border-slate-100 bg-[var(--c-paper)] px-4 py-3">
          <h2 id="sheet-title" class="min-w-0 flex-1 truncate text-base font-extrabold">{{ title }}</h2>
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
