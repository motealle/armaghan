<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import { Eye, EyeOff, Palette, RotateCcw, Type, X } from '@lucide/vue'
import { useLocaleStore } from '@/stores/locale'
import { brandTokens, type BrandTokenId } from './tokenRegistry'
import { collectEditableCandidates, type EditableCandidate } from './selection'
import { useVisualStyleStore, type ElementStyleOverride } from './store'

const visual=useVisualStyleStore()
const locale=useLocaleStore()
const selected=ref<EditableCandidate|null>(null)
const candidates=ref<EditableCandidate[]>([])
const sheetHeight=ref(0)
let pointerStart:{x:number;y:number}|null=null
let resizeState:{pointerId:number;startY:number;startHeight:number}|null=null

const currentStyle=computed<ElementStyleOverride>(()=>
  selected.value?visual.profile.styles[selected.value.id]??{}:{},
)
const textValue=computed(()=>selected.value
  ? visual.textOverride(selected.value.id,locale.locale)
      ?? document.querySelector<HTMLElement>(`[data-style-id="${selected.value.id}"]`)?.textContent?.trim()
      ?? ''
  : '',
)

function clampSheetHeight(value:number):number{
  const min=Math.min(190,window.innerHeight*.34)
  const max=Math.max(min,window.innerHeight*.78)
  return Math.min(max,Math.max(min,value))
}

function setDefaultSheetHeight(){
  sheetHeight.value=clampSheetHeight(window.innerHeight*.48)
}

function applyEditorFrame(){
  if(!visual.enabled){
    document.documentElement.classList.remove('visual-editor-active')
    document.documentElement.style.removeProperty('--visual-editor-sheet-height')
    document.querySelectorAll<HTMLElement>('[data-ve-selected]').forEach(el=>delete el.dataset.veSelected)
    return
  }
  document.documentElement.classList.add('visual-editor-active')
  document.documentElement.style.setProperty('--visual-editor-sheet-height',`${sheetHeight.value}px`)
}

function markSelected(){
  document.querySelectorAll<HTMLElement>('[data-ve-selected]').forEach(el=>delete el.dataset.veSelected)
  if(!selected.value)return
  document.querySelectorAll<HTMLElement>(`[data-style-id="${selected.value.id}"]`).forEach(el=>{
    el.dataset.veSelected='true'
  })
}

function choose(candidate:EditableCandidate){
  selected.value=candidate
  candidates.value=[]
}

function selectAt(event:PointerEvent){
  const list=collectEditableCandidates(event.clientX,event.clientY,event.width,event.height)
  if(!list.length){
    selected.value=null
    candidates.value=[]
    return
  }
  if(list.length===1){
    choose(list[0]!)
    return
  }
  selected.value=null
  candidates.value=list
}

function onPointerDown(event:PointerEvent){
  if(!visual.enabled)return
  const target=event.target as Element|null
  if(target?.closest('[data-visual-editor-ui]'))return
  pointerStart={x:event.clientX,y:event.clientY}
}

function onPointerUp(event:PointerEvent){
  if(!visual.enabled||!pointerStart)return
  const target=event.target as Element|null
  const start=pointerStart
  pointerStart=null
  if(target?.closest('[data-visual-editor-ui]'))return
  const distance=Math.hypot(event.clientX-start.x,event.clientY-start.y)
  if(distance>10)return
  event.preventDefault()
  event.stopPropagation()
  selectAt(event)
}

function onClickCapture(event:MouseEvent){
  if(!visual.enabled)return
  const target=event.target as Element|null
  if(target?.closest('[data-visual-editor-ui]'))return
  event.preventDefault()
  event.stopPropagation()
}

function onResizeStart(event:PointerEvent){
  event.preventDefault()
  resizeState={pointerId:event.pointerId,startY:event.clientY,startHeight:sheetHeight.value}
  ;(event.currentTarget as HTMLElement).setPointerCapture(event.pointerId)
}
function onResizeMove(event:PointerEvent){
  if(!resizeState||resizeState.pointerId!==event.pointerId)return
  sheetHeight.value=clampSheetHeight(resizeState.startHeight+(resizeState.startY-event.clientY))
}
function onResizeEnd(event:PointerEvent){
  if(!resizeState||resizeState.pointerId!==event.pointerId)return
  resizeState=null
  const handle=event.currentTarget as HTMLElement
  if(handle.hasPointerCapture(event.pointerId))handle.releasePointerCapture(event.pointerId)
}

function setToken(key:'textColor'|'backgroundColor'|'borderColor',value:BrandTokenId){
  if(!selected.value)return
  visual.patchStyle(selected.value.id,{[key]:value})
}
function clearToken(key:'textColor'|'backgroundColor'|'borderColor'){
  if(!selected.value)return
  visual.clearStyleProperty(selected.value.id,key)
}
function updateText(event:Event){
  if(!selected.value||!selected.value.textEditable)return
  visual.setText(selected.value.id,locale.locale,(event.target as HTMLTextAreaElement).value)
}
function toggleHidden(){
  if(!selected.value)return
  visual.patchStyle(selected.value.id,{hidden:!currentStyle.value.hidden})
}
function resetSelected(){
  if(!selected.value)return
  visual.resetElement(selected.value.id)
}
function disableEditor(){
  visual.setEnabled(false)
  selected.value=null
  candidates.value=[]
}

