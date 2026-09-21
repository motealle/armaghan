import { defineStore } from 'pinia'
import { ref } from 'vue'

export type ThemeMode='light'|'dark'
const KEY='armaghan:test22:theme'
const query=window.matchMedia('(prefers-color-scheme: dark)')

function initialMode():ThemeMode{
  const saved=localStorage.getItem(KEY)
  if(saved==='light'||saved==='dark')return saved
  return query.matches?'dark':'light'
}

export const useThemeStore=defineStore('theme',()=>{
  const mode=ref<ThemeMode>(initialMode())

  function apply(){
    const root=document.documentElement
    root.classList.toggle('dark',mode.value==='dark')
    root.dataset.theme=mode.value
    root.style.colorScheme=mode.value
  }
  function setMode(value:ThemeMode){
    mode.value=value
    localStorage.setItem(KEY,value)
    apply()
  }
  function toggle(){setMode(mode.value==='dark'?'light':'dark')}

  apply()
  return{mode,setMode,toggle,apply}
})
