<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { RefreshCw } from '@lucide/vue'
import AdminDashboard from '@/features/admin/components/AdminDashboard.vue'
import { useAdminStore } from '@/features/admin/store'
import { useLocaleStore } from '@/stores/locale'
const emit=defineEmits<{login:[]}>()
const admin=useAdminStore()
const locale=useLocaleStore()
const busy=ref(true)
async function check(){busy.value=true; await admin.hydrate(); busy.value=false}
onMounted(check)
</script>
<template>
  <section>
    <div class="mb-4 flex flex-wrap items-center gap-3">
      <h1 class="text-xl font-black">{{locale.t('adminOverview')}}</h1>
      <span v-if="admin.identity" class="text-sm" dir="ltr">{{admin.identity.email}}</span>
    </div>
    <p v-if="busy" role="status">{{locale.t('adminLoading')}}</p>
    <AdminDashboard v-else-if="admin.identity" live/>
    <div v-else class="admin-surface rounded-2xl p-5">
      <p>{{locale.t('adminSessionRequired')}}</p>
      <button class="mini-action mt-3" @click="check"><RefreshCw :size="16"/>{{locale.t('adminReload')}}</button>
      <button class="mini-action mt-3 ms-3" @click="emit('login')">{{locale.t('adminSignIn')}}</button>
    </div>
  </section>
</template>
