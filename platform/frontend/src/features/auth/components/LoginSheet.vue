<script setup lang="ts">
import { ref } from 'vue'
import { Globe2, KeyRound, LogIn } from '@lucide/vue'
import { useRouter } from 'vue-router'
import { useSessionStore } from '@/stores/session'
import BaseSheet from '@/components/ui/BaseSheet.vue'

defineProps<{ open: boolean }>()
const emit = defineEmits<{ close: [] }>()
const session = useSessionStore()
const router = useRouter()
const username = ref('')
const password = ref('')
const error = ref('')

function submit() {
  error.value = ''
  if (!session.login(username.value.trim(), password.value)) {
    error.value = 'نام کاربری یا رمز آزمایشی نادرست است.'
    return
  }
  emit('close')
  router.push('/tracking')
}
</script>

<template>
  <BaseSheet :open="open" title="ورود آزمایشی" @close="emit('close')">
    <form class="space-y-3" @submit.prevent="submit">
      <div class="rounded-xl border border-amber-100 bg-amber-50 p-3 text-xs leading-6 text-amber-900">
        مدیر: <b>1 / 1</b> · مشتری: <b>2 / 2</b>. این ورود فقط برای تست رابط است.
      </div>
      <label class="block text-xs font-bold">
        نام کاربری
        <input v-model="username" autocomplete="username" class="mt-1 w-full rounded-xl border border-slate-200 bg-white p-3 text-sm outline-none focus:border-[var(--c-primary)]" />
      </label>
      <label class="block text-xs font-bold">
        رمز عبور
        <input v-model="password" type="password" autocomplete="current-password" class="mt-1 w-full rounded-xl border border-slate-200 bg-white p-3 text-sm outline-none focus:border-[var(--c-primary)]" />
      </label>
      <p v-if="error" class="text-xs font-bold text-rose-600">{{ error }}</p>
      <button class="flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-[var(--c-primary)] font-extrabold text-white">
        <LogIn :size="19" /> ورود
      </button>
      <div class="grid grid-cols-2 gap-2">
        <button type="button" class="flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-200 text-xs" @click="error='Google Login در Laravel/Socialite فعال می‌شود.'">
          <Globe2 :size="17" /> Google
        </button>
        <button type="button" class="flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-200 text-xs" @click="error='Magic Link امن در بک‌اند Laravel فعال می‌شود.'">
          <KeyRound :size="17" /> لینک ورود
        </button>
      </div>
    </form>
  </BaseSheet>
</template>
