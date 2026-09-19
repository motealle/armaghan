<script setup lang="ts">
import ProductGrid from '@/features/catalog/components/ProductGrid.vue'
import { useFavoritesStore } from '@/stores/favorites'
import { useLocaleStore } from '@/stores/locale'
const favorites=useFavoritesStore()
const locale=useLocaleStore()
</script>
<template><section><div class="mb-4"><h1 class="text-2xl font-black">{{locale.t('favoriteTitle')}}</h1><p class="mt-1 text-sm text-slate-500">{{locale.t('favoriteHelp')}}</p></div><ProductGrid v-if="favorites.items.length" :products="favorites.items"/><div v-else class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500">{{locale.t('emptyFavorites')}}</div></section></template>
