<script setup lang="ts">
import { LogIn, LogOut, UserRound } from '@lucide/vue'
import { onBeforeUnmount, ref } from 'vue'
import { useSessionStore } from '@/stores/session'
import { useLocaleStore } from '@/stores/locale'
import type { Locale } from '@/services/localeDetection'

const emit=defineEmits<{login:[];designlab:[]}>()
const session=useSessionStore()
const locale=useLocaleStore()
const timer=ref<number|null>(null)

function startLongPress(){
  stopLongPress()
  timer.value=window.setTimeout(()=>emit('designlab'),3000)
}
function stopLongPress(){
  if(timer.value)window.clearTimeout(timer.value)
  timer.value=null
}
function logout(){session.logout()}
function changeLanguage(event:Event){
  locale.setManual((event.target as HTMLSelectElement).value as Locale)
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
    <div class="mx-auto flex max-w-6xl items-center gap-2 px-3 py-2.5 sm:gap-3">
      <img class="h-10 w-10 shrink-0 rounded-xl bg-white/10 object-cover" :src="'../../logo.png'" alt="Armaghan" />
      <div class="min-w-0">
        <b class="block text-sm">ارمغان</b>
        <span class="hidden truncate text-[10px] text-white/70 sm:block">{{locale.t('manufacturer')}}</span>
      </div>

      <div class="ms-auto flex items-center gap-1.5 sm:gap-2">
        <label class="relative" @pointerdown.stop>
          <span class="sr-only">{{locale.t('language')}}</span>
          <select
            :value="locale.locale"
            class="min-h-10 max-w-[6.4rem] rounded-xl border border-white/15 bg-white/10 px-2 text-xs font-bold text-white outline-none [&>option]:text-slate-900"
            :aria-label="locale.t('language')"
            @change="changeLanguage"
          >
            <option value="fa">فارسی</option>
            <option value="ar">العربية</option>
            <option value="en">English</option>
            <option value="ku">کوردی</option>
          </select>
        </label>

        <button
          v-if="!session.isAuthenticated"
          class="inline-flex min-h-10 items-center gap-1.5 rounded-xl border border-white/15 bg-white/10 px-2.5 text-xs font-bold"
          @pointerdown.stop
          @click.stop="emit('login')"
        >
          <LogIn :size="17"/><span class="hidden sm:inline">{{locale.t('login')}}</span>
        </button>

        <template v-else>
          <span class="hidden rounded-xl border border-white/15 bg-white/10 px-3 py-2 text-xs font-bold md:inline-flex">
            <UserRound class="me-1" :size="17"/>
            {{session.impersonatedCustomerId?'مدیر ← مشتری':session.isAdmin?'مدیر':'مشتری'}}
          </span>
          <button
            class="inline-flex min-h-10 items-center gap-1.5 rounded-xl border border-white/15 bg-white/10 px-2.5 text-xs font-bold"
            :aria-label="locale.t('logout')"
            @pointerdown.stop
            @click.stop="logout"
          >
            <LogOut :size="17"/><span class="hidden sm:inline">{{locale.t('logout')}}</span>
          </button>
        </template>
      </div>
    </div>
  </header>
</template>
