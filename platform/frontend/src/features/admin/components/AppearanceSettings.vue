<script setup lang="ts">
import { computed, ref } from 'vue'
import { Paintbrush, RotateCcw } from '@lucide/vue'
import { useRouter } from 'vue-router'
import { useAppearanceStore } from '@/stores/appearance'
import { useLocaleStore } from '@/stores/locale'
import { useVisualStyleStore } from '@/features/visual-editor/store'
import type {
  HeaderMode,
  HeroMode,
  HomeProductGridMode,
  ViewportAppearance,
  ViewportProfile,
} from '@/types/appearance'

type BooleanAppearanceKey={
  [K in keyof ViewportAppearance]:ViewportAppearance[K] extends boolean?K:never
}[keyof ViewportAppearance]

const appearance=useAppearanceStore()
const locale=useLocaleStore()
const visual=useVisualStyleStore()
const router=useRouter()
const target=ref<ViewportProfile>('mobile')
const current=computed(()=>appearance.profiles[target.value])

const viewportTabs:{id:ViewportProfile;label:string}[]=[
  {id:'mobile',label:'viewportMobile'},
  {id:'tablet',label:'viewportTablet'},
  {id:'desktop',label:'viewportDesktop'},
]
const sectionToggles:{key:BooleanAppearanceKey;label:string}[]=[
  {key:'showAbout',label:'showAboutLabel'},
  {key:'showWhy',label:'showWhyLabel'},
  {key:'showCapabilities',label:'showCapabilitiesLabel'},
  {key:'showProductBanners',label:'showProductBannersLabel'},
  {key:'showFooter',label:'showFooterLabel'},
]

async function openVisualEditor(){
  visual.setEnabled(true)
  await router.push('/')
}
function checked(event:Event){return (event.target as HTMLInputElement).checked}
function setBoolean(key:BooleanAppearanceKey,event:Event){
  appearance.updateProfile(target.value,{[key]:checked(event)} as Partial<ViewportAppearance>)
}
function setHeaderMode(event:Event){
  appearance.updateProfile(target.value,{headerMode:(event.target as HTMLSelectElement).value as HeaderMode})
}
function setHeroMode(event:Event){
  appearance.updateProfile(target.value,{heroMode:(event.target as HTMLSelectElement).value as HeroMode})
}
function setProductGrid(event:Event){
  appearance.updateProfile(target.value,{homeProductGrid:(event.target as HTMLSelectElement).value as HomeProductGridMode})
}
</script>

