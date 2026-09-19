<script setup lang="ts">
withDefaults(defineProps<{ size?: number; decorative?: boolean }>(), { size: 22, decorative: true })
</script>

<template>
  <img
    src="./icons/whatsapp.svg"
    class="whatsapp-brand-icon"
    :width="size"
    :height="size"
    :alt="decorative ? '' : 'WhatsApp'"
    :aria-hidden="decorative ? 'true' : undefined"
  />
</template>
