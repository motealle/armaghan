<script setup lang="ts">
import { Pencil } from '@lucide/vue'
import { useAdminStore } from '@/features/admin/store'
import { useHomeMediaStore } from './store'
import { useLocaleStore } from '@/stores/locale'

const props=defineProps<{target:string;label:string}>()
const admin=useAdminStore(),homeMedia=useHomeMediaStore(),locale=useLocaleStore()
const editText=()=>({fa:'ویرایش عکس',en:'Edit image',ar:'تعديل الصورة',ku:'دەستکاری وێنە'})[locale.locale]
function edit(){homeMedia.requestEdit(props.target)}
</script>

<template>
  <button
    v-if="admin.identity"
    type="button"
    class="absolute end-2 top-2 z-50 inline-flex min-h-9 items-center gap-1.5 rounded-xl border border-white/70 bg-white/95 px-3 text-xs font-black text-[var(--c-primary)] shadow-lg backdrop-blur"
    :aria-label="`${editText()} · ${label}`"
    @click.stop.prevent="edit"
  >
    <Pencil :size="15"/><span>{{editText()}}</span>
  </button>
</template>
