import { defineStore } from 'pinia'
import { ref, watch } from 'vue'
import { useAdminStore } from '../store'
import { useCatalogStore } from '@/stores/catalog'
import { CustomerSessionApiError } from '@/features/auth/services/customerSessionApi'
import { fetchProductOrder, saveProductOrder, type ProductOrderSnapshot, type ProductOrderGroup } from './adminApi'
import { moveOrderedProduct, sameProductOrder, type OrderMove } from './productOrder'

/** One shared, server-authoritative order for table, cards and Home. */
export const useProductQuickOrder = defineStore('product-quick-order', () => {
  const admin = useAdminStore(), catalog = useCatalogStore()
  const snapshot = ref<ProductOrderSnapshot | null>(null)
  const busy = ref(false), error = ref(''), note = ref('')
  let generation = 0
  function locate(code: string) {
    for (const group of ['available', 'producible'] as ProductOrderGroup[]) {
      const rows = snapshot.value?.groups[group].products ?? []
      const index = rows.findIndex(row => row.code === code)
      if (index >= 0) return { group, rows, index }
    }
    return null
  }
  function failure(e: unknown) {
    if (e instanceof CustomerSessionApiError && [401,403].includes(e.status)) { admin.clear(); return }
    error.value = e instanceof CustomerSessionApiError && e.status === 409 ? 'orderConflict' : 'adminRequestFailed'
  }
  function apply(value: ProductOrderSnapshot) {
    snapshot.value = value
    const ranks = new Map(Object.values(value.groups).flatMap(group => group.products).map(row => [row.id, row.sort_order]))
    // IDs are canonical server IDs, never local card IDs or the current filtered slice.
    catalog.items = catalog.items.map(item => item.backendId !== undefined && ranks.has(item.backendId)
      ? { ...item, sortOrder: ranks.get(item.backendId)! } : item)
  }
  async function reload() {
    if (!admin.identity || busy.value) return
    const version = generation
    busy.value = true; error.value = ''; note.value = ''
    try { const value = await fetchProductOrder(); if (version === generation && admin.identity) apply(value) }
    catch (e) { if (version === generation) failure(e) }
    finally { if (version === generation) busy.value = false }
  }
  async function move(code: string, direction: OrderMove) {
    const found = locate(code)
    if (!admin.identity || busy.value || error.value || !snapshot.value || !found) return false
    const next = moveOrderedProduct(found.rows, found.rows[found.index]!.id, direction)
    if (sameProductOrder(next, found.rows)) return false
    const version = generation, revision = snapshot.value.groups[found.group].revision
    busy.value = true; error.value = ''; note.value = ''
    try {
      const value = await saveProductOrder(found.group, revision, next.map(row => row.id))
      if (version !== generation || !admin.identity) return false
      apply(value); note.value = 'orderSaved'; return true
    } catch (e) { if (version === generation) failure(e); return false }
    finally { if (version === generation) busy.value = false }
  }
  watch(() => admin.identity, identity => {
    generation++; snapshot.value = null; busy.value = false; error.value = ''; note.value = ''
    if (identity) void reload()
  }, { immediate: true, flush: 'sync' })
  return { snapshot, busy, error, note, locate, reload, move }
})
