<script setup lang="ts">
import { computed, defineAsyncComponent, nextTick, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import AppHeader from '@/components/layout/AppHeader.vue'
import BottomNav from '@/components/layout/BottomNav.vue'
import HelpSheet from '@/components/layout/HelpSheet.vue'
const VisualEditor=defineAsyncComponent(()=>import('@/features/visual-editor/VisualEditor.vue'))
import VisualEditorQuickLauncher from '@/features/visual-editor/VisualEditorQuickLauncher.vue'
import VisualStyleRuntime from '@/features/visual-editor/VisualStyleRuntime.vue'
import SiteFooter from '@/components/layout/SiteFooter.vue'
import { useResolvedAppearance } from '@/composables/useResolvedAppearance'
import LoginSheet from '@/features/auth/components/LoginSheet.vue'
import HomeMediaQuickEditor from '@/features/home-media/HomeMediaQuickEditor.vue'
import { useDesignStore } from '@/stores/design'
import { useVisualStyleStore } from '@/features/visual-editor/store'
import { useAdminStore } from '@/features/admin/store'
import { useSessionStore } from '@/stores/session'
import { useCustomersStore } from '@/stores/customers'
import { useCatalogStore } from '@/stores/catalog'
import { useLocaleStore } from '@/stores/locale'
import { useThemeStore } from '@/stores/theme'

const pagePath=window.location.pathname+window.location.search
const loginOpen=ref(false)
const helpOpen=ref(false)
const design=useDesignStore()
const session=useSessionStore()
const admin=useAdminStore()
const visual=useVisualStyleStore()
watch(()=>!!admin.identity||session.isAdmin,(allowed)=>{if(!allowed)visual.setEnabled(false)},{immediate:true})
const customers=useCustomersStore()
const catalog=useCatalogStore()
const locale=useLocaleStore()
const theme=useThemeStore()
const route=useRoute()
watch(()=>route.path,(path)=>{if(path!=='/')visual.setEnabled(false)},{immediate:true})

const {profile,policy}=useResolvedAppearance()
const showFooter=computed(()=>policy.value.showFooter&&(profile.value!=='mobile'||route.path==='/'))

watch(()=>route.fullPath,async()=>{
  await nextTick()
  document.querySelector<HTMLElement>('#main-content')?.focus({preventScroll:true})
})

onMounted(async()=>{
  theme.apply()
  design.apply()
  await locale.initialize()
  void catalog.hydrateFromBackend()
  void admin.hydrate()
  const hasBackendCustomerSession=await session.hydrateFromBackend()
  const params=new URLSearchParams(location.search)
  const customerAccess=params.get('customerAccess')
  if(!hasBackendCustomerSession&&customerAccess){
    const customer=customers.resolveAccessToken(customerAccess)
    if(customer){session.loginCustomerRecord(customer.id,customer.email);location.hash='#/tracking'}
  }
})
</script>

<template>
  <div
    class="min-h-screen"
    :data-style-id="route.path==='/'?'home.page':undefined"
    :data-style-label="route.path==='/'?'صفحه خانه':undefined"
  >
    <VisualStyleRuntime/>
    <a class="skip-link" :href="pagePath+'#main-content'">{{locale.t('skipContent')}}</a>
    <AppHeader @login="loginOpen=true" @help="helpOpen=true"/>
    <nav v-if="admin.identity" class="mx-auto flex max-w-[1500px] flex-wrap items-center gap-3 px-4 py-3 text-sm" :aria-label="locale.t('adminRealSession')">
      <RouterLink class="mini-action" to="/admin">{{locale.t('adminRealSession')}}</RouterLink>
      <span>{{locale.t(admin.identity.is_owner?'adminOwnerRole':'adminAdminRole')}}</span>
    </nav>
    <main id="main-content" tabindex="-1" class="mx-auto max-w-[1500px] px-3 py-4 md:px-5 md:py-6 lg:px-8 lg:py-8 xl:px-10">
      <RouterView v-slot="{ Component }">
        <component :is="Component" @login="loginOpen=true" />
      </RouterView>
      <SiteFooter v-if="showFooter" @help="helpOpen=true"/>
    </main>
    <BottomNav/>
    <LoginSheet :open="loginOpen" @close="loginOpen=false"/>
    <HelpSheet :open="helpOpen" @close="helpOpen=false"/>
    <HomeMediaQuickEditor v-if="route.path==='/'&&admin.identity"/>
    <template v-if="route.path==='/'&&(admin.identity||session.isAdmin)&&!session.impersonatedCustomerId">
      <VisualEditorQuickLauncher/>
      <VisualEditor v-if="visual.enabled"/>
    </template>
  </div>
</template>
