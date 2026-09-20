<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Check, Search } from '@lucide/vue'
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
const activeCount=computed(()=>Number(category.value!=='all')+Number(subcategory.value!=='all')+Number(availability.value!=='all')+Number(Boolean(query.value.trim())))
function resetFilters(){category.value='all';subcategory.value='all';availability.value='all';query.value=''}
</script>

<template>
  <section>
    <div class="mb-4 flex items-end justify-between gap-3">
      <div>
        <h1 class="text-2xl font-black">{{locale.t('productsTitle')}}</h1>
        <p class="mt-1 text-sm text-[var(--c-muted)]">{{locale.t('productsHelp')}}</p>
      </div>
      <button v-if="activeCount" class="hidden text-xs font-extrabold text-[var(--c-primary)] lg:inline-flex" @click="resetFilters">
        {{locale.t('clearFilters')}} · {{activeCount}}
      </button>
    </div>

    <!-- Mobile + tablet controls intentionally preserve the app-like Test 15 behavior. -->
    <div class="lg:hidden">
      <div class="grid grid-cols-3 gap-2">
        <button v-for="cat in categories" :key="cat.code" class="rounded-2xl border p-3 text-start" :class="category===cat.code?'border-[var(--c-primary)] bg-indigo-50 dark:bg-indigo-950/30':'border-[var(--c-border)] bg-[var(--c-surface)]'" @click="category=cat.code;subcategory='all'">
          <span class="text-[10px] font-extrabold text-[var(--c-primary)]">0{{cat.code}}</span><b class="mt-1 block text-sm">{{cat.name}}</b>
        </button>
      </div>

      <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
        <button class="filter-chip" :class="{active:subcategory==='all'}" @click="subcategory='all'">{{locale.t('allSubs')}}</button>
        <button v-for="sub in subs" :key="sub.code" class="filter-chip" :class="{active:subcategory===sub.code}" @click="subcategory=sub.code">{{sub.code}} · {{sub.name}}</button>
      </div>

      <div class="mt-4 flex flex-wrap gap-2">
        <label class="flex min-h-11 flex-1 basis-56 items-center gap-2 rounded-xl border border-[var(--c-border)] bg-[var(--c-surface)] px-3">
          <Search :size="18" class="shrink-0 text-[var(--c-muted)]"/>
          <input v-model="query" class="min-w-0 flex-1 bg-transparent text-sm outline-none" :placeholder="locale.t('search')"/>
        </label>
        <select v-model="availability" class="min-h-11 rounded-xl border border-[var(--c-border)] bg-[var(--c-surface)] px-3 text-sm">
          <option value="all">{{locale.t('allStatuses')}}</option>
          <option value="available">{{locale.t('available')}}</option>
          <option value="unavailable">{{locale.t('unavailable')}}</option>
          <option value="made_to_order">{{locale.t('madeToOrder')}}</option>
        </select>
      </div>
    </div>

    <!-- Desktop becomes a commerce workspace: stable facet rail + broad product canvas. -->
    <div class="mt-5 lg:grid lg:grid-cols-[15.5rem_minmax(0,1fr)] lg:items-start lg:gap-5">
      <aside class="desktop-filter-panel hidden rounded-2xl p-3 lg:block lg:sticky lg:top-[5.25rem]">
        <label class="mb-4 flex min-h-11 items-center gap-2 rounded-xl border border-[var(--c-border)] bg-[var(--c-surface-2)] px-3">
          <Search :size="17" class="shrink-0 text-[var(--c-muted)]"/>
          <input v-model="query" class="min-w-0 flex-1 bg-transparent text-xs outline-none" :placeholder="locale.t('search')"/>
        </label>

        <div class="border-b border-[var(--c-border)] pb-4">
          <div class="mb-2 text-xs font-black text-[var(--c-text)]">{{locale.t('categoryLabel')}}</div>
          <button class="desktop-filter-option" :class="{active:category==='all'}" @click="category='all';subcategory='all'">
            <span>{{locale.t('allCategories')}}</span><Check v-if="category==='all'" :size="15"/>
          </button>
          <button v-for="cat in categories" :key="cat.code" class="desktop-filter-option" :class="{active:category===cat.code}" @click="category=cat.code;subcategory='all'">
            <span>{{cat.name}}</span><Check v-if="category===cat.code" :size="15"/>
          </button>
        </div>

        <div v-if="subs.length" class="border-b border-[var(--c-border)] py-4">
          <div class="mb-2 text-xs font-black text-[var(--c-text)]">{{locale.t('subcategoryLabel')}}</div>
          <button class="desktop-filter-option" :class="{active:subcategory==='all'}" @click="subcategory='all'">
            <span>{{locale.t('allSubs')}}</span><Check v-if="subcategory==='all'" :size="15"/>
          </button>
          <button v-for="sub in subs" :key="sub.code" class="desktop-filter-option" :class="{active:subcategory===sub.code}" @click="subcategory=sub.code">
            <span>{{sub.code}} · {{sub.name}}</span><Check v-if="subcategory===sub.code" :size="15"/>
          </button>
        </div>

        <div class="pt-4">
          <label class="mb-2 block text-xs font-black text-[var(--c-text)]">{{locale.t('statusLabel')}}</label>
          <select v-model="availability" class="min-h-10 w-full rounded-xl border border-[var(--c-border)] bg-[var(--c-surface-2)] px-3 text-xs">
            <option value="all">{{locale.t('allStatuses')}}</option>
            <option value="available">{{locale.t('available')}}</option>
            <option value="unavailable">{{locale.t('unavailable')}}</option>
            <option value="made_to_order">{{locale.t('madeToOrder')}}</option>
          </select>
        </div>
      </aside>

      <div class="min-w-0">
        <div class="mb-3 hidden items-center justify-between rounded-xl border border-[var(--c-border)] bg-[var(--c-surface)] px-4 py-3 text-xs lg:flex">
          <span class="font-bold text-[var(--c-text)]">{{filtered.length}} {{locale.t('productCount')}}</span>
          <span class="text-[var(--c-muted)]">{{locale.t('desktopFilterHelp')}}</span>
        </div>
        <ProductGrid v-if="filtered.length" :products="filtered"/>
        <div v-else class="rounded-2xl border border-dashed border-[var(--c-border)] bg-[var(--c-surface)] p-10 text-center text-sm text-[var(--c-muted)]">—</div>
      </div>
    </div>
  </section>
</template>
