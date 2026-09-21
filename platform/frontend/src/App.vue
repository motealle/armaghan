<script setup lang="ts">
import { nextTick, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import AppHeader from '@/components/layout/AppHeader.vue'
import BottomNav from '@/components/layout/BottomNav.vue'
import HelpSheet from '@/components/layout/HelpSheet.vue'
import LoginSheet from '@/features/auth/components/LoginSheet.vue'
import { useDesignStore } from '@/stores/design'
import { useSessionStore } from '@/stores/session'
import { useCustomersStore } from '@/stores/customers'
import { useLocaleStore } from '@/stores/locale'
import { useThemeStore } from '@/stores/theme'

const loginOpen=ref(false)
const helpOpen=ref(false)
const design=useDesignStore()
const session=useSessionStore()
const customers=useCustomersStore()
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
  const params=new URLSearchParams(location.search)
  const magic=params.get('magic')
  const customerAccess=params.get('customerAccess')
  if(magic&&session.consumeMagicLink(magic)){
    const customer=customers.items.find(item=>item.email&&item.email.toLowerCase()===session.currentEmail.toLowerCase())
    if(customer)session.loginCustomerRecord(customer.id,customer.email)
    location.hash='#/tracking'
  }
  if(customerAccess){
    const customer=customers.resolveAccessToken(customerAccess)
    if(customer){session.loginCustomerRecord(customer.id,customer.email);location.hash='#/tracking'}
  }
})
</script>

<template>
  <div class="min-h-screen">
    <a class="skip-link" href="#main-content">{{locale.t('skipContent')}}</a>
    <AppHeader @login="loginOpen=true" @help="helpOpen=true"/>
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
  </div>
</template>
