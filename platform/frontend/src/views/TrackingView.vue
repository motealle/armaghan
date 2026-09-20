<script setup lang="ts">
import { LogIn, ShieldCheck, UserRound, UserRoundX } from '@lucide/vue'
import { useSessionStore } from '@/stores/session'
import { useLocaleStore } from '@/stores/locale'
import AdminDashboard from '@/features/admin/components/AdminDashboard.vue'
import CustomerDashboard from '@/features/customers/components/CustomerDashboard.vue'

const session=useSessionStore()
const locale=useLocaleStore()
const emit=defineEmits<{login:[]}>()
</script>
<template>
  <section>
    <div class="mb-4"><h1 class="text-[1.75rem] font-black leading-tight text-[var(--c-text)]">{{locale.t('account')}}</h1><p class="mt-1 text-sm leading-6 text-[var(--c-muted)]">{{locale.t('accountHelp')}}</p></div>

    <div v-if="!session.isAuthenticated" class="rounded-3xl border border-[var(--c-border)] bg-[var(--c-surface)] p-7 text-center shadow-sm">
      <div class="mx-auto mb-3 grid h-14 w-14 place-items-center rounded-2xl bg-[color-mix(in_srgb,var(--c-primary)_8%,var(--c-surface))] text-[var(--c-primary)]"><UserRound :size="25"/></div>
      <h2 class="text-xl font-black text-[var(--c-text)]">{{locale.t('signInToTrack')}}</h2>
      <p class="mt-2 text-sm leading-6 text-[var(--c-muted)]">{{locale.t('signInToTrackHelp')}}</p>
      <button class="mt-4 inline-flex min-h-12 items-center gap-2 rounded-xl bg-[var(--c-primary)] px-5 font-bold text-white" @click="emit('login')"><LogIn :size="18"/>{{locale.t('signIn')}}</button>
    </div>

    <div v-else>
      <div v-if="session.impersonatedCustomerId" class="mb-4 flex flex-wrap items-center gap-2 rounded-xl border border-[var(--c-border)] bg-[var(--c-surface-2)] p-3 text-xs text-[var(--c-text)]">
        <UserRoundX :size="18"/>
        <b>{{locale.t('impersonate')}} #{{session.impersonatedCustomerId}}</b>
        <button class="ms-auto rounded-lg border border-[var(--c-border)] bg-[var(--c-surface)] px-3 py-1.5 font-bold" @click="session.stopImpersonating()">{{locale.t('back')}}</button>
      </div>
      <div class="mb-4 flex items-center gap-2 rounded-xl border border-[var(--c-border)] bg-[color-mix(in_srgb,var(--c-secondary)_7%,var(--c-surface))] p-3 text-xs text-[var(--c-secondary)]"><ShieldCheck :size="18"/><b>{{session.isAdmin&&!session.impersonatedCustomerId?locale.t('adminOverview'):locale.t('drawerSummary')}}</b></div>
      <CustomerDashboard v-if="session.impersonatedCustomerId||session.isCustomer"/>
      <AdminDashboard v-else/>
    </div>
  </section>
</template>
