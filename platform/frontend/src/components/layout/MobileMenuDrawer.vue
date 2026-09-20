<script setup lang="ts">
import { computed, nextTick, onBeforeUnmount, ref, watch } from 'vue'
import { CircleHelp, LogIn, LogOut, X } from '@lucide/vue'
import { useLocaleStore } from '@/stores/locale'
import { useSessionStore } from '@/stores/session'
import { useCustomersStore } from '@/stores/customers'
import type { Locale } from '@/services/localeDetection'
import DrawerAccountDashboard from './DrawerAccountDashboard.vue'

const props=defineProps<{open:boolean}>()
const emit=defineEmits<{close:[];login:[];help:[]}>()
const locale=useLocaleStore()
const session=useSessionStore()
const customers=useCustomersStore()
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
    const customer=customers.items.find(item=>item.id===session.impersonatedCustomerId)
    return customer?{flag:customer.flag,name:customer.name,meta:locale.orderStatus(customer.activeOrder)}:{flag:'🌐',name:locale.t('customerLabel'),meta:locale.t('drawerSummary')}
  }
  if(session.isAdmin)return{flag:'🇮🇷',name:locale.t('adminOverview'),meta:locale.t('drawerOperational')}
  if(session.isCustomer){
    const customer=customers.items[0]
    return customer?{flag:customer.flag,name:customer.name,meta:locale.t('drawerSummary')}:{flag:'🌐',name:locale.t('customerLabel'),meta:locale.t('drawerSummary')}
  }
  return null
})

function setLanguage(value:Locale){locale.setManual(value)}
function login(){emit('close');emit('login')}
function help(){emit('close');emit('help')}
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
    <Transition name="drawer">
      <div v-if="open" class="mobile-drawer-layer lg:hidden">
        <button class="mobile-drawer-backdrop" :aria-label="locale.t('close')" tabindex="-1" @click="emit('close')" />
        <aside ref="panelRef" class="mobile-drawer-panel" role="dialog" aria-modal="true" :aria-label="locale.t('drawerSummary')" tabindex="-1">
          <div class="drawer-scroll">
            <div class="drawer-header">
              <img class="h-11 w-11 rounded-xl object-cover" :src="'../../logo.png'" alt="Armaghan" />
              <div class="min-w-0">
                <b class="block text-sm text-[var(--c-text)]">{{locale.t('brandName')}}</b>
                <span class="block truncate text-[11px] text-[var(--c-muted)]">{{locale.t('manufacturer')}}</span>
              </div>
              <button class="drawer-close-button" :aria-label="locale.t('close')" @click="emit('close')"><X :size="20"/></button>
            </div>

            <section class="px-3 pt-4">
              <div class="mb-2 text-xs font-black text-[var(--c-muted)]">{{locale.t('language')}}</div>
              <div class="language-button-row" role="group" :aria-label="locale.t('language')">
                <button
                  v-for="item in languages"
                  :key="item.id"
                  type="button"
                  class="language-square-button"
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

            <div class="px-3 pt-3">
              <button class="drawer-help-button" @click="help">
                <CircleHelp :size="19"/>
                <span>{{locale.t('helpGuide')}}</span>
              </button>
            </div>
          </div>

          <footer class="drawer-account-footer">
            <template v-if="!session.isAuthenticated">
              <button class="drawer-login-button" @click="login">
                <LogIn :size="20"/>
                <span>{{locale.t('login')}}</span>
              </button>
              <div class="drawer-footer-divider"/>
              <div class="drawer-footer-brand">{{locale.t('manufacturer')}}</div>
            </template>

            <div v-else class="drawer-profile-row">
              <span class="drawer-profile-flag" aria-hidden="true">{{profile?.flag}}</span>
              <div class="min-w-0 flex-1">
                <b class="block truncate text-xs text-[var(--c-text)]">{{profile?.name}}</b>
                <span class="block truncate text-[10px] text-[var(--c-muted)]">{{profile?.meta}}</span>
              </div>
              <button class="drawer-logout-button" :aria-label="locale.t('logout')" @click="logout"><LogOut :size="18"/></button>
            </div>
          </footer>
        </aside>
      </div>
    </Transition>
  </Teleport>
</template>
