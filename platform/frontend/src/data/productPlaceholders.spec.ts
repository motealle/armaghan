import { describe, expect, it } from 'vitest'
import {
  defaultPlaceholderOrientation,
  defaultPlaceholderSet,
  isPlaceholderOrientation,
  isPlaceholderSetId,
  landscapePlaceholder,
  placeholderSets,
  portraitPlaceholder,
  productPlaceholder,
} from './productPlaceholders'

describe('product placeholder registry', () => {
  it('keeps three complete, unique and validated visual sets', () => {
    expect(placeholderSets.map((set) => set.id)).toEqual(['paper-cut', 'flat-geometric', 'dimensional'])
    expect(defaultPlaceholderSet).toBe('paper-cut')
    expect(defaultPlaceholderOrientation).toBe('portrait')
    expect(isPlaceholderSetId('paper-cut')).toBe(true)
    expect(isPlaceholderSetId('unknown')).toBe(false)
    expect(isPlaceholderOrientation('portrait')).toBe(true)
    expect(isPlaceholderOrientation('landscape')).toBe(true)
    expect(isPlaceholderOrientation('auto')).toBe(true)
  })

  it('keeps landscape originals and resolves portrait derivatives by default', () => {
    for (const set of placeholderSets) {
      for (const code of ['11', '12', '21', '22', '31', '32'] as const) {
        expect(landscapePlaceholder(set.id, code)).toBe(`./images/placeholders/${set.id}/sub-${code}.webp`)
        expect(portraitPlaceholder(set.id, code)).toBe(`./images/placeholders-portrait/${set.id}/sub-${code}.webp`)
        expect(productPlaceholder(set.id, code)).toBe(`./images/placeholders-portrait/${set.id}/sub-${code}.webp`)
        expect(productPlaceholder(set.id, code, 'landscape')).toBe(`./images/placeholders/${set.id}/sub-${code}.webp`)
      }
    }
  })
})
