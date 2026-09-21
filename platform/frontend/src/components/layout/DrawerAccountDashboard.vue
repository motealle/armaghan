<script setup lang="ts">
import { computed } from 'vue'
import { CheckCircle2, ClipboardClock, Heart, PackageCheck, Sparkles, UsersRound } from '@lucide/vue'
import { useCatalogStore } from '@/stores/catalog'
import { useCustomersStore } from '@/stores/customers'
import { useFavoritesStore } from '@/stores/favorites'
import { useLocaleStore } from '@/stores/locale'
import { useSessionStore } from '@/stores/session'

const session=useSessionStore()
const favorites=useFavoritesStore()
const catalog=useCatalogStore()
const customers=useCustomersStore()
const locale=useLocaleStore()

const customer=computed(()=>{
  if(session.impersonatedCustomerId)return customers.items.find(item=>item.id===session.impersonatedCustomerId)??customers.items[0]??null
  if(session.isCustomer)return customers.items.find(item=>item.id===(session.currentCustomerId??1))??customers.items[0]??null
  return null
})
const activeOrders=computed(()=>customers.items.filter(item=>item.activeOrder!=='بدون سفارش فعال').length)
const nextCustomerAction=computed(()=>{
  const state=customer.value?.activeOrder??''
  if(state.includes('پیش‌پرداخت'))return locale.t('approvePrepayment')
  if(state.includes('ارسال'))return locale.t('tracking')
  if(state.includes('تولید'))return locale.t('timeline')
  return locale.t('production')
})
</script>

<template>
  <section class="drawer-dashboard" :aria-label="locale.t('drawerSummary')">
    <template v-if="session.isAdmin&&!session.impersonatedCustomerId">
      <div class="drawer-dashboard-head">
        <div><span class="drawer-dashboard-eyebrow">{{locale.t('adminOverview')}}</span><b>{{locale.t('drawerOperational')}}</b></div>
        <UsersRound :size="20"/>
      </div>
      <div class="drawer-metric-grid">
        <div class="drawer-metric"><strong>{{customers.items.length}}</strong><span>{{locale.t('customerLabel')}}</span></div>
        <div class="drawer-metric"><strong>{{activeOrders}}</strong><span>{{locale.t('activeOrderLabel')}}</span></div>
        <div class="drawer-metric"><strong>{{catalog.items.length}}</strong><span>{{locale.t('productCount')}}</span></div>
        <div class="drawer-metric drawer-metric-wide"><ClipboardClock :size="16"/><span>{{locale.t('nextAction')}}: {{locale.t('reviewActiveOrders')}}</span></div>
      </div>
    </template>

    <template v-else-if="session.isAuthenticated">
      <div class="drawer-dashboard-head">
        <div><span class="drawer-dashboard-eyebrow">{{locale.t('drawerSummary')}}</span><b>{{customer?.name??locale.t('customerLabel')}}</b></div>
        <PackageCheck :size="20"/>
      </div>
      <div class="drawer-metric-grid">
        <div class="drawer-metric"><strong>{{customer?.orderCount??0}}</strong><span>{{locale.t('ordersLabel')}}</span></div>
        <div class="drawer-metric"><strong>{{favorites.ids.length}}</strong><span>{{locale.t('favorites')}}</span></div>
        <div class="drawer-metric drawer-metric-wide"><CheckCircle2 :size="16"/><span>{{customer?.timelineStage?locale.orderStatus(customer.timelineStage):locale.t('noActiveStage')}}</span></div>
        <div class="drawer-metric drawer-metric-wide"><Sparkles :size="16"/><span>{{locale.t('nextAction')}}: {{nextCustomerAction}}</span></div>
      </div>
    </template>

    <template v-else>
      <div class="drawer-guest-card">
        <div class="grid h-10 w-10 place-items-center rounded-xl bg-[color-mix(in_srgb,var(--c-primary)_10%,var(--c-surface))] text-[var(--c-primary)]"><Heart :size="19"/></div>
        <div><b class="block text-sm text-[var(--c-text)]">{{locale.t('guestAccountTitle')}}</b><p class="mt-1 text-[11px] leading-5 text-[var(--c-muted)]">{{locale.t('guestAccountText')}}</p></div>
      </div>
    </template>
  </section>
</template>
