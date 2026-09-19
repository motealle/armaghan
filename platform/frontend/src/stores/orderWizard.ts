import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { categories, products } from '@/data/catalog'
import type { RequestPath } from '@/types/domain'
import { buildProductionMessage, whatsappUrl } from '@/services/whatsapp'
import { useLocaleStore } from '@/stores/locale'

type ProductionPath = Extract<RequestPath, 'custom' | 'brand' | 'packaging'>

export const useOrderWizardStore = defineStore('order-wizard', () => {
  const locale=useLocaleStore()
  const step = ref(0)
  const path = ref<ProductionPath | null>(null)
  const categoryCode = ref<string | null>(null)
  const subcategoryCode = ref<string | null>(null)
  const note = ref('')

  const category = computed(() => categories.find((item) => item.code === categoryCode.value) ?? null)
  const subcategory = computed(() => category.value?.subcategories.find((item) => item.code === subcategoryCode.value) ?? null)
  const specSource = computed(() => products.find((item) => item.subcategoryCode === subcategoryCode.value) ?? null)

  const preview = computed(() => {
    if (!path.value || !category.value || !subcategory.value || !specSource.value) return ''
    return buildProductionMessage({
      path: path.value,
      category: category.value.name,
      subcategory: `${subcategory.value.code} · ${subcategory.value.name}`,
      negotiable: specSource.value.specs.negotiable,
      locked: specSource.value.specs.locked,
      note: note.value,
      locale: locale.locale,
    })
  })

  const whatsapp = computed(() => preview.value ? whatsappUrl(preview.value) : '#')

  function choosePath(value: ProductionPath): void {
    path.value = value; categoryCode.value = null; subcategoryCode.value = null; note.value = ''; step.value = 1
  }
  function chooseCategory(value: string): void { categoryCode.value = value; subcategoryCode.value = null; step.value = 2 }
  function chooseSubcategory(value: string): void { subcategoryCode.value = value; step.value = 3 }
  function back(): void {
    if (step.value === 3) { subcategoryCode.value = null; step.value = 2 }
    else if (step.value === 2) { categoryCode.value = null; step.value = 1 }
    else reset()
  }
  function reset(): void { step.value = 0; path.value = null; categoryCode.value = null; subcategoryCode.value = null; note.value = '' }

  return { step, path, categoryCode, subcategoryCode, note, category, subcategory, specSource, preview, whatsapp, choosePath, chooseCategory, chooseSubcategory, back, reset }
})