onMounted(()=>{
  setDefaultSheetHeight()
  document.addEventListener('pointerdown',onPointerDown,true)
  document.addEventListener('pointerup',onPointerUp,true)
  document.addEventListener('click',onClickCapture,true)
  window.addEventListener('resize',setDefaultSheetHeight)
  applyEditorFrame()
})
onUnmounted(()=>{
  document.removeEventListener('pointerdown',onPointerDown,true)
  document.removeEventListener('pointerup',onPointerUp,true)
  document.removeEventListener('click',onClickCapture,true)
  window.removeEventListener('resize',setDefaultSheetHeight)
  document.documentElement.classList.remove('visual-editor-active')
  document.documentElement.style.removeProperty('--visual-editor-sheet-height')
  document.querySelectorAll<HTMLElement>('[data-ve-selected]').forEach(el=>delete el.dataset.veSelected)
})
watch(()=>visual.enabled,applyEditorFrame)
watch(sheetHeight,applyEditorFrame)
watch(selected,markSelected)
</script>

<template>
  <section
    v-if="visual.enabled"
    class="visual-editor-sheet"
    data-visual-editor-ui
    :style="{height:`${sheetHeight}px`}"
    aria-label="ویرایش دیداری صفحه"
  >
    <button
      type="button"
      class="visual-editor-handle"
      aria-label="تغییر ارتفاع پنل"
      @pointerdown="onResizeStart"
      @pointermove="onResizeMove"
      @pointerup="onResizeEnd"
      @pointercancel="onResizeEnd"
    ><span></span></button>

    <header class="visual-editor-header">
      <div>
        <b>ویرایش دیداری</b>
        <small>{{selected?.label ?? (candidates.length?'انتخاب عنصر':'یک بخش از صفحه را لمس کنید')}}</small>
      </div>
      <span class="visual-editor-autosave">ذخیره خودکار</span>
      <button type="button" class="visual-editor-icon-button" aria-label="خاموش کردن ادیتور" @click="disableEditor">
        <X :size="19"/>
      </button>
    </header>

    <div class="visual-editor-body">
      <section v-if="candidates.length" class="visual-editor-candidates">
        <b>کدام بخش را می‌خواهید؟</b>
        <p>چند عنصر نزدیک به لمس شما پیدا شد.</p>
        <div>
          <button v-for="candidate in candidates" :key="candidate.id" type="button" @click="choose(candidate)">
            <span>{{candidate.label}}</span>
            <small>{{candidate.tag}} · {{candidate.id}}</small>
          </button>
        </div>
      </section>

      <template v-else-if="selected">
        <section v-if="selected.textEditable" class="visual-editor-control-group">
          <div class="visual-editor-control-title"><Type :size="17"/><b>متن</b></div>
          <textarea
            :value="textValue"
            rows="2"
            maxlength="4000"
            aria-label="متن عنصر انتخاب‌شده"
            @input="updateText"
          ></textarea>
          <small>متن برای زبان فعلی ذخیره می‌شود و پیش‌نمایش زنده دارد.</small>
        </section>

        <section class="visual-editor-control-group">
          <div class="visual-editor-control-title"><Palette :size="17"/><b>رنگ متن</b></div>
          <div class="visual-token-grid">
            <button type="button" :class="{active:!currentStyle.textColor}" @click="clearToken('textColor')">پیش‌فرض</button>
            <button
              v-for="token in brandTokens"
              :key="`text-${token.id}`"
              type="button"
              class="visual-token-button"
              :class="{active:currentStyle.textColor===token.id}"
              :aria-label="`رنگ متن: ${token.label}`"
              @click="setToken('textColor',token.id)"
            ><i :style="{background:token.value}"></i><span>{{token.label}}</span></button>
          </div>
        </section>

        <section class="visual-editor-control-group">
          <div class="visual-editor-control-title"><Palette :size="17"/><b>رنگ زمینه</b></div>
          <div class="visual-token-grid">
            <button type="button" :class="{active:!currentStyle.backgroundColor}" @click="clearToken('backgroundColor')">پیش‌فرض</button>
            <button
              v-for="token in brandTokens"
              :key="`bg-${token.id}`"
              type="button"
              class="visual-token-button"
              :class="{active:currentStyle.backgroundColor===token.id}"
              :aria-label="`رنگ زمینه: ${token.label}`"
              @click="setToken('backgroundColor',token.id)"
            ><i :style="{background:token.value}"></i><span>{{token.label}}</span></button>
          </div>
        </section>

        <section class="visual-editor-control-group">
          <div class="visual-editor-control-title"><Palette :size="17"/><b>رنگ خط/جداکننده</b></div>
          <div class="visual-token-grid">
            <button type="button" :class="{active:!currentStyle.borderColor}" @click="clearToken('borderColor')">پیش‌فرض</button>
            <button
              v-for="token in brandTokens"
              :key="`border-${token.id}`"
              type="button"
              class="visual-token-button"
              :class="{active:currentStyle.borderColor===token.id}"
              :aria-label="`رنگ خط: ${token.label}`"
              @click="setToken('borderColor',token.id)"
            ><i :style="{background:token.value}"></i><span>{{token.label}}</span></button>
          </div>
        </section>

        <section class="visual-editor-row-actions">
          <button type="button" class="visual-editor-action" @click="toggleHidden">
            <Eye v-if="currentStyle.hidden" :size="17"/><EyeOff v-else :size="17"/>
            {{currentStyle.hidden?'نمایش دوباره':'مخفی کردن'}}
          </button>
          <button type="button" class="visual-editor-action" @click="resetSelected">
            <RotateCcw :size="17"/>بازنشانی این عنصر
          </button>
        </section>
      </template>

      <section v-else class="visual-editor-empty">
        <b>صفحه هنوز قابل لمس و اسکرول است.</b>
        <p>روی هر بخش قابل ویرایش بزنید. اگر چند عنصر نزدیک باشند، اول از شما می‌پرسیم کدام را می‌خواهید.</p>
      </section>
    </div>
  </section>
</template>
