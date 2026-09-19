<script setup lang="ts">
import { LogIn, ShieldCheck, UserRound } from '@lucide/vue'
import { useSessionStore } from '@/stores/session'
import AdminDashboard from '@/features/admin/components/AdminDashboard.vue'
import CustomerDashboard from '@/features/customers/components/CustomerDashboard.vue'

const session=useSessionStore()
const emit=defineEmits<{ login: [] }>()
</script>
<template>
  <section>
    <div class="mb-4"><h1 class="text-2xl font-black">پیگیری و حساب</h1><p class="mt-1 text-sm text-slate-500">سفارشات، تایملاین، مطلوب‌ها و مدیریت مشتری.</p></div>
    <div v-if="!session.isAuthenticated" class="rounded-3xl border border-slate-200 bg-white p-7 text-center shadow-sm">
      <div class="mx-auto mb-3 grid h-14 w-14 place-items-center rounded-2xl bg-indigo-50 text-[var(--c-primary)]"><UserRound :size="25"/></div>
      <h2 class="text-xl font-black">برای پیگیری وارد شوید</h2><p class="mt-2 text-sm text-slate-500">ورود آزمایشی مدیر 1/1 و مشتری 2/2.</p>
      <button class="mt-4 inline-flex min-h-12 items-center gap-2 rounded-xl bg-[var(--c-primary)] px-5 font-bold text-white" @click="emit('login')"><LogIn :size="18"/> ورود</button>
    </div>
    <div v-else>
      <div class="mb-4 flex items-center gap-2 rounded-xl bg-emerald-50 p-3 text-xs text-emerald-800"><ShieldCheck :size="18"/><b>{{session.isAdmin?'حالت مدیر':'حالت مشتری'}}</b><span>— خروج از حساب همیشه از نوار بالای صفحه در دسترس است.</span></div>
      <AdminDashboard v-if="session.isAdmin"/><CustomerDashboard v-else/>
    </div>
  </section>
</template>
