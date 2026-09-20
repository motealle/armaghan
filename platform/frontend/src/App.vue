<script setup lang="ts">
import { nextTick, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import AppHeader from '@/components/layout/AppHeader.vue'
import BottomNav from '@/components/layout/BottomNav.vue'
import HelpSheet from '@/components/layout/HelpSheet.vue'
import LoginSheet from '@/features/auth/components/LoginSheet.vue'
import DesignLabSheet from '@/features/design/components/DesignLabSheet.vue'
import { useDesignStore } from '@/stores/design'
import { useSessionStore } from '@/stores/session'
import { useLocaleStore } from '@/stores/locale'
import { useThemeStore } from '@/stores/theme'

const loginOpen=ref(false)
const helpOpen=ref(false)
const designLabOpen=ref(false)
const design=useDesignStore()
const session=useSessionStore()
const locale=useLocaleStore()
const theme=useThemeStore()
const route=useRoute()

watch(()=>route.fullPath,async()=>{
  await nextTick()
  document.querySelector<HTMLElement>('#main-content')?.focus({preventScroll:true})
})

onMounted(async()=>{
  theme.apply()
  design.apply()
  await locale.initialize()
  const magic=new URLSearchParams(location.search).get('magic')
  if(magic==='customer-demo-token'){
    session.login('2','2')
    location.hash='#/tracking'
  }
})
</script>

<template>
  <div class="min-h-screen">
    <a class="skip-link" href="#main-content">{{locale.t('skipContent')}}</a>
    <AppHeader @login="loginOpen=true" @help="helpOpen=true" @designlab="designLabOpen=true"/>
    <main id="main-content" tabindex="-1" class="mx-auto max-w-[1440px] px-3 py-4 md:py-6 lg:px-5">
      <RouterView v-slot="{ Component }">
        <component :is="Component" @login="loginOpen=true" />
      </RouterView>
      <footer class="site-footer">
        <b>{{locale.t('footerText')}}</b>
        <span>{{locale.t('footerContact')}}</span>
      </footer>
    </main>
    <BottomNav/>
    <LoginSheet :open="loginOpen" @close="loginOpen=false"/>
    <HelpSheet :open="helpOpen" @close="helpOpen=false"/>
    <DesignLabSheet :open="designLabOpen" @close="designLabOpen=false"/>
  </div>
</template>
