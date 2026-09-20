<script setup lang="ts">
import { computed } from 'vue'
import { Moon, Sun } from '@lucide/vue'
import { useThemeStore } from '@/stores/theme'
import { useLocaleStore } from '@/stores/locale'

const theme=useThemeStore()
const locale=useLocaleStore()
const label=computed(()=>theme.mode==='dark'?locale.t('themeToLight'):locale.t('themeToDark'))
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
