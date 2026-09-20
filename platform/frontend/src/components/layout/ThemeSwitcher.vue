<script setup lang="ts">
import { computed } from 'vue'
import { Moon, Sun } from '@lucide/vue'
import { useThemeStore } from '@/stores/theme'

const theme=useThemeStore()
const label=computed(()=>theme.mode==='dark'?'تغییر به حالت روشن':'تغییر به حالت تیره')
</script>

<template>
  <button
    type="button"
    class="theme-cycle-btn"
    :title="label"
    :aria-label="label"
    @pointerdown.stop
    @click.stop="theme.toggle()"
  >
    <Sun v-if="theme.mode==='light'" :size="18"/>
    <Moon v-else :size="18"/>
  </button>
</template>
