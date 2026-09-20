<script setup lang="ts">
import { ref } from 'vue'
import { Globe2, KeyRound, LogIn } from '@lucide/vue'
import { useRouter } from 'vue-router'
import { useSessionStore } from '@/stores/session'
import BaseModal from '@/components/ui/BaseModal.vue'

defineProps<{ open: boolean }>()
const emit=defineEmits<{ close: [] }>()
const session=useSessionStore()
const router=useRouter()
const username=ref('')
const password=ref('')
const error=ref('')

function submit(){
  error.value=''
  if(!session.login(username.value.trim(),password.value)){
    error.value='نام کاربری یا رمز آزمایشی نادرست است.'
    return
  }
  emit('close')
  router.push('/tracking')
}
</script>

<template>
  <BaseModal :open="open" title="ورود آزمایشی" @close="emit('close')">
    <form class="space-y-4" @submit.prevent="submit">
      <div class="rounded-xl border border-amber-200/70 bg-amber-50 p-3 text-xs leading-6 text-amber-900 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-100">
        مدیر: <b>1 / 1</b> · مشتری: <b>2 / 2</b>. این ورود فقط برای تست رابط است.
      </div>

      <label class="block text-xs font-black text-[var(--c-text)]">
        نام کاربری
        <input v-model="username" autocomplete="username" autofocus class="modal-input" />
      </label>

      <label class="block text-xs font-black text-[var(--c-text)]">
        رمز عبور
        <input v-model="password" type="password" autocomplete="current-password" class="modal-input" />
      </label>

      <p v-if="error" class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 dark:bg-rose-950/30 dark:text-rose-200">{{error}}</p>

      <button class="flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-[var(--c-primary)] font-extrabold text-white shadow-lg shadow-indigo-950/10">
        <LogIn :size="19"/> ورود
      </button>

      <div class="grid grid-cols-2 gap-2">
        <button type="button" class="modal-secondary-action" @click="error='Google Login در Laravel/Socialite فعال می‌شود.'">
          <Globe2 :size="17"/> Google
        </button>
        <button type="button" class="modal-secondary-action" @click="error='Magic Link امن در بک‌اند Laravel فعال می‌شود.'">
          <KeyRound :size="17"/> لینک ورود
        </button>
      </div>
    </form>
  </BaseModal>
</template>
