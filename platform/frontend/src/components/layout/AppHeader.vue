<script setup lang="ts">
import {
  CircleHelp, ClipboardList, Globe2, Grid2X2, Heart, House, LogIn, LogOut, Menu, UserRound, WandSparkles,
} from '@lucide/vue'
import { computed, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useResolvedAppearance } from '@/composables/useResolvedAppearance'
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
const {profile,policy}=useResolvedAppearance()

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
  <header class="sticky top-0 z-[90] border-b border-white/10 bg-[var(--c-primary)] text-white shadow-sm">
    <div class="mx-auto max-w-[1500px] px-3 py-2.5 md:px-5 lg:px-8">
      <div
        class="app-header-layout"
        :class="[
          policy.headerMode==='expanded'?'app-header-expanded':'app-header-compact',
          `app-header-${profile}`,
        ]"
      >
        <RouterLink to="/" class="app-header-brand" @pointerdown.stop>
          <img class="h-10 w-10 shrink-0 rounded-xl bg-white/10 object-cover" :src="'../../logo.png'" alt="Armaghan" />
          <div v-if="policy.showBrandText" class="min-w-0">
            <b class="block text-sm">{{locale.t('brandName')}}</b>
            <span class="block truncate text-[10px] text-white/70">{{locale.t('manufacturer')}}</span>
          </div>
        </RouterLink>

        <nav
          v-if="policy.headerMode==='expanded'"
          class="app-header-nav"
          :aria-label="locale.t('mainNavigation')"
        >
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

        <div class="app-header-actions">
          <ThemeSwitcher @pointerdown.stop />

          <label
            v-if="policy.showLanguage&&profile==='mobile'&&policy.headerMode!=='expanded'"
            class="mobile-header-language"
            @pointerdown.stop
          >
            <Globe2 :size="16" aria-hidden="true"/>
            <select :value="locale.locale" class="header-select" :aria-label="locale.t('language')" @change="changeLanguage">
              <option value="fa" lang="fa">فارسی</option>
              <option value="ar" lang="ar">العربية</option>
              <option value="en" lang="en">English</option>
              <option value="ku" lang="ckb">کوردی</option>
            </select>
          </label>

          <button
            v-if="policy.showHelp&&policy.headerMode==='expanded'"
            type="button"
            class="header-action"
            @pointerdown.stop
            @click.stop="emit('help')"
          >
            <CircleHelp :size="17"/><span>{{locale.t('helpGuide')}}</span>
          </button>

          <label v-if="policy.showLanguage&&policy.headerMode==='expanded'" class="header-language-control" @pointerdown.stop>
            <span class="header-language-label">{{locale.t('language')}}</span>
            <select :value="locale.locale" class="header-select" :aria-label="locale.t('language')" @change="changeLanguage">
              <option value="fa" lang="fa">فارسی</option>
              <option value="ar" lang="ar">العربية</option>
              <option value="en" lang="en">English</option>
              <option value="ku" lang="ckb">کوردی</option>
            </select>
          </label>

          <button
            v-if="policy.showAccount&&!session.isAuthenticated&&policy.headerMode==='expanded'"
            class="header-action"
            @pointerdown.stop
            @click.stop="emit('login')"
          >
            <LogIn :size="17"/><span>{{locale.t('login')}}</span>
          </button>

          <template v-else-if="policy.showAccount&&session.isAuthenticated&&policy.headerMode==='expanded'">
            <RouterLink to="/tracking" class="header-action" @pointerdown.stop>
              <UserRound :size="17"/>
              <span>{{session.impersonatedCustomerId?locale.t('impersonationRole'):session.isAdmin?locale.t('adminRole'):locale.t('customerRole')}}</span>
            </RouterLink>
            <button class="header-action header-icon-action" :aria-label="locale.t('logout')" @pointerdown.stop @click.stop="logout">
              <LogOut :size="17"/>
            </button>
          </template>

          <button
            v-if="policy.showHamburger"
            type="button"
            class="mobile-menu-trigger"
            :aria-label="locale.t('openMenu')"
            @pointerdown.stop
            @click.stop="mobileMenuOpen=true"
          >
            <Menu :size="21"/>
          </button>
        </div>
      </div>
    </div>
  </header>

  <MobileMenuDrawer
    :open="mobileMenuOpen"
    :show-language="policy.showLanguage&&profile!=='mobile'"
    :show-help="policy.showHelp"
    :show-account="policy.showAccount"
    :show-brand-text="policy.showBrandText"
    @close="mobileMenuOpen=false"
    @login="emit('login')"
    @help="emit('help')"
  />
</template>
