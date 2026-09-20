<script setup lang="ts">
import { nextTick, onBeforeUnmount, ref, watch } from 'vue'
import { X } from '@lucide/vue'

const props=defineProps<{open:boolean;title:string}>()
const emit=defineEmits<{close:[]}>()
const panelRef=ref<HTMLElement|null>(null)
let previousFocus:HTMLElement|null=null
const focusable='a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])'

function controls(){return panelRef.value?Array.from(panelRef.value.querySelectorAll<HTMLElement>(focusable)):[]}
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
watch(()=>props.open,async(open,wasOpen)=>{
  document.body.style.overflow=open?'hidden':''
  if(open){
    previousFocus=document.activeElement instanceof HTMLElement?document.activeElement:null
    window.addEventListener('keydown',onKey)
    await nextTick()
    focusFirst()
  }else{
    window.removeEventListener('keydown',onKey)
    if(wasOpen){await nextTick();previousFocus?.focus({preventScroll:true});previousFocus=null}
  }
},{immediate:true})
onBeforeUnmount(()=>{document.body.style.overflow='';window.removeEventListener('keydown',onKey)})
</script>

<template>
  <Teleport to="body">
    <div v-if="open" class="modal-layer" role="presentation">
      <button class="modal-backdrop" aria-label="بستن" tabindex="-1" @click="emit('close')" />
      <section ref="panelRef" class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="modal-title" tabindex="-1">
        <header class="modal-header">
          <div>
            <div class="text-[10px] font-black tracking-[.16em] text-[var(--c-primary)]">ARMAGHAN</div>
            <h2 id="modal-title" class="mt-1 text-lg font-black text-[var(--c-text)]">{{title}}</h2>
          </div>
          <button class="modal-close" aria-label="بستن" @click="emit('close')"><X :size="20"/></button>
        </header>
        <div class="p-5"><slot/></div>
      </section>
    </div>
  </Teleport>
</template>
