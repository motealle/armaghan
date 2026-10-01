import { describe, expect, it } from 'vitest'
import {
  contrastRatio,
  NORMAL_TEXT_MIN_CONTRAST,
  passesNormalTextContrast,
  tokenContrastRatio,
} from './contrast'

describe('visual editor contrast guard',()=>{
  it('uses the WCAG normal-text threshold',()=>{
    expect(NORMAL_TEXT_MIN_CONTRAST).toBe(4.5)
  })

  it('accepts strong approved-token pairs',()=>{
    expect(passesNormalTextContrast('blue','white')).toBe(true)
    expect(passesNormalTextContrast('white','blue')).toBe(true)
  })

  it('rejects weak approved-token pairs',()=>{
    expect(passesNormalTextContrast('gold','white')).toBe(false)
    expect(passesNormalTextContrast('mint','white')).toBe(false)
  })

  it('is symmetric and returns a measurable ratio',()=>{
    expect(tokenContrastRatio('blue','white')).toBeCloseTo(tokenContrastRatio('white','blue'),8)
    expect(contrastRatio('#000000','#FFFFFF')).toBeCloseTo(21,5)
  })
})
