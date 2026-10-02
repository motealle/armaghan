<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { LogOut, RefreshCw } from '@lucide/vue'
import AdminDashboard from '@/features/admin/components/AdminDashboard.vue'
import { useAdminStore } from '@/features/admin/store'
import { useLocaleStore } from '@/stores/locale'
const admin=useAdminStore()
const locale=useLocaleStore()
const busy=ref(true)
const error=ref(false)
async function check(){busy.value=true; await admin.hydrate(); busy.value=false}
async function signOut(){
  busy.value=true; error.value=false
  try{await admin.logout()}catch{error.value=true}
  finally{busy.value=false}
}
onMounted(check)
</script>
<template>
  <section>
    <div class="mb-4 flex flex-wrap items-center gap-3">
      <h1 class="text-xl font-black">{{locale.t('adminOverview')}}</h1>
      <span v-if="admin.identity" class="text-sm" dir="ltr">{{admin.identity.email}}</span>
      <button v-if="admin.identity" class="mini-action ms-auto" :disabled="busy" @click="signOut"><LogOut :size="17"/>{{locale.t('logout')}}</button>
    </div>
    <p v-if="error" class="auth-error" role="alert">{{locale.t('logoutFailed')}}</p>
    <p v-if="busy" role="status">{{locale.t('adminLoading')}}</p>
    <AdminDashboard v-else-if="admin.identity" live/>
    <div v-else class="admin-surface rounded-2xl p-5">
      <p>{{locale.t('adminSessionRequired')}}</p>
      <button class="mini-action mt-3" @click="check"><RefreshCw :size="16"/>{{locale.t('adminReload')}}</button>
      <a class="mini-action mt-3 ms-3" href="/backend/admin/login">{{locale.t('adminSignIn')}}</a>
    </div>
  </section>
</template>
