<script setup lang="ts">
import { computed, ref } from 'vue'
import {
  BellRing, Boxes, ImagePlus, Languages, LayoutDashboard, MessageCircleMore, MoreVertical,
  PackagePlus, Plus, Trash2, UserPlus, UserRoundCog, UsersRound,
} from '@lucide/vue'
import { categories } from '@/data/catalog'
import { compressImage } from '@/features/admin/services/imageCompression'
import CustomerDetailSheet from '@/features/admin/components/CustomerDetailSheet.vue'
import TranslationManager from '@/features/admin/components/TranslationManager.vue'
import { useCatalogStore } from '@/stores/catalog'
import { useCustomersStore } from '@/stores/customers'
import { useDesignStore } from '@/stores/design'
import { useLocaleStore } from '@/stores/locale'
import { useSessionStore } from '@/stores/session'
import type { Product } from '@/types/domain'

type AdminTab='overview'|'customers'|'products'|'languages'
const catalog=useCatalogStore()
const customers=useCustomersStore()
const session=useSessionStore()
const design=useDesignStore()
const locale=useLocaleStore()

const activeTab=ref<AdminTab>('overview')
const selectedCustomers=ref<number[]>([])
const selectedProducts=ref<number[]>([])
const customerDetailId=ref<number|null>(null)
const customerDetailOpen=ref(false)
const openMenuId=ref<number|null>(null)
const note=ref('')
const customerFormOpen=ref(false)
const customerName=ref('')
const customerEmail=ref('')
const customerWhatsapp=ref('')
const productName=ref('')
const productCode=ref('')
const productSubcategory=ref<Product['subcategoryCode']>('11')

const tabs=computed(()=>[
  {id:'overview' as const,label:locale.t('adminOverview'),icon:LayoutDashboard},
  {id:'customers' as const,label:locale.t('adminCustomers'),icon:UsersRound},
  {id:'products' as const,label:locale.t('adminProducts'),icon:Boxes},
  {id:'languages' as const,label:locale.t('adminLanguages'),icon:Languages},
])
const allCustomersSelected=computed(()=>customers.items.length>0&&selectedCustomers.value.length===customers.items.length)
const allProductsSelected=computed(()=>catalog.items.length>0&&selectedProducts.value.length===catalog.items.length)
const activeOrders=computed(()=>customers.items.filter(item=>item.activeOrder!=='بدون سفارش فعال').length)

