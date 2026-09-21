import { describe, expect, it } from 'vitest'
import { defaultPlaceholderSet, isPlaceholderSetId, placeholderSets, productPlaceholder } from './productPlaceholders'

describe('product placeholder registry', () => {
  it('keeps three complete, unique and validated visual sets', () => {
    expect(placeholderSets.map((set) => set.id)).toEqual(['paper-cut', 'flat-geometric', 'dimensional'])
    expect(defaultPlaceholderSet).toBe('paper-cut')
    expect(isPlaceholderSetId('paper-cut')).toBe(true)
    expect(isPlaceholderSetId('unknown')).toBe(false)
  })

  it('resolves every subcategory to a stable web asset', () => {
    for (const set of placeholderSets) {
      for (const code of ['11', '12', '21', '22', '31', '32'] as const) {
        expect(productPlaceholder(set.id, code)).toBe(`./images/placeholders/${set.id}/sub-${code}.webp`)
      }
    }
  })
})
