<script setup lang="ts">
import { ClipboardList, Grid2X2, Heart, House, LogIn, LogOut, UserRound, WandSparkles } from '@lucide/vue'
import { computed, onBeforeUnmount, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useSessionStore } from '@/stores/session'
import { useLocaleStore } from '@/stores/locale'
import type { Locale } from '@/services/localeDetection'
import ThemeSwitcher from './ThemeSwitcher.vue'

const emit=defineEmits<{login:[];designlab:[]}>()
const session=useSessionStore()
const locale=useLocaleStore()
const route=useRoute()
const timer=ref<number|null>(null)

const navItems=computed(()=>[
  {to:'/',label:locale.t('home'),icon:House},
  {to:'/products',label:locale.t('products'),icon:Grid2X2},
  {to:'/production',label:locale.t('production'),icon:WandSparkles},
  {to:'/favorites',label:locale.t('favorites'),icon:Heart},
  {to:'/tracking',label:locale.t('tracking'),icon:ClipboardList},
])
function active(path:string){return path==='/'?route.path==='/':route.path.startsWith(path)}
function startLongPress(){stopLongPress();timer.value=window.setTimeout(()=>emit('designlab'),3000)}
function stopLongPress(){if(timer.value)window.clearTimeout(timer.value);timer.value=null}
function logout(){session.logout()}
function changeLanguage(event:Event){locale.setManual((event.target as HTMLSelectElement).value as Locale)}
onBeforeUnmount(stopLongPress)
</script>

<template>
  <header
    class="sticky top-0 z-40 border-b border-white/10 bg-[var(--c-primary)] text-white shadow-sm"
    @pointerdown.passive="startLongPress"
    @pointerup.passive="stopLongPress"
    @pointercancel.passive="stopLongPress"
    @pointerleave.passive="stopLongPress"
  >
    <div class="mx-auto flex max-w-[1440px] items-center gap-2 px-3 py-2.5 sm:gap-3 lg:px-5">
      <RouterLink to="/" class="flex min-w-0 items-center gap-2.5" @pointerdown.stop>
        <img class="h-10 w-10 shrink-0 rounded-xl bg-white/10 object-cover" :src="'../../logo.png'" alt="Armaghan" />
        <div class="min-w-0">
          <b class="block text-sm">ارمغان</b>
          <span class="hidden truncate text-[10px] text-white/70 sm:block">{{locale.t('manufacturer')}}</span>
        </div>
      </RouterLink>

      <nav class="hidden lg:flex lg:items-center lg:gap-1 lg:self-stretch lg:px-4" aria-label="ناوبری اصلی">
        <RouterLink
          v-for="item in navItems"
          :key="item.to"
          :to="item.to"
          class="desktop-nav-link"
          :class="{active:active(item.to)}"
          @pointerdown.stop
        >
          <component :is="item.icon" :size="17"/>
          <span>{{item.label}}</span>
        </RouterLink>
      </nav>

      <div class="ms-auto flex items-center gap-1.5 sm:gap-2">
        <ThemeSwitcher @pointerdown.stop />

        <label class="relative" @pointerdown.stop>
          <span class="sr-only">{{locale.t('language')}}</span>
          <select
            :value="locale.locale"
            class="header-select"
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
          class="header-action"
          @pointerdown.stop
          @click.stop="emit('login')"
        >
          <LogIn :size="17"/><span class="hidden sm:inline">{{locale.t('login')}}</span>
        </button>

        <template v-else>
          <span class="hidden rounded-xl border border-white/15 bg-white/10 px-3 py-2 text-xs font-bold xl:inline-flex">
            <UserRound class="me-1" :size="17"/>
            {{session.impersonatedCustomerId?'مدیر ← مشتری':session.isAdmin?'مدیر':'مشتری'}}
          </span>
          <button class="header-action" :aria-label="locale.t('logout')" @pointerdown.stop @click.stop="logout">
            <LogOut :size="17"/><span class="hidden sm:inline">{{locale.t('logout')}}</span>
          </button>
        </template>
      </div>
    </div>
  </header>
</template>
