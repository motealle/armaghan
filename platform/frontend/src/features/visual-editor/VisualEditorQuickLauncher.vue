<script setup lang="ts">
import { Paintbrush } from '@lucide/vue'
import { useRouter } from 'vue-router'
import { useVisualStyleStore } from './store'

const router=useRouter()
const visual=useVisualStyleStore()

async function openEditor(){
  visual.setEnabled(true)
  await router.push('/')
}
</script>

<template>
  <button
    v-if="!visual.enabled"
    type="button"
    class="visual-editor-quick-launcher"
    data-visual-editor-ui
    aria-label="باز کردن ویرایش دیداری"
    @click="openEditor"
  >
    <Paintbrush :size="18"/>
    <span>ویرایش ظاهر</span>
  </button>
</template>
