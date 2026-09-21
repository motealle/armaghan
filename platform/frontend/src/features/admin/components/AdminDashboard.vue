<script setup lang="ts">
import { computed, ref } from 'vue'
import {
  BellRing, Boxes, Check, FilePenLine, Languages, LayoutDashboard, MessageCircleMore, MoreVertical,
  Plus, Trash2, UserPlus, UserRoundCog, UsersRound,
} from '@lucide/vue'
import CustomerDetailSheet from '@/features/admin/components/CustomerDetailSheet.vue'
import TranslationManager from '@/features/admin/components/TranslationManager.vue'
import HomeContentEditor from '@/features/admin/components/HomeContentEditor.vue'
import AdminProductsPanel from '@/features/admin/components/AdminProductsPanel.vue'
import { placeholderSets, productPlaceholder } from '@/data/productPlaceholders'
import { useCatalogStore } from '@/stores/catalog'
import { useCustomersStore } from '@/stores/customers'
import { useDesignStore } from '@/stores/design'
import { useLocaleStore } from '@/stores/locale'
import { useSessionStore } from '@/stores/session'

type AdminTab='overview'|'customers'|'products'|'content'|'languages'
const catalog=useCatalogStore()
const customers=useCustomersStore()
const session=useSessionStore()
const design=useDesignStore()
const locale=useLocaleStore()

const activeTab=ref<AdminTab>('overview')
const selectedCustomers=ref<number[]>([])
const customerDetailId=ref<number|null>(null)
const customerDetailOpen=ref(false)
const openMenuId=ref<number|null>(null)
const note=ref('')
const customerFormOpen=ref(false)
const customerName=ref('')
const customerEmail=ref('')
const customerWhatsapp=ref('')
const customerFormError=ref('')

const tabs=computed(()=>[
  {id:'overview' as const,label:locale.t('adminOverview'),icon:LayoutDashboard},
  {id:'customers' as const,label:locale.t('adminCustomers'),icon:UsersRound},
  {id:'products' as const,label:locale.t('adminProducts'),icon:Boxes},
  {id:'content' as const,label:locale.t('brandIntro'),icon:FilePenLine},
  {id:'languages' as const,label:locale.t('adminLanguages'),icon:Languages},
])
const allCustomersSelected=computed(()=>customers.items.length>0&&selectedCustomers.value.length===customers.items.length)
const activeOrders=computed(()=>customers.items.filter(item=>item.activeOrder!=='بدون سفارش فعال').length)

function toggleAllCustomers(){selectedCustomers.value=allCustomersSelected.value?[]:customers.items.map(item=>item.id)}
function bulkDeleteCustomers(){
  if(!selectedCustomers.value.length||!window.confirm(locale.t('confirmDelete')))return
  customers.removeMany(selectedCustomers.value);selectedCustomers.value=[]
}
function addCustomer(){
  customerFormError.value=''
  const name=customerName.value.trim()
  const email=customerEmail.value.trim()
  const whatsapp=customerWhatsapp.value.trim()
  if(!name)return
  if(!email&&!whatsapp){customerFormError.value=locale.t('contactIdentifierRequired');return}
  const id=customers.add({name,email,whatsapp})
  customerName.value='';customerEmail.value='';customerWhatsapp.value='';customerFormOpen.value=false
  customerDetailId.value=id
  customerDetailOpen.value=true
}
function manageCustomer(id:number){customerDetailId.value=id;customerDetailOpen.value=true;openMenuId.value=null}
function impersonate(id:number){session.impersonate(id);openMenuId.value=null}
function contactLead(id:string){
  const lead=customers.wishlistLeads.find(item=>item.id===id)
  if(!lead)return
  if(lead.kind==='guest'){
    customers.queueGuestMessage(id,locale.t('leadMessageText'))
    note.value=locale.t('guestMessage')
    return
  }
  const customer=customers.items.find(item=>item.id===lead.customerId)
  const digits=customer?.whatsapp.replace(/\D/g,'')??''
  if(digits.length>=8){
    window.open(`https://wa.me/${digits}?text=${encodeURIComponent(locale.t('leadMessageText'))}`,'_blank','noopener')
  }
  note.value=locale.t('contactCustomer')
}
function inviteLead(id:string){
  customers.queueGuestMessage(id,locale.t('inviteMessageText'))
  note.value=locale.t('inviteAccount')
}
</script>

