<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { ChevronLeft, ChevronRight, PackagePlus, Pencil, Search, RefreshCw } from '@lucide/vue'
import ProductEditorPanel from './ProductEditorPanel.vue'
import { useLocaleStore } from '@/stores/locale'
import { useCatalogStore } from '@/stores/catalog'
import { useAdminStore } from '../store'
import { CustomerSessionApiError } from '@/features/auth/services/customerSessionApi'
import { fetchAdminProducts, fetchProductTaxonomy, type AdminProduct, type AdminSubcategory, type ProductPage } from '../services/adminApi'
const locale=useLocaleStore(),admin=useAdminStore(),catalog=useCatalogStore()
const result=ref<ProductPage>({products:[],page:1,last_page:1,total:0})
const taxonomy=ref<AdminSubcategory[]>([])
const query=ref(''),subcategory=ref(''),pageSize=ref(25),error=ref(''),note=ref('')
const loading=ref(false),editorOpen=ref(false),selected=ref<AdminProduct|null>(null)
async function load(page=1){
  if(loading.value)return
  loading.value=true;error.value=''
  try{
    const [rows,groups]=await Promise.all([fetchAdminProducts(page,query.value.trim(),subcategory.value,pageSize.value),fetchProductTaxonomy()])
    result.value=rows;taxonomy.value=groups.subcategories
  }catch(e){
    result.value={products:[],page:1,last_page:1,total:0};taxonomy.value=[]
    if(e instanceof CustomerSessionApiError&&[401,403].includes(e.status))admin.clear()
    error.value=locale.t('adminRequestFailed')
  }finally{loading.value=false}
}
function edit(product:AdminProduct|null){selected.value=product;editorOpen.value=true;note.value=''}
async function saved(){
  editorOpen.value=false;selected.value=null;note.value=locale.t('adminSaved')
  await load(result.value.page)
  // Existing public adapter handles backend-owned rows and removes archived local copies.
  await catalog.hydrateFromBackend()
}
onMounted(()=>load())
</script>
<template>
  <section class="space-y-3" :aria-busy="loading">
    <div class="flex flex-wrap items-center gap-2">
      <div><h2 class="text-xl font-black text-[var(--c-text)]">{{locale.t('adminProducts')}}</h2><p class="text-xs text-[var(--c-muted)]">{{result.total}} {{locale.t('productCount')}}</p></div>
      <button class="ms-auto inline-flex min-h-11 items-center gap-2 rounded-xl bg-[var(--c-primary)] px-4 text-sm font-black text-white" :disabled="loading||!taxonomy.length" @click="edit(null)"><PackagePlus :size="17"/>{{locale.t('addProduct')}}</button>
    </div>
    <form class="admin-surface grid gap-2 rounded-2xl p-3 lg:grid-cols-[1fr_14rem_8rem_auto]" @submit.prevent="load(1)">
      <label class="form-field">{{locale.t('productSearch')}}<span class="relative block"><Search :size="16" class="pointer-events-none absolute start-3 top-1/2 -translate-y-1/2 text-[var(--c-muted)]"/><input v-model="query" maxlength="100" class="ps-9" :disabled="loading"></span></label>
      <label class="form-field">{{locale.t('subcategoryLabel')}}<select v-model="subcategory" :disabled="loading"><option value="">{{locale.t('allSubs')}}</option><option v-for="sub in taxonomy" :key="sub.id" :value="String(sub.id)">{{sub.code}} · {{locale.subcategoryName(sub.code,sub.name)}}</option></select></label>
      <label class="form-field">{{locale.t('rowsPerPage')}}<select v-model.number="pageSize" :disabled="loading"><option :value="25">25</option><option :value="50">50</option><option :value="100">100</option></select></label>
      <button class="mini-action mt-auto" :disabled="loading"><RefreshCw :size="16"/>{{locale.t('adminReload')}}</button>
    </form>
    <p v-if="loading" role="status">{{locale.t('adminLoading')}}</p><p v-if="error" class="auth-error" role="alert">{{error}}</p><p v-if="note" role="status" class="text-xs text-[var(--c-secondary)]">{{note}}</p>
    <div class="data-table-shell"><table class="data-table">
      <thead><tr><th>{{locale.t('productsTitle')}}</th><th>{{locale.t('codeLabel')}}</th><th>{{locale.t('categoryLabel')}}</th><th>{{locale.t('subcategoryLabel')}}</th><th>{{locale.t('statusLabel')}}</th><th>{{locale.t('actions')}}</th></tr></thead>
      <tbody><tr v-for="product in result.products" :key="product.id">
        <td><button class="text-start font-bold" :disabled="loading" @click="edit(product)">{{locale.productName(product.code,product.name_fa,{fa:product.name_fa,ar:product.name_ar??undefined,en:product.name_en??undefined,ku:product.name_ku??undefined})}}</button></td>
        <td><code class="text-[var(--c-primary)]">{{product.code}}</code></td>
        <td>{{locale.categoryName(product.category_code,'')}}</td><td>{{locale.subcategoryName(product.subcategory_code,'')}}</td>
        <td>{{locale.t(!product.active?'adminInactive':product.availability==='available'?'available':product.availability==='unavailable'?'unavailable':'madeToOrder')}}</td>
        <td><button class="mini-action" :disabled="loading" @click="edit(product)"><Pencil :size="15"/>{{locale.t('editProduct')}} · {{product.media.length}} {{locale.t('image')}}</button></td>
      </tr><tr v-if="!loading&&!result.products.length&&!error"><td colspan="6" class="py-8 text-center text-[var(--c-muted)]">{{locale.t('adminNoProducts')}}</td></tr></tbody>
    </table></div>
    <div class="pagination-bar"><span class="text-xs text-[var(--c-muted)]">{{result.total}}</span><div class="ms-auto flex items-center gap-1">
      <button class="pagination-button" :disabled="loading||result.page<=1" :aria-label="locale.t('previous')" @click="load(result.page-1)"><ChevronRight :size="17"/></button>
      <span class="min-w-20 text-center text-xs font-bold">{{locale.t('page')}} {{result.page}} / {{result.last_page}}</span>
      <button class="pagination-button" :disabled="loading||result.page>=result.last_page" :aria-label="locale.t('next')" @click="load(result.page+1)"><ChevronLeft :size="17"/></button>
    </div></div>
    <ProductEditorPanel live :open="editorOpen" :product-id="selected?.id??null" :server-product="selected" :taxonomy="taxonomy" @close="editorOpen=false" @saved="saved"/>
  </section>
</template>
