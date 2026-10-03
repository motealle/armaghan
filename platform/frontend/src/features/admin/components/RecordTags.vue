<script setup lang="ts">
defineProps<{tags?:string[]}>()
</script>
<template>
  <div v-if="tags?.length" class="mt-1 flex flex-wrap gap-1"><span v-for="tag in tags" :key="tag" class="rounded-full border border-[var(--c-border)] bg-[var(--c-surface-2)] px-2 py-0.5 text-xs text-[var(--c-text)]">{{tag}}</span></div>
</template>
