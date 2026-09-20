import { defineStore } from 'pinia'
import { computed, ref } from 'vue'

export type ThemeMode='system'|'light'|'dark'
const KEY='armaghan:test16:theme'
const query=window.matchMedia('(prefers-color-scheme: dark)')

export const useThemeStore=defineStore('theme',()=>{
  const saved=localStorage.getItem(KEY)
  const mode=ref<ThemeMode>(saved==='light'||saved==='dark'||saved==='system'?saved:'system')
  const resolved=computed<'light'|'dark'>(()=>mode.value==='system'?(query.matches?'dark':'light'):mode.value)
  function apply(){
    const root=document.documentElement
    root.classList.toggle('dark',resolved.value==='dark')
    root.dataset.theme=resolved.value
    root.style.colorScheme=resolved.value
  }
  function setMode(value:ThemeMode){mode.value=value;localStorage.setItem(KEY,value);apply()}
  function onSystemChange(){if(mode.value==='system')apply()}
  query.addEventListener?.('change',onSystemChange)
  apply()
  return{mode,resolved,setMode,apply}
})