<template>
  <div class="space-y-5">
    <nav class="admin-surface rounded-2xl p-2" :aria-label="locale.t('adminOverview')">
      <div class="admin-tabs">
        <button v-for="tab in tabs" :key="tab.id" class="admin-tab" :class="{active:activeTab===tab.id}" @click="activeTab=tab.id">
          <component :is="tab.icon" :size="16" class="me-1 inline"/>{{tab.label}}
        </button>
      </div>
    </nav>

    <template v-if="activeTab==='overview'">
      <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <article class="admin-stat"><UsersRound :size="20"/><strong>{{customers.items.length}}</strong><span>{{locale.t('adminCustomers')}}</span></article>
        <article class="admin-stat"><Boxes :size="20"/><strong>{{catalog.items.length}}</strong><span>{{locale.t('adminProducts')}}</span></article>
        <article class="admin-stat"><LayoutDashboard :size="20"/><strong>{{activeOrders}}</strong><span>{{locale.t('activeOrderLabel')}}</span></article>
        <article class="admin-stat alert"><BellRing :size="20"/><strong>{{customers.changedWishlistCount}}</strong><span>{{locale.t('wishlistLeads')}}</span></article>
      </section>

      <section class="admin-surface rounded-2xl p-4">
        <div class="mb-3"><h2 class="text-lg font-black">{{locale.t('wishlistLeads')}}</h2><p class="mt-1 text-xs text-[var(--c-muted)]">{{locale.t('wishlistLeadsHelp')}}</p></div>
        <div class="grid gap-2 md:grid-cols-2">
          <article v-for="lead in customers.wishlistLeads" :key="lead.id" class="lead-card">
            <div><b class="block text-sm">{{lead.kind==='guest'?locale.t('anonymousVisitor'):lead.label}}</b><span class="text-xs text-[var(--c-muted)]">{{lead.favoritesCount}} · {{locale.t('favorites')}}</span></div>
            <div class="ms-auto flex gap-1">
              <button class="mini-action" @click="contactLead(lead.id)"><MessageCircleMore :size="15"/>{{lead.kind==='guest'?locale.t('guestMessage'):locale.t('contactCustomer')}}</button>
              <button v-if="lead.kind==='guest'" class="mini-action" @click="inviteLead(lead.id)"><UserPlus :size="15"/>{{locale.t('inviteAccount')}}</button>
            </div>
          </article>
        </div>
      </section>

      <section class="admin-surface rounded-2xl p-4">
        <div class="mb-3"><h2 class="text-lg font-black">{{locale.t('adminPlaceholderSet')}}</h2><p class="mt-1 text-xs text-[var(--c-muted)]">{{locale.t('adminPlaceholderSetHelp')}}</p></div>
        <div class="grid gap-3 md:grid-cols-3" role="radiogroup" :aria-label="locale.t('adminPlaceholderSet')">
          <label
            v-for="set in placeholderSets"
            :key="set.id"
            class="placeholder-set-card"
            :class="{active:design.placeholderSet===set.id}"
          >
            <input v-model="design.placeholderSet" class="sr-only" type="radio" name="placeholder-set" :value="set.id">
            <img :src="productPlaceholder(set.id,'11',design.placeholderOrientation)" :data-landscape-preview="set.preview" alt="" loading="lazy" decoding="async">
            <span class="placeholder-set-copy"><b>{{locale.t(set.labelKey)}}</b><small>{{locale.t(set.descriptionKey)}}</small></span>
            <span v-if="design.placeholderSet===set.id" class="placeholder-set-check" aria-hidden="true"><Check :size="15"/></span>
          </label>
        </div>
        <fieldset class="placeholder-orientation mt-4">
          <legend class="text-xs font-black text-[var(--c-text)]">{{locale.t('placeholderOrientation')}}</legend>
          <p class="mt-1 text-[11px] leading-5 text-[var(--c-muted)]">{{locale.t('placeholderOrientationHelp')}}</p>
          <div class="mt-2 grid grid-cols-3 gap-2" role="radiogroup" :aria-label="locale.t('placeholderOrientation')">
            <label class="placeholder-orientation-option" :class="{active:design.placeholderOrientation==='portrait'}">
              <input v-model="design.placeholderOrientation" class="sr-only" type="radio" name="placeholder-orientation" value="portrait">
              <b>{{locale.t('placeholderPortrait')}}</b><small>{{locale.t('placeholderPortraitHelp')}}</small>
            </label>
            <label class="placeholder-orientation-option" :class="{active:design.placeholderOrientation==='landscape'}">
              <input v-model="design.placeholderOrientation" class="sr-only" type="radio" name="placeholder-orientation" value="landscape">
              <b>{{locale.t('placeholderLandscape')}}</b><small>{{locale.t('placeholderLandscapeHelp')}}</small>
            </label>
            <label class="placeholder-orientation-option" :class="{active:design.placeholderOrientation==='auto'}">
              <input v-model="design.placeholderOrientation" class="sr-only" type="radio" name="placeholder-orientation" value="auto">
              <b>{{locale.t('placeholderAuto')}}</b><small>{{locale.t('placeholderAutoHelp')}}</small>
            </label>
          </div>
        </fieldset>
        <p class="mt-3 text-[11px] font-bold text-[var(--c-secondary)]">{{locale.t('placeholderAppliedImmediately')}}</p>
      </section>

      <div class="grid gap-4 xl:grid-cols-2">
        <section class="admin-surface rounded-2xl p-4">
          <div class="mb-3 flex items-center justify-between"><h2 class="font-black">{{locale.t('adminCustomers')}}</h2><button class="mini-action" @click="activeTab='customers'">{{locale.t('manageCustomer')}}</button></div>
          <div class="space-y-2"><div v-for="customer in customers.items.slice(0,4)" :key="customer.id" class="overview-row"><span>{{customer.flag}}</span><b>{{customer.name}}</b><small>{{locale.orderStatus(customer.activeOrder)}}</small></div></div>
        </section>
        <section class="admin-surface rounded-2xl p-4">
          <div class="mb-3 flex items-center justify-between"><h2 class="font-black">{{locale.t('adminProducts')}}</h2><button class="mini-action" @click="activeTab='products'">{{locale.t('adminProducts')}}</button></div>
          <div class="space-y-2"><div v-for="product in catalog.items.slice(0,4)" :key="product.id" class="overview-row"><b>{{locale.productName(product.code,product.name,product.names)}}</b><small>{{product.code}}</small></div></div>
        </section>
      </div>
    </template>

    <section v-else-if="activeTab==='customers'" class="space-y-3">
      <div class="flex flex-wrap items-center gap-2">
        <div><h2 class="text-xl font-black">{{locale.t('adminCustomers')}}</h2><p class="text-xs text-[var(--c-muted)]">{{locale.t('customer360')}}</p></div>
        <button class="ms-auto mini-action bg-[var(--c-primary)] text-white" @click="customerFormOpen=!customerFormOpen"><Plus :size="16"/>{{locale.t('addCustomer')}}</button>
      </div>

      <form v-if="customerFormOpen" class="admin-surface grid gap-2 rounded-2xl p-3 md:grid-cols-4" @submit.prevent="addCustomer">
        <label class="form-field">{{locale.t('fullName')}}<input v-model="customerName" required></label>
        <label class="form-field">{{locale.t('email')}}<input v-model="customerEmail" type="email" autocomplete="email"></label>
        <label class="form-field">WhatsApp / {{locale.t('loginIdentifier')}}<input v-model="customerWhatsapp" dir="ltr" inputmode="tel" autocomplete="tel"></label>
        <button class="mt-auto min-h-11 rounded-xl bg-[var(--c-primary)] px-4 text-sm font-bold text-white"><UserPlus :size="16" class="me-1 inline"/>{{locale.t('add')}}</button>
        <p v-if="customerFormError" class="auth-error md:col-span-4">{{customerFormError}}</p>
      </form>

      <div v-if="selectedCustomers.length" class="batch-bar">
        <b>{{selectedCustomers.length}} {{locale.t('selectedCount')}}</b>
        <button class="ms-auto mini-action text-rose-700" @click="bulkDeleteCustomers"><Trash2 :size="15"/>{{locale.t('bulkDelete')}}</button>
      </div>

      <div class="data-table-shell">
        <table class="data-table">
          <thead><tr>
            <th><input type="checkbox" :checked="allCustomersSelected" :aria-label="locale.t('selectAll')" @change="toggleAllCustomers"></th>
            <th>{{locale.t('customerLabel')}}</th><th>{{locale.t('customerPriority')}}</th><th>WhatsApp</th><th>{{locale.t('email')}}</th><th>{{locale.t('currentOrder')}}</th><th>{{locale.t('actions')}}</th>
          </tr></thead>
          <tbody>
            <tr v-for="customer in customers.items" :key="customer.id">
              <td><input v-model="selectedCustomers" type="checkbox" :value="customer.id"></td>
              <td><button class="text-start font-bold" @click="manageCustomer(customer.id)">{{customer.flag}} {{customer.name}}</button></td>
              <td><span class="inline-flex items-center gap-1 text-amber-500">★ <b class="text-[var(--c-text)]">{{customer.priorityStars??0}}</b></span></td>
              <td dir="ltr">{{customer.whatsapp}}</td><td dir="ltr">{{customer.email}}</td><td>{{locale.orderStatus(customer.activeOrder)}}</td>
              <td><div class="relative flex items-center gap-1">
                <button class="row-overflow" :disabled="selectedCustomers.length>0" :aria-label="locale.t('impersonate')" @click="impersonate(customer.id)"><UserRoundCog :size="17"/></button>
                <button class="row-overflow" :disabled="selectedCustomers.length>0" :aria-label="locale.t('moreActions')" @click="openMenuId=openMenuId===customer.id?null:customer.id"><MoreVertical :size="17"/></button>
                <div v-if="openMenuId===customer.id&&selectedCustomers.length===0" class="row-menu">
                  <button @click="manageCustomer(customer.id)">{{locale.t('manageCustomer')}}</button>
                  <button class="text-rose-700" @click="customers.remove(customer.id);openMenuId=null"><Trash2 :size="14"/>{{locale.t('delete')}}</button>
                </div>
              </div></td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <AdminProductsPanel v-else-if="activeTab==='products'"/>

    <HomeContentEditor v-else-if="activeTab==='content'"/>

    <TranslationManager v-else/>

    <p v-if="note" class="text-xs font-bold text-[var(--c-secondary)]">{{note}}</p>
    <CustomerDetailSheet :open="customerDetailOpen" :customer-id="customerDetailId" @close="customerDetailOpen=false"/>
  </div>
</template>
