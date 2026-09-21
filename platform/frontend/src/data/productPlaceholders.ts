import type { Product } from '@/types/domain'

export type PlaceholderSetId = 'flat-geometric' | 'paper-cut' | 'dimensional'
export type PlaceholderOrientation = 'portrait' | 'landscape' | 'auto'

export interface PlaceholderSet {
  id: PlaceholderSetId
  labelKey: string
  descriptionKey: string
  preview: string
}

export const defaultPlaceholderSet: PlaceholderSetId = 'paper-cut'
export const defaultPlaceholderOrientation: PlaceholderOrientation = 'portrait'

export const placeholderSets: PlaceholderSet[] = [
  {
    id: 'paper-cut',
    labelKey: 'placeholderPaperCut',
    descriptionKey: 'placeholderPaperCutHelp',
    preview: './images/placeholders/paper-cut/sub-11.webp',
  },
  {
    id: 'flat-geometric',
    labelKey: 'placeholderFlat',
    descriptionKey: 'placeholderFlatHelp',
    preview: './images/placeholders/flat-geometric/sub-11.webp',
  },
  {
    id: 'dimensional',
    labelKey: 'placeholderDimensional',
    descriptionKey: 'placeholderDimensionalHelp',
    preview: './images/placeholders/dimensional/sub-11.webp',
  },
]

export function isPlaceholderSetId(value: string | null): value is PlaceholderSetId {
  return placeholderSets.some((set) => set.id === value)
}

export function isPlaceholderOrientation(value: string | null): value is PlaceholderOrientation {
  return value === 'portrait' || value === 'landscape' || value === 'auto'
}

export function landscapePlaceholder(
  set: PlaceholderSetId,
  subcategoryCode: Product['subcategoryCode'],
): string {
  return `./images/placeholders/${set}/sub-${subcategoryCode}.webp`
}

export function portraitPlaceholder(
  set: PlaceholderSetId,
  subcategoryCode: Product['subcategoryCode'],
): string {
  return `./images/placeholders-portrait/${set}/sub-${subcategoryCode}.webp`
}

export function productPlaceholder(
  set: PlaceholderSetId,
  subcategoryCode: Product['subcategoryCode'],
  orientation: PlaceholderOrientation = defaultPlaceholderOrientation,
): string {
  return orientation === 'landscape'
    ? landscapePlaceholder(set, subcategoryCode)
    : portraitPlaceholder(set, subcategoryCode)
}
