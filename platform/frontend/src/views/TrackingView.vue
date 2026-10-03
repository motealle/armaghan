<script setup lang="ts">
import { LogIn, LogOut, ShieldCheck, UserRound, UserRoundX } from '@lucide/vue'
import { ref } from 'vue'
import { useRoute } from 'vue-router'
import { useSessionStore } from '@/stores/session'
import { useLocaleStore } from '@/stores/locale'
import AdminView from '@/views/AdminView.vue'
import {useAdminStore} from '@/features/admin/store'
import AdminDashboard from '@/features/admin/components/AdminDashboard.vue'
import CustomerDashboard from '@/features/customers/components/CustomerDashboard.vue'

const route=useRoute()
const session=useSessionStore()
const admin=useAdminStore()
const locale=useLocaleStore()
const emit=defineEmits<{login:[]}>()
const loggingOut=ref(false)
const logoutFailed=ref(false)
async function signOut(){
  loggingOut.value=true
  logoutFailed.value=!(await session.logout())
  loggingOut.value=false
}
</script>
<template>
  <section>
    <AdminView v-if="admin.identity" @login="emit('login')"/>
    <template v-else>
    <div v-if="!session.isAuthenticated&&route.query.auth_error==='google'" role="alert" class="auth-error mb-4">
      <p>{{locale.t(route.query.auth_reason==='expired'?'googleSignInExpired':'googleSignInFailed')}}</p>
      <button class="mini-action mt-3" @click="emit('login')"><LogIn :size="16"/>{{locale.t('signIn')}}</button>
    </div>
    <div class="mb-4 flex flex-wrap items-start justify-between gap-3"><div><h1 class="text-[1.75rem] font-black leading-tight text-[var(--c-text)]">{{locale.t(session.isAuthenticated?'panel':'account')}}</h1><p class="mt-1 text-sm leading-6 text-[var(--c-muted)]">{{locale.t('accountHelp')}}</p></div><button v-if="session.isAuthenticated" :disabled="loggingOut" class="mini-action shrink-0" @click="signOut"><LogOut :size="18"/>{{locale.t('logout')}}</button></div>
    <p v-if="logoutFailed" role="alert" class="auth-error mb-4">{{locale.t('logoutFailed')}}</p>

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
    </template>
  </section>
</template>
