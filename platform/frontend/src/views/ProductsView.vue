<script setup lang="ts">
import { computed, defineAsyncComponent, onMounted, ref, watch } from 'vue'
import { Check, ChevronLeft, Search, Pencil } from '@lucide/vue'
import { useRoute, useRouter } from 'vue-router'
import { readProductFilters, productFilterQuery } from '@/features/catalog/services/productFilters'
import { useCatalogStore } from '@/stores/catalog'
import { useLocaleStore } from '@/stores/locale'
import ProductGrid from '@/features/catalog/components/ProductGrid.vue'
const BackendProductEditor=defineAsyncComponent(()=>import('@/features/admin/components/BackendProductEditor.vue'))
const BackendProductsPanel=defineAsyncComponent(()=>import('@/features/admin/components/BackendProductsPanel.vue'))
import AdaptivePanel from '@/components/ui/AdaptivePanel.vue'
import { useAdminStore } from '@/features/admin/store'
import { resolveCatalogProduct, fetchProductTaxonomy, type AdminProduct, type AdminSubcategory, type ProductFields } from '@/features/admin/services/adminApi'
import type { Product } from '@/types/domain'
import { useResolvedAppearance } from '@/composables/useResolvedAppearance'

const route=useRoute()
const router=useRouter()
const browseReady=ref(false)
const catalog=useCatalogStore()
const locale=useLocaleStore()
const admin=useAdminStore()
const {policy}=useResolvedAppearance()
const adminEditorOpen=ref(false)
const managerOpen=ref(false)
const adminEditorBusy=ref(false)
const adminEditorError=ref('')
const editingCatalogId=ref<number|null>(null)
const editorMode=ref<'create'|'edit'|'materialize'>('create')
const adminProduct=ref<AdminProduct|null>(null)
const adminInitial=ref<ProductFields|null>(null)
const adminTaxonomy=ref<AdminSubcategory[]>([])
const initialFilters=readProductFilters(route.query)
const category=ref(initialFilters.category)
const subcategory=ref(initialFilters.subcategory)
const query=ref(initialFilters.query)
const availability=ref(initialFilters.availability)

watch(()=>route.query,(value)=>{
  const filters=readProductFilters(value)
  category.value=filters.category;subcategory.value=filters.subcategory
  availability.value=filters.availability;query.value=filters.query
})
watch([category,subcategory,availability,query],()=>{
  const filters={category:category.value,subcategory:subcategory.value,availability:availability.value,query:query.value}
  const next=productFilterQuery(filters)
  if(JSON.stringify(next)!==JSON.stringify(productFilterQuery(readProductFilters(route.query))))void router.replace({path:'/products',query:next})
})
onMounted(async()=>{try{await catalog.hydrateFromBackend()}finally{browseReady.value=true}})

const subs=computed(()=>category.value==='all'?[]:catalog.categories.find(c=>c.code===category.value)?.subcategories ?? [])
const filtered=computed(()=>catalog.items.filter(product=>{
  if(category.value!=='all'&&product.categoryCode!==category.value)return false
  if(subcategory.value!=='all'&&product.subcategoryCode!==subcategory.value)return false
  if(availability.value==='available'&&product.availability!=='available')return false
  if(availability.value==='made_to_order'&&product.availability==='available')return false
  const needle=query.value.trim().toLowerCase()
  return !needle||(`${locale.productName(product.code,product.name)} ${product.code} ${locale.subcategoryName(product.subcategoryCode,product.subcategoryName)}`).toLowerCase().includes(needle)
}))
const activeCount=computed(()=>Number(category.value!=='all')+Number(subcategory.value!=='all')+Number(availability.value!=='all')+Number(Boolean(query.value.trim())))
function resetFilters(){category.value='all';subcategory.value='all';availability.value='all';query.value=''}
function selectCategory(code:string){
  category.value=category.value===code?'all':code
  subcategory.value='all'
}

