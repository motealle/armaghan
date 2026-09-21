import { defineStore } from 'pinia'
import { ref, watch } from 'vue'
import { defaultPlaceholderSet, isPlaceholderSetId, type PlaceholderSetId } from '@/data/productPlaceholders'

export interface DesignSystem { id: string; name: string; description: string }
export interface ColorSet { id: string; name: string; colors: [string,string,string,string,string] }
export type CardActionMode = 'compact' | 'labeled'

export const designSystems: DesignSystem[] = [
  { id: 'core', name: 'Armaghan Core', description: 'متعادل، برندمحور و مناسب نسخه اصلی' },
  { id: 'royal', name: 'Royal Commerce', description: 'رسمی‌تر و B2B' },
  { id: 'mint', name: 'Soft Mint Studio', description: 'آرام و روشن' },
  { id: 'dark', name: 'Executive Dark', description: 'داشبوردی و تیره' },
  { id: 'editorial', name: 'Editorial Luxe', description: 'تصویرمحور و فشن' },
]
export const colorSets: ColorSet[] = [
  { id:'balanced', name:'Brand Balanced', colors:['#151EDA','#21946A','#C8E3DB','#FFFEFF','#FFB514'] },
  { id:'deep', name:'Deep Royal', colors:['#0E178C','#151EDA','#DDE7F7','#FAFBFF','#F2B300'] },
  { id:'mint', name:'Mint Commerce', colors:['#1B6E59','#21946A','#C8E3DB','#F8FCFB','#E6A90A'] },
  { id:'dark', name:'Dark Premium', colors:['#0D132B','#1C2560','#BFD6CF','#F5F7FA','#FFB514'] },
  { id:'clean', name:'Clean Light', colors:['#2432D9','#2A8F6A','#E3F1EC','#FFFFFF','#FFBF2A'] },
]

export const useDesignStore = defineStore('design', () => {
  const system = ref(localStorage.getItem('armaghan:test22:design') || 'core')
  const palette = ref(localStorage.getItem('armaghan:test22:palette') || 'balanced')
  const savedActionMode = localStorage.getItem('armaghan:test22:card-actions')
  const cardActionMode = ref<CardActionMode>(savedActionMode === 'labeled' ? 'labeled' : 'compact')
  const savedPlaceholderSet = localStorage.getItem('armaghan:test22:placeholder-set')
  const placeholderSet = ref<PlaceholderSetId>(isPlaceholderSetId(savedPlaceholderSet) ? savedPlaceholderSet : defaultPlaceholderSet)

  function apply(): void {
    const selected = colorSets.find((item) => item.id === palette.value) ?? colorSets[0]!
    const [primary, secondary, soft, paper, accent] = selected.colors
    const root = document.documentElement
    root.dataset.design = system.value
    root.style.setProperty('--c-primary', primary)
    root.style.setProperty('--c-secondary', secondary)
    root.style.setProperty('--c-soft', soft)
    root.style.setProperty('--c-paper', paper)
    root.style.setProperty('--c-accent', accent)
  }

  watch([system, palette], () => {
    localStorage.setItem('armaghan:test22:design', system.value)
    localStorage.setItem('armaghan:test22:palette', palette.value)
    apply()
  })
  watch(cardActionMode, (value) => localStorage.setItem('armaghan:test22:card-actions', value))
  watch(placeholderSet, (value) => localStorage.setItem('armaghan:test22:placeholder-set', value))

  return { system, palette, cardActionMode, placeholderSet, apply }
})
