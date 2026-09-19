<script setup lang="ts">
import BaseSheet from '@/components/ui/BaseSheet.vue'
import { colorSets, designSystems, useDesignStore } from '@/stores/design'

defineProps<{ open: boolean }>()
const emit = defineEmits<{ close: [] }>()
const design = useDesignStore()
</script>

<template>
  <BaseSheet :open="open" title="Design Lab" @close="emit('close')">
    <section>
      <h3 class="mb-2 text-sm font-extrabold">Design System</h3>
      <div class="grid gap-2">
        <button
          v-for="item in designSystems"
          :key="item.id"
          class="rounded-xl border p-3 text-start"
          :class="design.system === item.id ? 'border-[var(--c-primary)] bg-indigo-50' : 'border-slate-200 bg-white'"
          @click="design.system = item.id"
        >
          <b class="text-sm">{{ item.name }}</b>
          <small class="mt-1 block text-slate-500">{{ item.description }}</small>
        </button>
      </div>
    </section>
    <section class="mt-6">
      <h3 class="mb-2 text-sm font-extrabold">Color Set</h3>
      <div class="grid gap-2">
        <button
          v-for="item in colorSets"
          :key="item.id"
          class="rounded-xl border p-3 text-start"
          :class="design.palette === item.id ? 'border-[var(--c-primary)]' : 'border-slate-200'"
          @click="design.palette = item.id"
        >
          <b class="text-sm">{{ item.name }}</b>
          <span class="mt-2 flex gap-1">
            <i v-for="color in item.colors" :key="color" class="h-6 flex-1 rounded-md border border-black/5" :style="{ background: color }" />
          </span>
        </button>
      </div>
    </section>
  </BaseSheet>
</template>