<template>
  <section class="space-y-4">
    <div class="flex flex-wrap items-end gap-3">
      <div>
        <h2 class="text-xl font-black text-[var(--c-text)]">{{locale.t('adminAppearance')}}</h2>
        <p class="mt-1 max-w-3xl text-sm leading-6 text-[var(--c-muted)]">{{locale.t('appearanceHelp')}}</p>
      </div>
      <button class="mini-action ms-auto" type="button" @click="appearance.resetAll()">
        <RotateCcw :size="15"/>{{locale.t('resetAllCustomerDefaults')}}
      </button>
    </div>

    <div class="admin-surface rounded-2xl p-2">
      <div class="admin-tabs">
        <button
          v-for="item in viewportTabs"
          :key="item.id"
          class="admin-tab"
          :class="{active:target===item.id}"
          type="button"
          @click="target=item.id"
        >{{locale.t(item.label)}}</button>
      </div>
    </div>

    <article class="admin-surface rounded-2xl p-4">
      <div class="flex flex-wrap items-center gap-3">
        <div>
          <h3 class="text-base font-black text-[var(--c-text)]">ویرایش دیداری صفحه</h3>
          <p class="mt-1 max-w-3xl text-xs leading-6 text-[var(--c-muted)]">صفحه را مستقیم لمس کنید؛ تنظیمات عنصر انتخاب‌شده در پنل پایین باز می‌شود. خاموش‌کردن ادیتور، استایل‌های ذخیره‌شده را پاک نمی‌کند.</p>
        </div>
        <button class="mini-action ms-auto bg-[var(--c-primary)] text-white" type="button" @click="openVisualEditor">
          <Paintbrush :size="16"/>باز کردن ادیتور
        </button>
      </div>
    </article>

    <div class="grid gap-4 lg:grid-cols-2">
      <article class="admin-surface rounded-2xl p-4">
        <h3 class="text-base font-black text-[var(--c-text)]">{{locale.t('appearanceNavigation')}}</h3>
        <div class="mt-4 grid gap-3">
          <label class="form-field">
            {{locale.t('headerModeLabel')}}
            <select :value="current.headerMode" @change="setHeaderMode">
              <option value="compact-drawer">{{locale.t('headerCompact')}}</option>
              <option value="expanded">{{locale.t('headerExpanded')}}</option>
            </select>
          </label>

          <label class="flex min-h-11 items-center gap-3 rounded-xl border border-[var(--c-border)] bg-[var(--c-surface-2)] px-3 text-sm">
            <input type="checkbox" :checked="current.showHamburger" @change="setBoolean('showHamburger',$event)">
            <span>{{locale.t('showHamburgerLabel')}}</span>
          </label>
          <label class="flex min-h-11 items-center gap-3 rounded-xl border border-[var(--c-border)] bg-[var(--c-surface-2)] px-3 text-sm">
            <input type="checkbox" :checked="current.showBrandText" @change="setBoolean('showBrandText',$event)">
            <span>{{locale.t('showBrandTextLabel')}}</span>
          </label>
          <label class="flex min-h-11 items-center gap-3 rounded-xl border border-[var(--c-border)] bg-[var(--c-surface-2)] px-3 text-sm">
            <input type="checkbox" :checked="current.showLanguage" @change="setBoolean('showLanguage',$event)">
            <span>{{locale.t('showLanguageLabel')}}</span>
          </label>
          <label class="flex min-h-11 items-center gap-3 rounded-xl border border-[var(--c-border)] bg-[var(--c-surface-2)] px-3 text-sm">
            <input type="checkbox" :checked="current.showHelp" @change="setBoolean('showHelp',$event)">
            <span>{{locale.t('showHelpLabel')}}</span>
          </label>
          <label class="flex min-h-11 items-center gap-3 rounded-xl border border-[var(--c-border)] bg-[var(--c-surface-2)] px-3 text-sm">
            <input type="checkbox" :checked="current.showAccount" @change="setBoolean('showAccount',$event)">
            <span>{{locale.t('showAccountLabel')}}</span>
          </label>
        </div>
      </article>

      <article class="admin-surface rounded-2xl p-4">
        <h3 class="text-base font-black text-[var(--c-text)]">{{locale.t('appearanceHome')}}</h3>
        <div class="mt-4 grid gap-3">
          <label class="form-field">
            {{locale.t('heroModeLabel')}}
            <select :value="current.heroMode" @change="setHeroMode">
              <option value="single">{{locale.t('heroSingle')}}</option>
              <option value="carousel">{{locale.t('heroCarousel')}}</option>
            </select>
          </label>

          <label class="form-field">
            {{locale.t('homeProductGridLabel')}}
            <select :value="current.homeProductGrid" @change="setProductGrid">
              <option value="hidden">{{locale.t('homeProductGridHidden')}}</option>
              <option value="recommended-6">{{locale.t('homeProductGridRecommended')}}</option>
            </select>
          </label>

          <label class="flex min-h-11 items-center gap-3 rounded-xl border border-[var(--c-border)] bg-[var(--c-surface-2)] px-3 text-sm">
            <input type="checkbox" :checked="current.showCategoryNumbers" @change="setBoolean('showCategoryNumbers',$event)">
            <span>{{locale.t('showCategoryNumbersLabel')}}</span>
          </label>

          <label class="flex min-h-11 items-center gap-3 rounded-xl border border-[var(--c-border)] bg-[var(--c-surface-2)] px-3 text-sm">
            <input type="checkbox" :checked="current.showSubcategoryCodes" @change="setBoolean('showSubcategoryCodes',$event)">
            <span>{{locale.t('showSubcategoryCodesLabel')}}</span>
          </label>

          <div class="mt-1 text-xs font-black text-[var(--c-muted)]">{{locale.t('homeSectionsLabel')}}</div>
          <label v-for="item in sectionToggles" :key="item.key" class="flex min-h-11 items-center gap-3 rounded-xl border border-[var(--c-border)] bg-[var(--c-surface-2)] px-3 text-sm">
            <input
              type="checkbox"
              :checked="current[item.key]"
              @change="setBoolean(item.key,$event)"
            >
            <span>{{locale.t(item.label)}}</span>
          </label>
        </div>
      </article>
    </div>

    <div class="flex justify-end">
      <button class="mini-action" type="button" @click="appearance.resetProfile(target)">
        <RotateCcw :size="15"/>{{locale.t('resetDeviceDefaults')}}
      </button>
    </div>
  </section>
</template>
