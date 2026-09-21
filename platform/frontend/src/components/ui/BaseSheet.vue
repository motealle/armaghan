<script setup lang="ts">
import { nextTick, onBeforeUnmount, ref, watch } from 'vue'
import { LogOut, X } from '@lucide/vue'
import { useSessionStore } from '@/stores/session'
import { useLocaleStore } from '@/stores/locale'

const props=defineProps<{open:boolean;title:string}>()
const emit=defineEmits<{close:[]}>()
const session=useSessionStore()
const locale=useLocaleStore()
const panelRef=ref<HTMLElement|null>(null)
let previousFocus:HTMLElement|null=null
const focusableSelector=[
  'a[href]','button:not([disabled])','input:not([disabled])','select:not([disabled])',
  'textarea:not([disabled])','[tabindex]:not([tabindex="-1"])'
].join(',')

function controls(){return panelRef.value?Array.from(panelRef.value.querySelectorAll<HTMLElement>(focusableSelector)):[]}
function focusFirst(){(controls()[0]??panelRef.value)?.focus({preventScroll:true})}
function onKey(event:KeyboardEvent){
  if(!props.open)return
  if(event.key==='Escape'){event.preventDefault();emit('close');return}
  if(event.key!=='Tab')return
  const list=controls()
  if(!list.length){event.preventDefault();panelRef.value?.focus({preventScroll:true});return}
  const first=list[0]!,last=list[list.length-1]!,active=document.activeElement
  if(event.shiftKey&&(active===first||active===panelRef.value)){event.preventDefault();last.focus()}
  else if(!event.shiftKey&&active===last){event.preventDefault();first.focus()}
}
function logoutEverywhere(){session.logout();emit('close')}

watch(()=>props.open,async(open,wasOpen)=>{
  document.body.style.overflow=open?'hidden':''
  if(open){
    previousFocus=document.activeElement instanceof HTMLElement?document.activeElement:null
    window.addEventListener('keydown',onKey)
    await nextTick();focusFirst()
  }else{
    window.removeEventListener('keydown',onKey)
    if(wasOpen){await nextTick();previousFocus?.focus({preventScroll:true});previousFocus=null}
  }
},{immediate:true})

onBeforeUnmount(()=>{
  document.body.style.overflow=''
  window.removeEventListener('keydown',onKey)
  if(props.open)previousFocus?.focus({preventScroll:true})
})
</script>

<template>
  <Teleport to="body">
    <Transition name="sheet">
      <div v-if="open" class="sheet-layer" role="presentation">
        <button class="sheet-backdrop" :aria-label="locale.t('close')" tabindex="-1" @click="emit('close')" />
        <section ref="panelRef" class="sheet-panel" role="dialog" aria-modal="true" aria-labelledby="sheet-title" tabindex="-1">
          <div class="mx-auto mt-2 h-1 w-11 rounded-full bg-[var(--c-border)] md:hidden"/>
          <header class="sheet-header">
            <h2 id="sheet-title" class="min-w-0 flex-1 truncate text-base font-extrabold">{{title}}</h2>
            <button v-if="session.isAuthenticated" class="sheet-logout" @click="logoutEverywhere">
              <LogOut :size="17"/><span>{{locale.t('logout')}}</span>
            </button>
            <button class="sheet-close" :aria-label="locale.t('close')" @click="emit('close')"><X :size="20"/></button>
          </header>
          <div class="p-4"><slot/></div>
        </section>
      </div>
    </Transition>
  </Teleport>
</template>
