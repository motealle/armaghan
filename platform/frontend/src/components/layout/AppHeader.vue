<script setup lang="ts">
import { ClipboardList, Grid2X2, Heart, House, LogIn, LogOut, Menu, UserRound, WandSparkles } from '@lucide/vue'
import { computed, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useSessionStore } from '@/stores/session'
import { useLocaleStore } from '@/stores/locale'
import type { Locale } from '@/services/localeDetection'
import ThemeSwitcher from './ThemeSwitcher.vue'
import MobileMenuDrawer from './MobileMenuDrawer.vue'

const emit=defineEmits<{login:[];help:[]}>()
const session=useSessionStore()
const locale=useLocaleStore()
const route=useRoute()
const mobileMenuOpen=ref(false)

const navItems=computed(()=>[
  {to:'/',label:locale.t('home'),icon:House},
  {to:'/products',label:locale.t('products'),icon:Grid2X2},
  {to:'/production',label:locale.t('production'),icon:WandSparkles},
  {to:'/favorites',label:locale.t('favorites'),icon:Heart},
  {to:'/tracking',label:locale.t('tracking'),icon:ClipboardList},
])
function active(path:string){return path==='/'?route.path==='/':route.path.startsWith(path)}
function logout(){session.logout()}
function changeLanguage(event:Event){locale.setManual((event.target as HTMLSelectElement).value as Locale)}
</script>

<template>
  <header
    class="sticky top-0 z-[90] border-b border-white/10 bg-[var(--c-primary)] text-white shadow-sm"
  >
    <div class="app-header-layout mx-auto flex max-w-[1440px] items-center gap-2 px-3 py-2.5 lg:grid lg:grid-cols-[auto_1fr_auto] lg:px-5">
      <RouterLink to="/" class="flex min-w-0 items-center gap-2.5" @pointerdown.stop>
        <img class="h-10 w-10 shrink-0 rounded-xl bg-white/10 object-cover" :src="'../../logo.png'" alt="Armaghan" />
        <div class="min-w-0">
          <b class="block text-sm">{{locale.t('brandName')}}</b>
          <span class="hidden truncate text-[10px] text-white/70 lg:block">{{locale.t('manufacturer')}}</span>
        </div>
      </RouterLink>

      <nav class="hidden lg:flex lg:items-center lg:justify-self-center lg:gap-1" :aria-label="locale.t('mainNavigation')">
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

      <div class="ms-auto flex items-center gap-1.5 lg:ms-0 lg:justify-self-end">
        <ThemeSwitcher @pointerdown.stop />

        <button
          type="button"
          class="mobile-menu-trigger lg:hidden"
          :aria-label="locale.t('openMenu')"
          @pointerdown.stop
          @click.stop="mobileMenuOpen=true"
        >
          <Menu :size="21"/>
        </button>

        <label class="relative hidden lg:block" @pointerdown.stop>
          <span class="sr-only">{{locale.t('language')}}</span>
          <select :value="locale.locale" class="header-select" :aria-label="locale.t('language')" @change="changeLanguage">
            <option value="fa" lang="fa">فارسی</option>
            <option value="ar" lang="ar">العربية</option>
            <option value="en" lang="en">English</option>
            <option value="ku" lang="ckb">کوردی</option>
          </select>
        </label>

        <button
          v-if="!session.isAuthenticated"
          class="header-action hidden lg:inline-flex"
          @pointerdown.stop
          @click.stop="emit('login')"
        >
          <LogIn :size="17"/><span>{{locale.t('login')}}</span>
        </button>

        <template v-else>
          <span class="hidden items-center gap-1.5 text-xs font-bold text-white/80 xl:inline-flex">
            <UserRound :size="17"/>
            {{session.impersonatedCustomerId?locale.t('impersonationRole'):session.isAdmin?locale.t('adminRole'):locale.t('customerRole')}}
          </span>
          <button class="header-action hidden lg:inline-flex" :aria-label="locale.t('logout')" @pointerdown.stop @click.stop="logout">
            <LogOut :size="17"/><span>{{locale.t('logout')}}</span>
          </button>
        </template>
      </div>
    </div>
  </header>

  <MobileMenuDrawer :open="mobileMenuOpen" @close="mobileMenuOpen=false" @login="emit('login')" @help="emit('help')"/>
</template>