function seedFields(product:Product):ProductFields|null{
  const group=adminTaxonomy.value.find(item=>item.code===product.subcategoryCode)
  if(!group)return null
  const values=product.specificationValues??[]
  return {
    subcategory_id:group.id,
    code:product.code,
    name_fa:product.names?.fa||product.name,
    name_ar:product.names?.ar??null,
    name_en:product.names?.en??null,
    name_ku:product.names?.ku??null,
    availability:product.availability==='available'?'available':'made_to_order',
    active:true,
    sort_order:Math.max(0,catalog.items.findIndex(item=>item.id===product.id)),
    specifications:values.flatMap(value=>{
      const definition=group.specifications.find(item=>item.key===value.key)
      return definition?[{definition_id:definition.id,value_text:value.value_text??null}]:[]
    }),
  }
}
async function editFromCatalog(product:Product){
  if(!admin.identity||adminEditorBusy.value)return
  adminEditorBusy.value=true;adminEditorError.value=''
  try{
    if(!adminTaxonomy.value.length)adminTaxonomy.value=(await fetchProductTaxonomy()).subcategories
    adminProduct.value=await resolveCatalogProduct(product)
    editingCatalogId.value=product.id
    editorMode.value=adminProduct.value?'edit':'materialize'
    adminInitial.value=adminProduct.value?null:seedFields(product)
    if(!adminProduct.value&&!adminInitial.value)throw new Error('taxonomy missing')
    adminEditorOpen.value=true
  }catch{
    adminEditorError.value=locale.t('adminRequestFailed')
  }finally{adminEditorBusy.value=false}
}
function adminProductPersisted(product:AdminProduct){
  if(editingCatalogId.value===null)return
  const group=adminTaxonomy.value.find(item=>item.id===product.subcategory_id)
  catalog.acceptBackendProduct(editingCatalogId.value,{
    id:product.id,code:product.code,
    names:{fa:product.name_fa,ar:product.name_ar,en:product.name_en,ku:product.name_ku},
    availability:product.availability,sort_order:product.sort_order,
    category:{code:product.category_code,names:{fa:group?.category_name??null,ar:null,en:null,ku:null}},
    subcategory:{code:product.subcategory_code,names:{fa:group?.name??null,ar:null,en:null,ku:null}},
    specifications:product.specifications.map(spec=>({key:spec.key,locked:spec.locked,labels:spec.labels,value_text:spec.value_text??null})),
    media:product.media.map(media=>({...media,id:String(media.id)})),
  })
}
async function adminEditorSaved(){
  adminEditorOpen.value=false;adminProduct.value=null;adminInitial.value=null;editingCatalogId.value=null
  await catalog.hydrateFromBackend(true)
}
function adminEditorClosed(){
  adminEditorOpen.value=false;adminProduct.value=null;adminInitial.value=null;editingCatalogId.value=null
}
async function editFromManager(product:AdminProduct|null){
  adminEditorError.value=''
  try{
    if(!adminTaxonomy.value.length)adminTaxonomy.value=(await fetchProductTaxonomy()).subcategories
    managerOpen.value=false
    editingCatalogId.value=null;editorMode.value=product?'edit':'create'
    adminProduct.value=product
    adminInitial.value=null
    adminEditorOpen.value=true
  }catch{
    adminEditorError.value=locale.t('adminRequestFailed')
  }
}
</script>

