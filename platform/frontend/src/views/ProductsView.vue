<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Search } from '@lucide/vue'
import { useRoute } from 'vue-router'
import { categories } from '@/data/catalog'
import { useCatalogStore } from '@/stores/catalog'
import { useLocaleStore } from '@/stores/locale'
import ProductGrid from '@/features/catalog/components/ProductGrid.vue'

const route=useRoute()
const catalog=useCatalogStore()
const locale=useLocaleStore()
const category=ref(String(route.query.category ?? 'all'))
const subcategory=ref('all')
const query=ref('')
const availability=ref('all')

watch(()=>route.query.category,(value)=>{category.value=value?String(value):'all';subcategory.value='all'})

const subs=computed(()=>category.value==='all'?[]:categories.find(c=>c.code===category.value)?.subcategories ?? [])
const filtered=computed(()=>catalog.items.filter(product=>{
  if(category.value!=='all'&&product.categoryCode!==category.value)return false
  if(subcategory.value!=='all'&&product.subcategoryCode!==subcategory.value)return false
  if(availability.value!=='all'&&product.availability!==availability.value)return false
  const needle=query.value.trim().toLowerCase()
  return !needle||(`${product.name} ${product.code} ${product.subcategoryName}`).toLowerCase().includes(needle)
}))
</script>

<template>
  <section>
    <div class="mb-4">
      <h1 class="text-2xl font-black">{{locale.t('productsTitle')}}</h1>
      <p class="mt-1 text-sm text-slate-500">{{locale.t('productsHelp')}}</p>
    </div>

    <div class="grid grid-cols-3 gap-2">
      <button v-for="cat in categories" :key="cat.code" class="rounded-2xl border p-3 text-start" :class="category===cat.code?'border-[var(--c-primary)] bg-indigo-50':'border-slate-200 bg-white'" @click="category=cat.code;subcategory='all'">
        <span class="text-[10px] font-extrabold text-[var(--c-primary)]">0{{cat.code}}</span><b class="mt-1 block text-sm">{{cat.name}}</b>
      </button>
    </div>

    <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
      <button class="filter-chip" :class="{active:subcategory==='all'}" @click="subcategory='all'">{{locale.t('allSubs')}}</button>
      <button v-for="sub in subs" :key="sub.code" class="filter-chip" :class="{active:subcategory===sub.code}" @click="subcategory=sub.code">{{sub.code}} · {{sub.name}}</button>
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
      <label class="flex min-h-11 flex-1 basis-56 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3">
        <Search :size="18" class="shrink-0 text-slate-400"/>
        <input v-model="query" class="min-w-0 flex-1 bg-transparent text-sm outline-none" :placeholder="locale.t('search')"/>
      </label>
      <select v-model="availability" class="min-h-11 rounded-xl border border-slate-200 bg-white px-3 text-sm">
        <option value="all">{{locale.t('allStatuses')}}</option>
        <option value="available">{{locale.t('available')}}</option>
        <option value="unavailable">{{locale.t('unavailable')}}</option>
        <option value="made_to_order">{{locale.t('madeToOrder')}}</option>
      </select>
    </div>

    <div class="mt-5">
      <ProductGrid v-if="filtered.length" :products="filtered"/>
      <div v-else class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500">—</div>
    </div>
  </section>
</template>