function toggleAllCustomers(){selectedCustomers.value=allCustomersSelected.value?[]:customers.items.map(item=>item.id)}
function toggleAllProducts(){selectedProducts.value=allProductsSelected.value?[]:catalog.items.map(item=>item.id)}
function bulkDeleteCustomers(){
  if(!selectedCustomers.value.length||!window.confirm(locale.t('confirmDelete')))return
  customers.removeMany(selectedCustomers.value);selectedCustomers.value=[]
}
function bulkDeleteProducts(){
  if(!selectedProducts.value.length||!window.confirm(locale.t('confirmDelete')))return
  catalog.removeMany(selectedProducts.value);selectedProducts.value=[]
}
function addCustomer(){
  if(!customerName.value.trim()||!customerEmail.value.trim())return
  customers.add({name:customerName.value.trim(),email:customerEmail.value.trim(),whatsapp:customerWhatsapp.value.trim()})
  customerName.value='';customerEmail.value='';customerWhatsapp.value='';customerFormOpen.value=false
}
function addProduct(){
  if(!productName.value.trim()||!productCode.value.trim())return
  const sub=categories.flatMap(category=>category.subcategories.map(item=>({category,item}))).find(row=>row.item.code===productSubcategory.value)
  if(!sub)return
  const nextId=Math.max(0,...catalog.items.map(item=>item.id))+1
  const product:Product={
    id:nextId,code:productCode.value.trim(),name:productName.value.trim(),
    categoryCode:sub.category.code,subcategoryCode:sub.item.code,
    categoryName:sub.category.name,subcategoryName:sub.item.name,availability:'available',
    specs:{locked:[],negotiable:['مشخصات قابل مذاکره']},
  }
  catalog.add(product);productName.value='';productCode.value=''
}
async function upload(id:number,event:Event){
  const file=(event.target as HTMLInputElement).files?.[0]
  if(!file)return
  try{catalog.updateImage(id,await compressImage(file));note.value=locale.t('save')}catch{}
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
        <div class="mb-3"><h2 class="text-lg font-black">{{locale.t('adminCardActions')}}</h2><p class="mt-1 text-xs text-[var(--c-muted)]">{{locale.t('adminCardActionsHelp')}}</p></div>
        <div class="grid gap-2 sm:grid-cols-2">
          <button class="admin-display-mode" :class="{active:design.cardActionMode==='compact'}" :aria-pressed="design.cardActionMode==='compact'" @click="design.cardActionMode='compact'"><b>{{locale.t('compactMode')}}</b><span>{{locale.t('compactModeHelp')}}</span></button>
          <button class="admin-display-mode" :class="{active:design.cardActionMode==='labeled'}" :aria-pressed="design.cardActionMode==='labeled'" @click="design.cardActionMode='labeled'"><b>{{locale.t('labeledMode')}}</b><span>{{locale.t('labeledModeHelp')}}</span></button>
        </div>
      </section>

      <div class="grid gap-4 xl:grid-cols-2">
        <section class="admin-surface rounded-2xl p-4">
          <div class="mb-3 flex items-center justify-between"><h2 class="font-black">{{locale.t('adminCustomers')}}</h2><button class="mini-action" @click="activeTab='customers'">{{locale.t('manageCustomer')}}</button></div>
          <div class="space-y-2"><div v-for="customer in customers.items.slice(0,4)" :key="customer.id" class="overview-row"><span>{{customer.flag}}</span><b>{{customer.name}}</b><small>{{locale.orderStatus(customer.activeOrder)}}</small></div></div>
        </section>
        <section class="admin-surface rounded-2xl p-4">
          <div class="mb-3 flex items-center justify-between"><h2 class="font-black">{{locale.t('adminProducts')}}</h2><button class="mini-action" @click="activeTab='products'">{{locale.t('adminProducts')}}</button></div>
          <div class="space-y-2"><div v-for="product in catalog.items.slice(0,4)" :key="product.id" class="overview-row"><b>{{locale.productName(product.code,product.name)}}</b><small>{{product.code}}</small></div></div>
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
        <label class="form-field">{{locale.t('email')}}<input v-model="customerEmail" type="email" required></label>
        <label class="form-field">WhatsApp<input v-model="customerWhatsapp" dir="ltr"></label>
        <button class="mt-auto min-h-11 rounded-xl bg-[var(--c-primary)] px-4 text-sm font-bold text-white"><UserPlus :size="16" class="me-1 inline"/>{{locale.t('add')}}</button>
      </form>

      <div v-if="selectedCustomers.length" class="batch-bar">
        <b>{{selectedCustomers.length}} {{locale.t('selectedCount')}}</b>
        <button class="ms-auto mini-action text-rose-700" @click="bulkDeleteCustomers"><Trash2 :size="15"/>{{locale.t('bulkDelete')}}</button>
      </div>

      <div class="data-table-shell">
        <table class="data-table">
          <thead><tr>
            <th><input type="checkbox" :checked="allCustomersSelected" :aria-label="locale.t('selectAll')" @change="toggleAllCustomers"></th>
            <th>{{locale.t('customerLabel')}}</th><th>WhatsApp</th><th>{{locale.t('email')}}</th><th>{{locale.t('currentOrder')}}</th><th>{{locale.t('actions')}}</th>
          </tr></thead>
          <tbody>
            <tr v-for="customer in customers.items" :key="customer.id">
              <td><input v-model="selectedCustomers" type="checkbox" :value="customer.id"></td>
              <td><button class="text-start font-bold" @click="manageCustomer(customer.id)">{{customer.flag}} {{customer.name}}</button></td>
              <td dir="ltr">{{customer.whatsapp}}</td><td dir="ltr">{{customer.email}}</td><td>{{customer.activeOrder}}</td>
              <td><div class="relative flex items-center gap-1">
                <button class="row-overflow" :aria-label="locale.t('impersonate')" @click="impersonate(customer.id)"><UserRoundCog :size="17"/></button>
                <button class="row-overflow" :aria-label="locale.t('moreActions')" @click="openMenuId=openMenuId===customer.id?null:customer.id"><MoreVertical :size="17"/></button>
                <div v-if="openMenuId===customer.id" class="row-menu">
                  <button @click="manageCustomer(customer.id)">{{locale.t('manageCustomer')}}</button>
                  <button class="text-rose-700" @click="customers.remove(customer.id);openMenuId=null"><Trash2 :size="14"/>{{locale.t('delete')}}</button>
                </div>
              </div></td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <section v-else-if="activeTab==='products'" class="space-y-3">
      <div><h2 class="text-xl font-black">{{locale.t('adminProducts')}}</h2><p class="text-xs text-[var(--c-muted)]">{{locale.t('adminCardActionsHelp')}}</p></div>
      <form class="admin-surface grid gap-2 rounded-2xl p-3 md:grid-cols-[1fr_10rem_12rem_auto]" @submit.prevent="addProduct">
        <label class="form-field">{{locale.t('productsTitle')}}<input v-model="productName" required></label>
        <label class="form-field">{{locale.t('codeLabel')}}<input v-model="productCode" required></label>
        <label class="form-field">{{locale.t('subcategoryLabel')}}<select v-model="productSubcategory"><option v-for="category in categories" :key="category.code" disabled>{{locale.categoryName(category.code,category.name)}}</option><template v-for="category in categories" :key="'s-'+category.code"><option v-for="sub in category.subcategories" :key="sub.code" :value="sub.code">{{sub.code}} · {{locale.subcategoryName(sub.code,sub.name)}}</option></template></select></label>
        <button class="mt-auto min-h-11 rounded-xl bg-[var(--c-primary)] px-4 text-sm font-bold text-white"><PackagePlus :size="16" class="me-1 inline"/>{{locale.t('add')}}</button>
      </form>

      <div v-if="selectedProducts.length" class="batch-bar"><b>{{selectedProducts.length}} {{locale.t('selectedCount')}}</b><button class="ms-auto mini-action text-rose-700" @click="bulkDeleteProducts"><Trash2 :size="15"/>{{locale.t('bulkDelete')}}</button></div>

      <div class="data-table-shell">
        <table class="data-table">
          <thead><tr><th><input type="checkbox" :checked="allProductsSelected" :aria-label="locale.t('selectAll')" @change="toggleAllProducts"></th><th>{{locale.t('productsTitle')}}</th><th>{{locale.t('codeLabel')}}</th><th>{{locale.t('subcategoryLabel')}}</th><th>{{locale.t('actions')}}</th></tr></thead>
          <tbody>
            <tr v-for="product in catalog.items" :key="product.id">
              <td><input v-model="selectedProducts" type="checkbox" :value="product.id"></td>
              <td class="font-bold">{{locale.productName(product.code,product.name)}}</td><td>{{product.code}}</td><td>{{locale.subcategoryName(product.subcategoryCode,product.subcategoryName)}}</td>
              <td><div class="flex gap-1"><label class="mini-action cursor-pointer"><ImagePlus :size="15"/><input class="hidden" type="file" accept="image/*" @change="upload(product.id,$event)"></label><button class="row-overflow text-rose-700" :aria-label="locale.t('delete')" @click="catalog.remove(product.id)"><Trash2 :size="16"/></button></div></td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <TranslationManager v-else/>

    <p v-if="note" class="text-xs font-bold text-[var(--c-secondary)]">{{note}}</p>
    <CustomerDetailSheet :open="customerDetailOpen" :customer-id="customerDetailId" @close="customerDetailOpen=false"/>
  </div>
</template>
