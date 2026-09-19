import { defineStore } from 'pinia'
import { computed, ref, watch } from 'vue'
import { useCatalogStore } from '@/stores/catalog'

const KEY = 'armaghan:test15:favorites'

function load(): number[] {
  try { return JSON.parse(localStorage.getItem(KEY) ?? '[]') as number[] } catch { return [] }
}

export const useFavoritesStore = defineStore('favorites', () => {
  const ids = ref<number[]>(load())
  const catalog = useCatalogStore()
  const items = computed(() => catalog.items.filter((product) => ids.value.includes(product.id)))

  function toggle(id: number): void {
    ids.value = ids.value.includes(id) ? ids.value.filter((x) => x !== id) : [...ids.value, id]
  }
  function has(id: number): boolean { return ids.value.includes(id) }

  watch(ids, (value) => localStorage.setItem(KEY, JSON.stringify(value)), { deep: true })
  return { ids, items, toggle, has }
})
