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
import { useVisualStyleStore } from '@/features/visual-editor/store'
import ThemeSwitcher from './ThemeSwitcher.vue'
import BaseModal from '@/components/ui/BaseModal.vue'
import { useAdminStore } from '@/features/admin/store'
import MobileMenuDrawer from './MobileMenuDrawer.vue'

const emit=defineEmits<{login:[];help:[]}>()
const session=useSessionStore()
const admin=useAdminStore()
const languageOpen=ref(false)
const logoutBusy=ref(false)
const logoutError=ref(false)
const languageCode=computed(()=>locale.locale.slice(0,1).toUpperCase()+locale.locale.slice(1))
const languages=[{id:'fa' as const,label:'فارسی'},{id:'ar' as const,label:'العربية'},{id:'en' as const,label:'English'},{id:'ku' as const,label:'کوردی'}]
const authenticated=computed(()=>!!admin.identity||session.isAuthenticated)
const locale=useLocaleStore()
const visual=useVisualStyleStore()
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
async function logout(){if(logoutBusy.value)return;logoutBusy.value=true;logoutError.value=false;try{if(admin.identity)await admin.logout();else if(!await session.logout())throw Error('Logout failed')}catch{logoutError.value=true}finally{logoutBusy.value=false}}
function selectLanguage(value:Locale){locale.setManual(value);languageOpen.value=false}
function changeLanguage(event:Event){locale.setManual((event.target as HTMLSelectElement).value as Locale)}
</script>

<template>
  <header data-style-id="header.shell" data-style-label="نوار بالای سایت" class="sticky top-0 z-[90] border-b border-white/10 bg-[var(--role-brand-chrome)] text-white shadow-sm">
    <div class="mx-auto max-w-[1500px] px-3 py-2.5 md:px-5 lg:px-8">
      <div
        class="app-header-layout"
        :class="[
          policy.headerMode==='expanded'?'app-header-expanded':'app-header-compact',
          `app-header-${profile}`,
        ]"
      >
        <RouterLink to="/" data-style-id="header.brand" data-style-label="لوگو و نام برند" class="app-header-brand" @pointerdown.stop>
          <img class="h-10 w-10 shrink-0 rounded-xl bg-white/10 object-cover" :src="'../../logo.png'" alt="Armaghan" />
          <div v-if="policy.showBrandText" class="min-w-0">
            <b data-style-id="header.brand-name" data-style-label="نام برند در هدر" data-editable-text="true" class="block text-sm">{{visual.resolveText('header.brand-name',locale.locale,locale.t('brandName'))}}</b>
            <span data-style-id="header.manufacturer" data-style-label="زیرعنوان برند در هدر" data-editable-text="true" class="block truncate text-[10px] text-white/70">{{visual.resolveText('header.manufacturer',locale.locale,locale.t('manufacturer'))}}</span>
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

          <button v-if="profile==='mobile'" type="button" class="mobile-header-language" :aria-label="locale.t('language')" aria-haspopup="dialog" @pointerdown.stop @click.stop="languageOpen=true">
            <Globe2 :size="18" aria-hidden="true"/><span>{{languageCode}}</span>
          </button>

          <button
            v-if="profile==='mobile'||(policy.showHelp&&policy.headerMode==='expanded')"
            type="button"
            class="header-action"
            :class="{'header-icon-action':profile==='mobile'}"
            :aria-label="locale.t('helpGuide')"
            @pointerdown.stop
            @click.stop="emit('help')"
          >
            <CircleHelp :size="19"/><span v-if="profile!=='mobile'">{{locale.t('helpGuide')}}</span>
          </button>

          <label v-if="profile!=='mobile'&&policy.showLanguage&&policy.headerMode==='expanded'" class="header-language-control" @pointerdown.stop>
            <select :value="locale.locale" class="header-select" :aria-label="locale.t('language')" @change="changeLanguage">
              <option value="fa" lang="fa">فارسی</option>
              <option value="ar" lang="ar">العربية</option>
              <option value="en" lang="en">English</option>
              <option value="ku" lang="ckb">کوردی</option>
            </select>
          </label>

          <button
            v-if="!authenticated&&(profile==='mobile'||(policy.showAccount&&policy.headerMode==='expanded'))"
            class="header-action"
            :class="{'header-icon-action':profile==='mobile'}"
            :aria-label="locale.t('login')"
            @pointerdown.stop
            @click.stop="emit('login')"
          >
            <LogIn :size="19"/><span v-if="profile!=='mobile'">{{locale.t('login')}}</span>
          </button>

          <template v-else-if="authenticated&&(profile==='mobile'||(policy.showAccount&&policy.headerMode==='expanded'))">
            <RouterLink v-if="profile!=='mobile'" :to="admin.identity?'/admin':'/tracking'" class="header-action" @pointerdown.stop>
              <UserRound :size="17"/>
              <span>{{session.impersonatedCustomerId?locale.t('impersonationRole'):(admin.identity||session.isAdmin)?locale.t('adminRole'):locale.t('customerRole')}}</span>
            </RouterLink>
            <button class="header-action header-icon-action" :disabled="logoutBusy" :aria-label="locale.t('logout')" @pointerdown.stop @click.stop="logout">
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

  <p v-if="logoutError" role="alert" class="auth-error">{{locale.t('logoutFailed')}}</p>
  <BaseModal :open="languageOpen" :title="locale.t('language')" @close="languageOpen=false">
    <div class="grid grid-cols-2 gap-3">
      <button v-for="item in languages" :key="item.id" type="button" class="language-choice" :class="{selected:locale.locale===item.id}" :aria-pressed="locale.locale===item.id" :lang="item.id==='ku'?'ckb':item.id" @click="selectLanguage(item.id)">
        <Globe2 :size="22" aria-hidden="true"/><b>{{item.label}}</b>
      </button>
    </div>
  </BaseModal>
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
