<script setup lang="ts">
const props=withDefaults(defineProps<{ size?: number; decorative?: boolean; tone?: 'green'|'white' }>(), { size: 22, decorative: true, tone: 'green' })
</script>

<template>
  <img
    :src="props.tone==='white'?'./icons/whatsapp-white.svg':'./icons/whatsapp.svg'"
    class="whatsapp-brand-icon"
    :width="size"
    :height="size"
    :alt="decorative ? '' : 'WhatsApp'"
    :aria-hidden="decorative ? 'true' : undefined"
  />
</template>
