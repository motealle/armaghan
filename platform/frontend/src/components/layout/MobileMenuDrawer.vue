<script setup lang="ts">
import { ClipboardList, Grid2X2, Heart, House, LogIn, LogOut, UserRound, WandSparkles, X } from '@lucide/vue'
import { computed, nextTick, onBeforeUnmount, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useLocaleStore } from '@/stores/locale'
import { useSessionStore } from '@/stores/session'
import type { Locale } from '@/services/localeDetection'

const props=defineProps<{open:boolean}>()
const emit=defineEmits<{close:[];login:[]}>()
const locale=useLocaleStore()
const session=useSessionStore()
const route=useRoute()
const panelRef=ref<HTMLElement|null>(null)
let previousFocus:HTMLElement|null=null
const focusable='a[href],button:not([disabled]),select:not([disabled]),input:not([disabled]),[tabindex]:not([tabindex="-1"])'

const items=computed(()=>[
  {to:'/',label:locale.t('home'),icon:House},
  {to:'/products',label:locale.t('products'),icon:Grid2X2},
  {to:'/production',label:locale.t('production'),icon:WandSparkles},
  {to:'/favorites',label:locale.t('favorites'),icon:Heart},
  {to:'/tracking',label:locale.t('tracking'),icon:ClipboardList},
])
function changeLanguage(event:Event){locale.setManual((event.target as HTMLSelectElement).value as Locale)}
function login(){emit('close');emit('login')}
function logout(){session.logout();emit('close')}
function controls(){return panelRef.value?Array.from(panelRef.value.querySelectorAll<HTMLElement>(focusable)):[]}
function onKey(event:KeyboardEvent){
  if(!props.open)return
  if(event.key==='Escape'){emit('close');return}
  if(event.key!=='Tab')return
  const list=controls()
  if(!list.length)return
  const first=list[0]!,last=list[list.length-1]!,active=document.activeElement
  if(event.shiftKey&&active===first){event.preventDefault();last.focus()}
  else if(!event.shiftKey&&active===last){event.preventDefault();first.focus()}
}

watch(()=>props.open,async(open,wasOpen)=>{
  document.body.style.overflow=open?'hidden':''
  if(open){
    previousFocus=document.activeElement instanceof HTMLElement?document.activeElement:null
    window.addEventListener('keydown',onKey)
    await nextTick()
    controls()[0]?.focus({preventScroll:true})
  }else{
    window.removeEventListener('keydown',onKey)
    if(wasOpen){await nextTick();previousFocus?.focus({preventScroll:true});previousFocus=null}
  }
})
watch(()=>route.fullPath,()=>{if(props.open)emit('close')})
onBeforeUnmount(()=>{document.body.style.overflow='';window.removeEventListener('keydown',onKey)})
</script>

<template>
  <Teleport to="body">
    <div v-if="open" class="mobile-drawer-layer lg:hidden">
      <button class="mobile-drawer-backdrop" aria-label="بستن منو" tabindex="-1" @click="emit('close')" />
      <aside ref="panelRef" class="mobile-drawer-panel" role="dialog" aria-modal="true" aria-label="منوی موبایل">
        <div class="flex items-center gap-3 border-b border-[var(--c-border)] px-4 py-4">
          <img class="h-11 w-11 rounded-xl object-cover" src="../../logo.png" alt="Armaghan" />
          <div class="min-w-0">
            <b class="block text-sm text-[var(--c-text)]">ارمغان</b>
            <span class="block truncate text-[10px] text-[var(--c-muted)]">{{locale.t('manufacturer')}}</span>
          </div>
          <button class="ms-auto grid h-10 w-10 place-items-center rounded-xl border border-[var(--c-border)] text-[var(--c-text)]" aria-label="بستن" @click="emit('close')">
            <X :size="20"/>
          </button>
        </div>

        <nav class="p-3" aria-label="ناوبری موبایل">
          <RouterLink v-for="item in items" :key="item.to" :to="item.to" class="mobile-drawer-link">
            <component :is="item.icon" :size="19"/>
            <span>{{item.label}}</span>
          </RouterLink>
        </nav>

        <div class="mx-3 border-t border-[var(--c-border)] pt-3">
          <label class="block text-xs font-black text-[var(--c-text)]">
            {{locale.t('language')}}
            <select :value="locale.locale" class="mt-2 min-h-11 w-full rounded-xl border border-[var(--c-border)] bg-[var(--c-surface)] px-3 text-sm" @change="changeLanguage">
              <option value="fa">فارسی</option>
              <option value="ar">العربية</option>
              <option value="en">English</option>
              <option value="ku">کوردی</option>
            </select>
          </label>
        </div>

        <div class="mt-auto p-3">
          <button v-if="!session.isAuthenticated" class="mobile-drawer-account" @click="login">
            <LogIn :size="19"/><span>{{locale.t('login')}}</span>
          </button>
          <template v-else>
            <div class="mb-2 flex items-center gap-2 rounded-xl bg-[var(--c-surface-2)] p-3 text-xs font-bold text-[var(--c-text)]">
              <UserRound :size="18"/>
              <span>{{session.impersonatedCustomerId?'مدیر ← مشتری':session.isAdmin?'مدیر':'مشتری'}}</span>
            </div>
            <button class="mobile-drawer-account text-rose-600" @click="logout">
              <LogOut :size="19"/><span>{{locale.t('logout')}}</span>
            </button>
          </template>
        </div>
      </aside>
    </div>
  </Teleport>
</template>