<template>
  <section data-style-id="products.page" data-style-label="زمینه صفحه محصولات" class="products-page" :data-browse-ready="browseReady">
    <div class="products-intro-surface">
      <div class="mb-4 flex items-end justify-between gap-3">
        <div>
          <h1 class="text-[1.75rem] font-black leading-tight">{{locale.t('productsTitle')}}</h1>
          <p class="mt-1 text-sm leading-6 text-[var(--c-muted)]">{{locale.t('productsHelp')}}</p>
        </div>
        <button v-if="activeCount" class="hidden text-xs font-extrabold text-[var(--c-primary)] lg:inline-flex" @click="resetFilters">
          {{locale.t('clearFilters')}} · {{activeCount}}
        </button>
      </div>

      <div class="category-showcase grid grid-cols-3 gap-2.5 md:gap-3">
      <button
        v-for="cat in catalog.categories"
        :key="cat.code"
        type="button"
        class="category-card"
        :class="{active:category===cat.code}"
        :aria-pressed="category===cat.code"
        @click="selectCategory(cat.code)"
      >
        <span class="category-card-media">
          <img :src="cat.image" alt="" loading="eager" decoding="async">
        </span>
        <span class="category-card-footer">
          <span v-if="policy.showCategoryNumbers" class="category-number">0{{cat.code}}</span>
          <b class="category-card-title">{{locale.categoryName(cat.code,cat.name)}}</b>
          <ChevronLeft :size="16" class="category-chevron" aria-hidden="true"/>
        </span>
      </button>
      </div>
    </div>

    <!-- Mobile + tablet controls intentionally stay lightweight and app-like. -->
    <div class="products-mobile-controls mt-3 lg:hidden">
      <div class="chip-scroller flex gap-2 overflow-x-auto pb-1">
        <button class="filter-chip" :class="{active:subcategory==='all'}" @click="subcategory='all'">{{locale.t('allSubs')}}</button>
        <button v-for="sub in subs" :key="sub.code" class="filter-chip" :class="{active:subcategory===sub.code}" @click="subcategory=sub.code"><span v-if="policy.showSubcategoryCodes" class="subcategory-code">{{sub.code}} · </span>{{locale.subcategoryName(sub.code,sub.name)}}</button>
      </div>

      <div class="products-filter-row mt-4 flex flex-wrap gap-2">
        <label class="flex min-h-11 flex-1 basis-56 items-center gap-2 rounded-xl border border-[var(--c-border)] bg-[var(--c-surface)] px-3">
          <Search :size="18" class="shrink-0 text-[var(--c-muted)]"/>
          <input v-model="query" class="min-w-0 flex-1 bg-transparent text-sm outline-none" :placeholder="locale.t('search')"/>
        </label>
        <select v-model="availability" class="min-h-11 rounded-xl border border-[var(--c-border)] bg-[var(--c-surface)] px-3 text-sm">
          <option value="all">{{locale.t('allStatuses')}}</option>
          <option value="available">{{locale.t('available')}}</option>
          <option value="made_to_order">{{locale.t('madeToOrder')}}</option>
        </select>
      </div>
    </div>

    <div class="commerce-layout mt-5 lg:grid lg:grid-cols-[15.5rem_minmax(0,1fr)] lg:items-start lg:gap-5">
      <aside class="desktop-filter-panel hidden rounded-2xl p-3 lg:block lg:sticky lg:top-[5.25rem]">
        <label class="mb-4 flex min-h-11 items-center gap-2 rounded-xl border border-[var(--c-border)] bg-[var(--c-surface-2)] px-3">
          <Search :size="17" class="shrink-0 text-[var(--c-muted)]"/>
          <input v-model="query" class="min-w-0 flex-1 bg-transparent text-xs outline-none" :placeholder="locale.t('search')"/>
        </label>

        <div v-if="subs.length" class="border-b border-[var(--c-border)] pb-4">
          <div class="mb-2 text-xs font-black text-[var(--c-text)]">{{locale.t('subcategoryLabel')}}</div>
          <button class="desktop-filter-option" :class="{active:subcategory==='all'}" @click="subcategory='all'">
            <span>{{locale.t('allSubs')}}</span><Check v-if="subcategory==='all'" :size="15"/>
          </button>
          <button v-for="sub in subs" :key="sub.code" class="desktop-filter-option" :class="{active:subcategory===sub.code}" @click="subcategory=sub.code">
            <span><span v-if="policy.showSubcategoryCodes" class="subcategory-code">{{sub.code}} · </span>{{locale.subcategoryName(sub.code,sub.name)}}</span><Check v-if="subcategory===sub.code" :size="15"/>
          </button>
        </div>

        <div class="pt-4">
          <label class="mb-2 block text-xs font-black text-[var(--c-text)]">{{locale.t('statusLabel')}}</label>
          <select v-model="availability" class="min-h-10 w-full rounded-xl border border-[var(--c-border)] bg-[var(--c-surface-2)] px-3 text-xs">
            <option value="all">{{locale.t('allStatuses')}}</option>
            <option value="available">{{locale.t('available')}}</option>
            <option value="made_to_order">{{locale.t('madeToOrder')}}</option>
          </select>
        </div>
      </aside>

      <div class="commerce-content min-w-0">
        <div class="mb-3 hidden items-center justify-between rounded-xl border border-[var(--c-border)] bg-[var(--c-surface)] px-4 py-3 text-xs lg:flex">
          <span class="font-bold text-[var(--c-text)]">{{filtered.length}} {{locale.t('productCount')}}</span>
          <span class="text-[var(--c-muted)]">{{locale.t('desktopFilterHelp')}}</span>
        </div>
        <p v-if="adminEditorError" class="auth-error mb-3" role="alert">{{adminEditorError}}</p>
        <ProductGrid v-if="filtered.length" :products="filtered" :admin-editable="!!admin.identity&&!adminEditorBusy" @edit="editFromCatalog"/>
        <div v-else class="rounded-2xl border border-dashed border-[var(--c-border)] bg-[var(--c-surface)] p-10 text-center text-sm text-[var(--c-muted)]">—</div>
      </div>
    </div>
    <BackendProductEditor
      v-if="adminEditorOpen"
      :open="adminEditorOpen"
      :product="adminProduct"
      :initial="adminInitial"
      :mode="editorMode"
      @persisted="adminProductPersisted"
      :taxonomy="adminTaxonomy"
      @close="adminEditorClosed"
      @saved="adminEditorSaved"
    />
    <button
      v-if="admin.identity&&!managerOpen&&!adminEditorOpen"
      type="button"
      class="visual-editor-quick-launcher"
      :aria-label="locale.t('adminProducts')"
      @click="managerOpen=true"
    ><Pencil :size="18"/><span>{{locale.t('adminProducts')}}</span></button>
    <AdaptivePanel :open="managerOpen" :title="locale.t('adminProducts')" wide @close="managerOpen=false">
      <BackendProductsPanel v-if="managerOpen" external-editor @edit-request="editFromManager"/>
    </AdaptivePanel>
  </section>
</template>
