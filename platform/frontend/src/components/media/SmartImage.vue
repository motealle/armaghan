<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { ImageOff, Shirt } from '@lucide/vue'

const props = withDefaults(defineProps<{
  src?: string
  alt: string
  label?: string
  aspect?: 'hero' | 'card' | 'square'
  eager?: boolean
}>(), { label: 'تصویر محصول', aspect: 'card', eager: false })

const loaded = ref(false)
const failed = ref(false)
const ratioClass = computed(() => props.aspect === 'hero' ? 'aspect-[16/9]' : props.aspect === 'square' ? 'aspect-square' : 'aspect-[4/3]')

watch(() => props.src, () => {
  loaded.value = false
  failed.value = false
})
</script>

<template>
  <div class="smart-image relative overflow-hidden" :class="ratioClass">
    <div class="absolute inset-0 grid place-items-center bg-gradient-to-br from-[color-mix(in_srgb,var(--c-soft)_74%,white)] via-slate-100 to-indigo-50 text-[var(--c-primary)]">
      <div class="absolute -start-8 -top-8 h-24 w-24 rounded-full bg-white/50" />
      <div class="absolute -bottom-10 -end-6 h-28 w-28 rounded-full bg-[color-mix(in_srgb,var(--c-primary)_8%,transparent)]" />
      <div class="relative flex flex-col items-center gap-3 text-center">
        <div class="fallback-clay-icon">
          <Shirt v-if="src" :size="34" :stroke-width="1.65" />
          <ImageOff v-else :size="34" :stroke-width="1.65" />
        </div>
        <span class="max-w-28 text-[10px] font-bold text-[var(--c-muted)]">{{ label }}</span>
      </div>
    </div>
    <div v-if="src && !loaded && !failed" class="skeleton absolute inset-0 z-10" />
    <img
      v-if="src && !failed"
      class="absolute inset-0 z-20 h-full w-full object-cover transition-opacity duration-200"
      :class="{ 'opacity-0': !loaded }"
      :src="src"
      :alt="alt"
      :loading="eager ? 'eager' : 'lazy'"
      @load="loaded = true"
      @error="failed = true"
    />
  </div>
</template>
