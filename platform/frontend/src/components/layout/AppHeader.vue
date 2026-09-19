<script setup lang="ts">
import { LogIn, LogOut, UserRound } from '@lucide/vue'
import { onBeforeUnmount, ref } from 'vue'
import { useSessionStore } from '@/stores/session'

const emit = defineEmits<{ login: []; designlab: [] }>()
const session = useSessionStore()
const timer = ref<number | null>(null)

function startLongPress() {
  stopLongPress()
  timer.value = window.setTimeout(() => emit('designlab'), 3000)
}
function stopLongPress() {
  if (timer.value) window.clearTimeout(timer.value)
  timer.value = null
}
function logout() {
  session.logout()
}
onBeforeUnmount(stopLongPress)
</script>

<template>
  <header
    class="sticky top-0 z-40 bg-[var(--c-primary)] text-white shadow-sm"
    @pointerdown.passive="startLongPress"
    @pointerup.passive="stopLongPress"
    @pointercancel.passive="stopLongPress"
    @pointerleave.passive="stopLongPress"
  >
    <div class="mx-auto flex max-w-6xl items-center gap-3 px-3 py-2.5">
      <img class="h-10 w-10 rounded-xl bg-white/10 object-cover" :src="'../../logo.png'" alt="ارمغان" />
      <div class="min-w-0">
        <b class="block text-sm">ارمغان</b>
        <span class="block truncate text-[10px] text-white/70">تولید و صادرات پوشاک</span>
      </div>

      <button
        v-if="!session.isAuthenticated"
        class="ms-auto inline-flex min-h-10 items-center gap-2 rounded-xl border border-white/15 bg-white/10 px-3 text-xs font-bold"
        @click.stop="emit('login')"
      >
        <LogIn :size="18" /> ورود
      </button>

      <div v-else class="ms-auto flex items-center gap-2">
        <span class="hidden rounded-xl border border-white/15 bg-white/10 px-3 py-2 text-xs font-bold sm:inline-flex">
          <UserRound class="me-1" :size="17" />
          {{ session.impersonatedCustomerId ? 'مدیر ← مشتری' : session.isAdmin ? 'مدیر' : 'مشتری' }}
        </span>
        <button
          class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-white/15 bg-white/10 px-3 text-xs font-bold"
          aria-label="خروج"
          @click.stop="logout"
        >
          <LogOut :size="18" /><span>خروج</span>
        </button>
      </div>
    </div>
  </header>
</template>
