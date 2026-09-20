<script setup lang="ts">
import { Monitor, Moon, Sun } from '@lucide/vue'
import { useThemeStore, type ThemeMode } from '@/stores/theme'

const theme=useThemeStore()
const modes:{id:ThemeMode;label:string;icon:typeof Sun}[]=[
  {id:'system',label:'سیستم',icon:Monitor},
  {id:'light',label:'روشن',icon:Sun},
  {id:'dark',label:'تیره',icon:Moon},
]
</script>

<template>
  <div class="theme-switcher" aria-label="حالت نمایش">
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
