<script setup lang="ts">
import { onMounted, ref } from 'vue'
import AppHeader from '@/components/layout/AppHeader.vue'
import BottomNav from '@/components/layout/BottomNav.vue'
import LoginSheet from '@/features/auth/components/LoginSheet.vue'
import DesignLabSheet from '@/features/design/components/DesignLabSheet.vue'
import { useDesignStore } from '@/stores/design'
import { useSessionStore } from '@/stores/session'

const loginOpen=ref(false)
const designLabOpen=ref(false)
const design=useDesignStore()
const session=useSessionStore()

onMounted(()=>{
  design.apply()
  const magic=new URLSearchParams(location.search).get('magic')
  if(magic==='customer-demo-token'){
    session.login('2','2')
    location.hash='#/tracking'
  }
})
</script>

<template>
  <div class="min-h-screen">
    <AppHeader @login="loginOpen=true" @designlab="designLabOpen=true"/>
    <main class="mx-auto max-w-6xl px-3 py-4 pb-28 md:py-6">
      <RouterView v-slot="{ Component }">
        <component :is="Component" @login="loginOpen=true" />
      </RouterView>
      <footer class="mt-12 border-t border-slate-200 py-6 text-center text-[11px] text-slate-500">
        <div>نسخه آزمایشی محصول · داده‌های نمایشی</div>
        <RouterLink to="/credits" class="mt-1 inline-block font-bold text-[var(--c-primary)]">منابع و مجوز تصاویر آزمایشی</RouterLink>
      </footer>
    </main>
    <BottomNav/>
    <LoginSheet :open="loginOpen" @close="loginOpen=false"/>
    <DesignLabSheet :open="designLabOpen" @close="designLabOpen=false"/>
  </div>
</template>
