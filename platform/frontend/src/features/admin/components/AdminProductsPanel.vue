<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { ChevronLeft, ChevronRight, ImagePlus, MoreVertical, PackagePlus, Pencil, Search, Trash2 } from '@lucide/vue'
import { categories } from '@/data/catalog'
import { compressImage } from '@/features/admin/services/imageCompression'
import ProductEditorPanel from '@/features/admin/components/ProductEditorPanel.vue'
import { useCatalogStore } from '@/stores/catalog'
import { useLocaleStore } from '@/stores/locale'
import type { Locale } from '@/services/localeDetection'
import type { Product } from '@/types/domain'

const catalog=useCatalogStore()
const locale=useLocaleStore()
const query=ref('')
const categoryFilter=ref('')
const subcategoryFilter=ref('')
const page=ref(1)
const pageSize=ref(50)
const selected=ref<number[]>([])
const openMenuId=ref<number|null>(null)
const editorOpen=ref(false)
const editorProductId=ref<number|null>(null)
const note=ref('')

const availableSubcategories=computed(()=>categories
  .filter(category=>!categoryFilter.value||category.code===categoryFilter.value)
  .flatMap(category=>category.subcategories)
)

const filtered=computed(()=>{
  const needle=query.value.trim().toLocaleLowerCase()
  return catalog.items.filter(product=>{
    if(categoryFilter.value&&product.categoryCode!==categoryFilter.value)return false
    if(subcategoryFilter.value&&product.subcategoryCode!==subcategoryFilter.value)return false
    if(!needle)return true
    const names=(['fa','ar','en','ku'] as Locale[])
      .map(lang=>locale.productNameFor(product.code,lang,product.name,product.names))
      .join(' ')
      .toLocaleLowerCase()
    return (product.code+' '+names).includes(needle)
  })
})
const pageCount=computed(()=>Math.max(1,Math.ceil(filtered.value.length/pageSize.value)))
const paged=computed(()=>{
  const start=(page.value-1)*pageSize.value
  return filtered.value.slice(start,start+pageSize.value)
})
const allPageSelected=computed(()=>paged.value.length>0&&paged.value.every(product=>selected.value.includes(product.id)))
const rangeLabel=computed(()=>{
  if(!filtered.value.length)return '0'
  const start=(page.value-1)*pageSize.value+1
  const end=Math.min(page.value*pageSize.value,filtered.value.length)
  return start+'–'+end+' / '+filtered.value.length
})

watch([query,categoryFilter,subcategoryFilter,pageSize],()=>{
  page.value=1
  selected.value=[]
  openMenuId.value=null
})
watch(pageCount,count=>{if(page.value>count)page.value=count})

function toggleAllPage(){
  const ids=paged.value.map(product=>product.id)
  if(allPageSelected.value)selected.value=selected.value.filter(id=>!ids.includes(id))
  else selected.value=[...new Set([...selected.value,...ids])]
}
function bulkDelete(){
  if(!selected.value.length||!window.confirm(locale.t('confirmDelete')))return
  catalog.removeMany(selected.value)
  selected.value=[]
}
function createProduct(){editorProductId.value=null;editorOpen.value=true;openMenuId.value=null}
function editProduct(id:number){editorProductId.value=id;editorOpen.value=true;openMenuId.value=null}
function removeProduct(id:number){
  if(!window.confirm(locale.t('confirmDelete')))return
  catalog.remove(id);openMenuId.value=null
}
async function upload(id:number,event:Event){
  const file=(event.target as HTMLInputElement).files?.[0]
  if(!file)return
  try{catalog.updateImage(id,await compressImage(file));note.value=locale.t('save')}catch{}
  openMenuId.value=null
}
function categoryLabel(product:Product){return locale.categoryName(product.categoryCode,product.categoryName)}
function subcategoryLabel(product:Product){return locale.subcategoryName(product.subcategoryCode,product.subcategoryName)}
</script>

