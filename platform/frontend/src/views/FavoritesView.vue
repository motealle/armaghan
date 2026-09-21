<script setup lang="ts">
import { MessageCircleMore, X } from '@lucide/vue'
import ProductGrid from '@/features/catalog/components/ProductGrid.vue'
import { useCustomersStore } from '@/stores/customers'
import { useFavoritesStore } from '@/stores/favorites'
import { useLocaleStore } from '@/stores/locale'

const favorites=useFavoritesStore()
const customers=useCustomersStore()
const locale=useLocaleStore()
</script>

<template>
  <section>
    <div class="mb-4">
      <h1 class="text-[1.75rem] font-black leading-tight text-[var(--c-text)]">{{locale.t('favoriteTitle')}}</h1>
      <p class="mt-1 text-sm leading-6 text-[var(--c-muted)]">{{locale.t('favoriteHelp')}}</p>
    </div>

    <div v-if="customers.currentVisitorMessage" class="favorites-message-bubble">
      <MessageCircleMore :size="20"/>
      <p>{{customers.currentVisitorMessage}}</p>
      <button :aria-label="locale.t('close')" @click="customers.clearCurrentVisitorMessage()"><X :size="17"/></button>
    </div>

    <ProductGrid v-if="favorites.items.length" :products="favorites.items"/>
    <div v-else class="rounded-2xl border border-dashed border-[var(--c-border)] bg-[var(--c-surface)] p-10 text-center text-sm text-[var(--c-muted)]">{{locale.t('emptyFavorites')}}</div>
  </section>
</template>
