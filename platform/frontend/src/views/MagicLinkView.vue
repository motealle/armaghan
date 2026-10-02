<script setup lang="ts">
import { onMounted } from 'vue'
import { KeyRound } from '@lucide/vue'
import { useRoute, useRouter } from 'vue-router'
import { useSessionStore } from '@/stores/session'

const route=useRoute()
const router=useRouter()
const session=useSessionStore()

onMounted(async()=>{
  const token=typeof route.params.token==='string'?route.params.token:''
  await session.consumeBackendMagicLink(token)
  await router.replace('/tracking')
})
</script>

<template>
  <section class="mx-auto grid min-h-[45vh] max-w-lg place-items-center text-center">
    <div class="rounded-3xl border border-[var(--c-border)] bg-[var(--c-surface)] p-7 shadow-sm">
      <KeyRound :size="30" class="mx-auto text-[var(--c-primary)]"/>
      <p class="mt-3 text-sm leading-7 text-[var(--c-muted)]">در حال بررسی لینک ورود امن…</p>
    </div>
  </section>
</template>