<template>
  <section class="space-y-3">
    <div class="flex flex-wrap items-center gap-2">
      <div>
        <h2 class="text-xl font-black text-[var(--c-text)]">{{locale.t('adminProducts')}}</h2>
        <p class="text-xs text-[var(--c-muted)]">{{filtered.length}} {{locale.t('productCount')}}</p>
      </div>
      <button class="ms-auto inline-flex min-h-11 items-center gap-2 rounded-xl bg-[var(--c-primary)] px-4 text-sm font-black text-white" @click="createProduct">
        <PackagePlus :size="17"/>{{locale.t('addProduct')}}
      </button>
    </div>

    <div class="admin-surface grid gap-2 rounded-2xl p-3 lg:grid-cols-[1fr_12rem_14rem_auto]">
      <label class="form-field">{{locale.t('productSearch')}}
        <span class="relative block">
          <Search :size="16" class="pointer-events-none absolute start-3 top-1/2 mt-1 -translate-y-1/2 text-[var(--c-muted)]"/>
          <input v-model="query" class="ps-9" :placeholder="locale.t('search')"/>
        </span>
      </label>
      <label class="form-field">{{locale.t('categoryLabel')}}
        <select v-model="categoryFilter">
          <option value="">{{locale.t('allCategories')}}</option>
          <option v-for="category in categories" :key="category.code" :value="category.code">{{locale.categoryName(category.code,category.name)}}</option>
        </select>
      </label>
      <label class="form-field">{{locale.t('subcategoryLabel')}}
        <select v-model="subcategoryFilter">
          <option value="">{{locale.t('allSubs')}}</option>
          <option v-for="sub in availableSubcategories" :key="sub.code" :value="sub.code">{{sub.code}} · {{locale.subcategoryName(sub.code,sub.name)}}</option>
        </select>
      </label>
      <label class="form-field">{{locale.t('rowsPerPage')}}
        <select v-model.number="pageSize"><option :value="25">25</option><option :value="50">50</option><option :value="100">100</option></select>
      </label>
    </div>

    <div v-if="selected.length" class="batch-bar">
      <b>{{selected.length}} {{locale.t('selectedCount')}}</b>
      <button class="ms-auto mini-action text-rose-700" @click="bulkDelete"><Trash2 :size="15"/>{{locale.t('bulkDelete')}}</button>
    </div>

    <div class="data-table-shell">
      <table class="data-table">
        <thead>
          <tr>
            <th><input type="checkbox" :checked="allPageSelected" :aria-label="locale.t('selectAll')" @change="toggleAllPage"/></th>
            <th>{{locale.t('productsTitle')}}</th>
            <th>{{locale.t('codeLabel')}}</th>
            <th>{{locale.t('categoryLabel')}}</th>
            <th>{{locale.t('subcategoryLabel')}}</th>
            <th>{{locale.t('statusLabel')}}</th>
            <th>{{locale.t('actions')}}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in paged" :key="product.id">
            <td><input v-model="selected" type="checkbox" :value="product.id"/></td>
            <td><button class="text-start font-bold text-[var(--c-text)]" @click="editProduct(product.id)">{{locale.productName(product.code,product.name,product.names)}}</button></td>
            <td><code class="text-[var(--c-primary)]">{{product.code}}</code></td>
            <td>{{categoryLabel(product)}}</td>
            <td>{{subcategoryLabel(product)}}</td>
            <td>{{product.availability==='available'?locale.t('available'):product.availability==='unavailable'?locale.t('unavailable'):locale.t('madeToOrder')}}</td>
            <td>
              <div class="relative">
                <button
                  class="row-overflow"
                  :disabled="selected.length>0"
                  :aria-label="locale.t('moreActions')"
                  @click="openMenuId=openMenuId===product.id?null:product.id"
                ><MoreVertical :size="17"/></button>
                <div v-if="openMenuId===product.id&&selected.length===0" class="row-menu">
                  <button @click="editProduct(product.id)"><Pencil :size="14"/>{{locale.t('editProduct')}}</button>
                  <label class="row-menu-label"><ImagePlus :size="14"/>{{locale.t('profilePhoto')}}<input class="hidden" type="file" accept="image/*" @change="upload(product.id,$event)"/></label>
                  <button class="text-rose-700" @click="removeProduct(product.id)"><Trash2 :size="14"/>{{locale.t('delete')}}</button>
                </div>
              </div>
            </td>
          </tr>
          <tr v-if="!paged.length"><td colspan="7" class="py-8 text-center text-[var(--c-muted)]">{{locale.t('search')}}</td></tr>
        </tbody>
      </table>
    </div>

    <div class="pagination-bar">
      <span class="text-xs text-[var(--c-muted)]">{{rangeLabel}}</span>
      <div class="ms-auto flex items-center gap-1">
        <button class="pagination-button" :disabled="page<=1" :aria-label="locale.t('previous')" @click="page--"><ChevronRight :size="17"/></button>
        <span class="min-w-20 text-center text-xs font-bold text-[var(--c-text)]">{{locale.t('page')}} {{page}} / {{pageCount}}</span>
        <button class="pagination-button" :disabled="page>=pageCount" :aria-label="locale.t('next')" @click="page++"><ChevronLeft :size="17"/></button>
      </div>
    </div>

    <p v-if="note" class="text-xs font-bold text-[var(--c-secondary)]">{{note}}</p>
    <ProductEditorPanel :open="editorOpen" :product-id="editorProductId" @close="editorOpen=false"/>
  </section>
</template>
