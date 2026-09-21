<script setup lang="ts">
import { nextTick, onBeforeUnmount, ref, watch } from 'vue'
import { X } from '@lucide/vue'
import { useLocaleStore } from '@/stores/locale'

const props=defineProps<{open:boolean;title:string;wide?:boolean}>()
const emit=defineEmits<{close:[]}>()
const locale=useLocaleStore()
const panelRef=ref<HTMLElement|null>(null)
let previousFocus:HTMLElement|null=null
const focusable='a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])'

function controls(){return panelRef.value?Array.from(panelRef.value.querySelectorAll<HTMLElement>(focusable)):[]}
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
watch(()=>props.open,async(open,wasOpen)=>{
  document.body.style.overflow=open?'hidden':''
  if(open){
    previousFocus=document.activeElement instanceof HTMLElement?document.activeElement:null
    window.addEventListener('keydown',onKey)
    await nextTick()
    ;(panelRef.value?.querySelector<HTMLElement>('[autofocus]')??controls()[0]??panelRef.value)?.focus({preventScroll:true})
  }else{
    window.removeEventListener('keydown',onKey)
    if(wasOpen){await nextTick();previousFocus?.focus({preventScroll:true});previousFocus=null}
  }
},{immediate:true})
onBeforeUnmount(()=>{
  document.body.style.overflow=''
  window.removeEventListener('keydown',onKey)
})
</script>

<template>
  <Teleport to="body">
    <Transition name="adaptive">
      <div v-if="open" class="adaptive-layer" role="presentation">
        <button class="adaptive-backdrop" :aria-label="locale.t('close')" tabindex="-1" @click="emit('close')"/>
        <section
          ref="panelRef"
          class="adaptive-panel"
          :class="{wide}"
          role="dialog"
          aria-modal="true"
          aria-labelledby="adaptive-title"
          tabindex="-1"
        >
          <div class="adaptive-handle lg:hidden"/>
          <header class="adaptive-header">
            <h2 id="adaptive-title" class="min-w-0 flex-1 truncate text-base font-black text-[var(--c-text)]">{{title}}</h2>
            <button class="adaptive-close" :aria-label="locale.t('close')" @click="emit('close')"><X :size="20"/></button>
          </header>
          <div class="adaptive-body"><slot/></div>
        </section>
      </div>
    </Transition>
  </Teleport>
</template>
