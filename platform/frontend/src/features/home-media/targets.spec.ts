import { expect, it } from 'vitest'
import { homeImageTarget, homeImageTargets } from './targets'

it('provides all nine home images including official documents and maps panel/text/media selection to the same image',()=>{
  expect(homeImageTargets).toHaveLength(9)
  for(const target of homeImageTargets){
    expect(homeImageTarget(target.id)).toBe(target.target)
    expect(homeImageTarget(target.id.replace(/\.media$/,''))).toBe(target.target)
    expect(homeImageTarget(target.id.replace(/\.media$/,'.title'))).toBe(target.target)
  }
  expect(homeImageTarget('product.media')).toBe('')
  expect(homeImageTarget('home.about-other')).toBe('')
})
