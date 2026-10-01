<script setup lang="ts">
import { computed } from 'vue'
import { Eye, EyeOff, Palette, RotateCcw, Type } from '@lucide/vue'
import { useLocaleStore } from '@/stores/locale'
import { brandTokens, type BrandTokenId } from './tokenRegistry'
import { NORMAL_TEXT_MIN_CONTRAST, tokenContrastRatio } from './contrast'
import { visualTargetDefinition, type VisualStyleControl } from './targetRegistry'
import type { EditableCandidate } from './selection'
import { useVisualStyleStore, type ElementStyleOverride } from './store'

const props=defineProps<{selected:EditableCandidate}>()
const visual=useVisualStyleStore()
const locale=useLocaleStore()

const currentStyle=computed<ElementStyleOverride>(()=>visual.profile.styles[props.selected.id]??{})
const targetDefinition=computed(()=>visualTargetDefinition(props.selected.id))
const allowedStyleControls=computed<VisualStyleControl[]|null>(()=>targetDefinition.value?.styleControls??null)
const allowsStyle=(key:VisualStyleControl)=>allowedStyleControls.value===null||allowedStyleControls.value.includes(key)
const canHide=computed(()=>targetDefinition.value?.hideable!==false)
const explicitContrast=computed(()=>{
  const text=currentStyle.value.textColor
  const background=currentStyle.value.backgroundColor
  if(!text||!background)return null
  return tokenContrastRatio(text,background)
})
const contrastLabel=computed(()=>{
  if(explicitContrast.value===null)return 'برای کنترل قطعی کنتراست، رنگ متن و زمینه را هر دو از پالت انتخاب کنید.'
  return explicitContrast.value>=NORMAL_TEXT_MIN_CONTRAST
    ? `کنتراست مناسب: ${explicitContrast.value.toFixed(2)}:1`
    : `کنتراست ناکافی: ${explicitContrast.value.toFixed(2)}:1`
})
const textValue=computed(()=>
  visual.textOverride(props.selected.id,locale.locale)
    ?? document.querySelector<HTMLElement>(`[data-style-id="${props.selected.id}"]`)?.textContent?.trim()
    ?? ''
)

function wouldFailContrast(key:'textColor'|'backgroundColor',value:BrandTokenId):boolean{
  const text=key==='textColor'?value:currentStyle.value.textColor
  const background=key==='backgroundColor'?value:currentStyle.value.backgroundColor
  if(!text||!background)return false
  return tokenContrastRatio(text,background)<NORMAL_TEXT_MIN_CONTRAST
}

function setToken(key:'textColor'|'backgroundColor'|'borderColor',value:BrandTokenId){
  if((key==='textColor'||key==='backgroundColor')&&wouldFailContrast(key,value))return
  visual.patchStyle(props.selected.id,{[key]:value})
}
function clearToken(key:'textColor'|'backgroundColor'|'borderColor'){
  visual.clearStyleProperty(props.selected.id,key)
}
function updateText(event:Event){
  if(!props.selected.textEditable)return
  visual.setText(props.selected.id,locale.locale,(event.target as HTMLTextAreaElement).value)
}
function toggleHidden(){
  visual.patchStyle(props.selected.id,{hidden:!currentStyle.value.hidden})
}
function resetSelected(){
  visual.resetElement(props.selected.id)
}
</script>

<template>
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

  <section v-if="allowsStyle('textColor')" class="visual-editor-control-group">
    <div class="visual-editor-control-title"><Palette :size="17"/><b>رنگ متن</b></div>
    <div class="visual-token-grid">
      <button type="button" :class="{active:!currentStyle.textColor}" @click="clearToken('textColor')">پیش‌فرض</button>
      <button
        v-for="token in brandTokens"
        :key="`text-${token.id}`"
        type="button"
        class="visual-token-button"
        :class="{active:currentStyle.textColor===token.id}"
        :disabled="wouldFailContrast('textColor',token.id)"
        :title="wouldFailContrast('textColor',token.id)?'کنتراست متن و زمینه کمتر از 4.5:1 می‌شود':''"
        :aria-label="`رنگ متن: ${token.label}`"
        @click="setToken('textColor',token.id)"
      ><i :style="{background:token.value}"></i><span>{{token.label}}</span></button>
    </div>
  </section>

  <section v-if="allowsStyle('backgroundColor')" class="visual-editor-control-group">
    <div class="visual-editor-control-title"><Palette :size="17"/><b>رنگ زمینه</b></div>
    <div class="visual-token-grid">
      <button type="button" :class="{active:!currentStyle.backgroundColor}" @click="clearToken('backgroundColor')">پیش‌فرض</button>
      <button
        v-for="token in brandTokens"
        :key="`bg-${token.id}`"
        type="button"
        class="visual-token-button"
        :class="{active:currentStyle.backgroundColor===token.id}"
        :disabled="wouldFailContrast('backgroundColor',token.id)"
        :title="wouldFailContrast('backgroundColor',token.id)?'کنتراست متن و زمینه کمتر از 4.5:1 می‌شود':''"
        :aria-label="`رنگ زمینه: ${token.label}`"
        @click="setToken('backgroundColor',token.id)"
      ><i :style="{background:token.value}"></i><span>{{token.label}}</span></button>
    </div>
  </section>

  <p
    v-if="allowsStyle('textColor')&&allowsStyle('backgroundColor')"
    class="visual-editor-contrast"
    :class="{bad:explicitContrast!==null&&explicitContrast<NORMAL_TEXT_MIN_CONTRAST}"
  >{{contrastLabel}}</p>

  <section v-if="allowsStyle('borderColor')" class="visual-editor-control-group">
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
    <button v-if="canHide" type="button" class="visual-editor-action" @click="toggleHidden">
      <Eye v-if="currentStyle.hidden" :size="17"/><EyeOff v-else :size="17"/>
      {{currentStyle.hidden?'نمایش دوباره':'مخفی کردن'}}
    </button>
    <button type="button" class="visual-editor-action" @click="resetSelected">
      <RotateCcw :size="17"/>بازنشانی این عنصر
    </button>
  </section>
</template>
