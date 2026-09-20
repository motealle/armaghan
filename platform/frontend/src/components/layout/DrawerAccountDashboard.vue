<script setup lang="ts">
import { computed } from 'vue'
import { CheckCircle2, ClipboardClock, Heart, PackageCheck, Sparkles, UsersRound } from '@lucide/vue'
import { demoCustomers } from '@/data/catalog'
import { useCatalogStore } from '@/stores/catalog'
import { useFavoritesStore } from '@/stores/favorites'
import { useSessionStore } from '@/stores/session'

const session=useSessionStore()
const favorites=useFavoritesStore()
const catalog=useCatalogStore()

const customer=computed(()=>{
  if(session.impersonatedCustomerId)return demoCustomers.find(item=>item.id===session.impersonatedCustomerId) ?? demoCustomers[0]!
  if(session.isCustomer)return demoCustomers[0]!
  return null
})
const activeOrders=computed(()=>demoCustomers.filter(item=>item.activeOrder!=='بدون سفارش فعال').length)
const nextCustomerAction=computed(()=>{
  const state=customer.value?.activeOrder ?? ''
  if(state.includes('پیش‌پرداخت'))return 'تأیید پیش‌پرداخت'
  if(state.includes('آماده ارسال'))return 'هماهنگی ارسال'
  if(state.includes('مذاکره'))return 'ادامه مذاکره'
  if(state.includes('تولید'))return 'مشاهده تایملاین'
  return 'شروع استعلام جدید'
})
const stageLabel=computed(()=>{
  const state=customer.value?.activeOrder ?? ''
  if(!state||state==='بدون سفارش فعال')return 'بدون مرحله فعال'
  return state
})
</script>

<template>
  <section class="drawer-dashboard" aria-label="خلاصه حساب">
    <template v-if="session.isAdmin && !session.impersonatedCustomerId">
      <div class="drawer-dashboard-head">
        <div>
          <span class="drawer-dashboard-eyebrow">داشبورد مدیر</span>
          <b>وضعیت عملیاتی</b>
        </div>
        <UsersRound :size="20"/>
      </div>
      <div class="drawer-metric-grid">
        <div class="drawer-metric"><strong>{{demoCustomers.length}}</strong><span>مشتری</span></div>
        <div class="drawer-metric"><strong>{{activeOrders}}</strong><span>سفارش فعال</span></div>
        <div class="drawer-metric"><strong>{{catalog.items.length}}</strong><span>محصول</span></div>
        <div class="drawer-metric drawer-metric-wide"><ClipboardClock :size="16"/><span>اقدام بعدی: بررسی سفارش‌های فعال</span></div>
      </div>
    </template>

    <template v-else-if="session.isAuthenticated">
      <div class="drawer-dashboard-head">
        <div>
          <span class="drawer-dashboard-eyebrow">خلاصه حساب</span>
          <b>{{customer?.name ?? 'مشتری'}}</b>
        </div>
        <PackageCheck :size="20"/>
      </div>
      <div class="drawer-metric-grid">
        <div class="drawer-metric"><strong>{{customer?.orderCount ?? 0}}</strong><span>سفارش</span></div>
        <div class="drawer-metric"><strong>{{favorites.ids.length}}</strong><span>مطلوب</span></div>
        <div class="drawer-metric drawer-metric-wide"><CheckCircle2 :size="16"/><span>{{stageLabel}}</span></div>
        <div class="drawer-metric drawer-metric-wide"><Sparkles :size="16"/><span>اقدام بعدی: {{nextCustomerAction}}</span></div>
      </div>
    </template>

    <template v-else>
      <div class="drawer-guest-card">
        <div class="grid h-10 w-10 place-items-center rounded-xl bg-[color-mix(in_srgb,var(--c-primary)_10%,var(--c-surface))] text-[var(--c-primary)]">
          <Heart :size="19"/>
        </div>
        <div>
          <b class="block text-sm text-[var(--c-text)]">حساب شما، خلاصه و کاربردی</b>
          <p class="mt-1 text-[11px] leading-5 text-[var(--c-muted)]">پس از ورود، وضعیت سفارش، مرحله فعلی، اقدام بعدی و تعداد مطلوب‌ها همین‌جا دیده می‌شود.</p>
        </div>
      </div>
    </template>
  </section>
</template>
