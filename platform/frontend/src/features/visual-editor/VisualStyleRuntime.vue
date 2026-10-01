<script setup lang="ts">
import { onMounted, onUnmounted, watchEffect } from 'vue'
import { useVisualStyleStore } from './store'
import { usePublicVisualProfileBaseline } from './composables/usePublicVisualProfileBaseline'

const visual=useVisualStyleStore()
usePublicVisualProfileBaseline(visual)
const STYLE_ID='armaghan-visual-style-profile'

function ensureStyle():HTMLStyleElement{
  let element=document.getElementById(STYLE_ID) as HTMLStyleElement|null
  if(!element){
    element=document.createElement('style')
    element.id=STYLE_ID
    document.head.appendChild(element)
  }
  return element
}

onMounted(()=>{
  watchEffect(()=>{
    ensureStyle().textContent=visual.compiledCss
  })
})

onUnmounted(()=>{
  document.getElementById(STYLE_ID)?.remove()
})
</script>

<template></template>
