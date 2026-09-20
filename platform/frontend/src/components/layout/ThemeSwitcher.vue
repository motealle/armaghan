<script setup lang="ts">
import { computed } from 'vue'
import { Monitor, Moon, Sun } from '@lucide/vue'
import { useThemeStore, type ThemeMode } from '@/stores/theme'

const theme=useThemeStore()
const modes:{id:ThemeMode;label:string;icon:typeof Sun}[]=[
  {id:'system',label:'سیستم',icon:Monitor},
  {id:'light',label:'روشن',icon:Sun},
  {id:'dark',label:'تیره',icon:Moon},
]
const current=computed(()=>modes.find(item=>item.id===theme.mode) ?? modes[0]!)
function cycle(){
  const index=modes.findIndex(item=>item.id===theme.mode)
  theme.setMode(modes[(index+1)%modes.length]!.id)
}
</script>

<template>
  <button
    type="button"
    class="theme-cycle-btn lg:hidden"
    :title="`حالت نمایش: ${current.label}`"
    :aria-label="`حالت نمایش: ${current.label}`"
    @click.stop="cycle"
  >
    <component :is="current.icon" :size="17"/>
  </button>

  <div class="theme-switcher hidden lg:flex" aria-label="حالت نمایش">
    <button
      v-for="item in modes"
      :key="item.id"
      type="button"
      class="theme-mode-btn"
      :class="{active:theme.mode===item.id}"
      :aria-pressed="theme.mode===item.id"
      :title="item.label"
      @click.stop="theme.setMode(item.id)"
    >
      <component :is="item.icon" :size="16"/>
      <span class="hidden xl:inline">{{item.label}}</span>
    </button>
  </div>
</template>
