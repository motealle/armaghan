<script setup lang="ts">
import { computed, nextTick, onBeforeUnmount, ref, watch } from 'vue'
import { LogIn, LogOut, UserRound, X } from '@lucide/vue'
import { useLocaleStore } from '@/stores/locale'
import { useSessionStore } from '@/stores/session'
import { demoCustomers } from '@/data/catalog'
import type { Locale } from '@/services/localeDetection'
import DrawerAccountDashboard from './DrawerAccountDashboard.vue'

const props=defineProps<{open:boolean}>()
const emit=defineEmits<{close:[];login:[]}>()
const locale=useLocaleStore()
const session=useSessionStore()
const panelRef=ref<HTMLElement|null>(null)
let previousFocus:HTMLElement|null=null
const focusable='button:not([disabled]),select:not([disabled]),input:not([disabled]),a[href],[tabindex]:not([tabindex="-1"])'

const languages:{id:Locale;short:string;name:string;lang:string}[]=[
  {id:'fa',short:'Fa',name:'فارسی',lang:'fa'},
  {id:'en',short:'En',name:'English',lang:'en'},
  {id:'ar',short:'ع',name:'العربية',lang:'ar'},
  {id:'ku',short:'ک',name:'کوردی',lang:'ckb'},
]
const profile=computed(()=>{
  if(session.impersonatedCustomerId){
    const customer=demoCustomers.find(item=>item.id===session.impersonatedCustomerId)
    return customer ? {flag:customer.flag,name:customer.name,meta:customer.activeOrder} : {flag:'🌐',name:'مشتری',meta:'حالت نیابتی'}
  }
  if(session.isAdmin)return {flag:'🇮🇷',name:'مدیر ارمغان',meta:'مدیریت کاتالوگ و مشتری‌ها'}
  if(session.isCustomer){
    const customer=demoCustomers[0]!
    return {flag:customer.flag,name:customer.name,meta:customer.activeOrder}
  }
  return null
})

function setLanguage(value:Locale){locale.setManual(value)}
function login(){emit('close');emit('login')}
function logout(){session.logout();emit('close')}
function controls(){return panelRef.value?Array.from(panelRef.value.querySelectorAll<HTMLElement>(focusable)):[]}
function onKey(event:KeyboardEvent){
  if(!props.open)return
  if(event.key==='Escape'){event.preventDefault();emit('close');return}
  if(event.key!=='Tab')return
  const list=controls()
  if(!list.length)return
  const first=list[0]!,last=list[list.length-1]!,active=document.activeElement
  if(event.shiftKey&&(active===first||active===panelRef.value)){event.preventDefault();last.focus()}
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
onBeforeUnmount(()=>{document.body.style.overflow='';window.removeEventListener('keydown',onKey)})
</script>

<template>
  <Teleport to="body">
    <div v-if="open" class="mobile-drawer-layer lg:hidden">
      <button class="mobile-drawer-backdrop" aria-label="بستن منو" tabindex="-1" @click="emit('close')" />
      <aside ref="panelRef" class="mobile-drawer-panel" role="dialog" aria-modal="true" aria-label="منوی حساب و تنظیمات" tabindex="-1">
        <div class="drawer-scroll">
          <div class="flex items-center gap-3 border-b border-[var(--c-border)] px-4 py-4">
            <img class="h-11 w-11 rounded-xl object-cover" :src="'../../logo.png'" alt="Armaghan" />
            <div class="min-w-0">
              <b class="block text-sm text-[var(--c-text)]">ارمغان</b>
              <span class="block truncate text-[10px] text-[var(--c-muted)]">{{locale.t('manufacturer')}}</span>
            </div>
            <button class="ms-auto grid h-10 w-10 place-items-center rounded-xl border border-[var(--c-border)] text-[var(--c-text)]" aria-label="بستن" @click="emit('close')">
              <X :size="20"/>
            </button>
          </div>

          <section class="px-3 pt-4">
            <div class="mb-2 text-[11px] font-black text-[var(--c-muted)]">{{locale.t('language')}}</div>
            <div class="language-button-row" role="group" :aria-label="locale.t('language')">
              <button
                v-for="item in languages"
                :key="item.id"
                type="button"
                class="language-round-button"
                :class="{active:locale.locale===item.id}"
                :aria-label="item.name"
                :aria-pressed="locale.locale===item.id"
                :lang="item.lang"
                @click="setLanguage(item.id)"
              >{{item.short}}</button>
            </div>
          </section>

          <div class="px-3 pt-4">
            <DrawerAccountDashboard/>
          </div>
        </div>

        <footer class="drawer-account-footer">
          <button v-if="!session.isAuthenticated" class="drawer-login-button" @click="login">
            <LogIn :size="20"/>
            <span>{{locale.t('login')}}</span>
          </button>

          <div v-else class="drawer-profile-row">
            <span class="drawer-profile-flag" aria-hidden="true">{{profile?.flag}}</span>
            <div class="min-w-0 flex-1">
              <b class="block truncate text-xs text-[var(--c-text)]">{{profile?.name}}</b>
              <span class="block truncate text-[10px] text-[var(--c-muted)]">{{profile?.meta}}</span>
            </div>
            <button class="drawer-logout-button" :aria-label="locale.t('logout')" @click="logout">
              <LogOut :size="18"/>
            </button>
          </div>
        </footer>
      </aside>
    </div>
  </Teleport>
</template>
