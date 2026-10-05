import { describe, expect, it } from 'vitest'
import { cardMediaSources, swipeDirection } from './cardMedia'
import { products } from '@/data/catalog'

describe('card gallery delivery',()=>{
  it('uses only server media once present, preserving each lightweight and detail variant',()=>{
    const media=[{thumb:'/thumb/1',card:'/card/1',detail:'/detail/1',width:1200,height:1800},{thumb:'/thumb/2',card:'/card/2',detail:'/detail/2',width:1800,height:1200}]
    expect(cardMediaSources({...products[0]!,media,gallery:['/old-placeholder']})).toEqual(media)
  })
  it('keeps legacy local photos ordered without duplicates and has no fake gallery for empty media',()=>{
    expect(cardMediaSources({...products[0]!,image:'/a',gallery:['/a','/b']})).toHaveLength(2)
    expect(cardMediaSources({...products[0]!,image:undefined,gallery:[]})).toEqual([])
  })
  it('allows vertical scrolling and short taps, changing pictures only on a horizontal swipe',()=>{
    expect(swipeDirection(20,1)).toBe(0)
    expect(swipeDirection(50,80)).toBe(0)
    expect(swipeDirection(-80,10)).toBe(1)
    expect(swipeDirection(80,10)).toBe(-1)
  })
})


it('upgrades older cached server galleries before slow catalog hydration completes',()=>{
  const slides=cardMediaSources({...products[0]!,image:'/backend/api/catalog/media/2/card?v=old',gallery:['/backend/api/catalog/media/2/detail?v=old','/backend/api/catalog/media/1/detail?v=old','/placeholder']})
  expect(slides).toHaveLength(2)
  expect(slides[0]?.thumb).toBe('/backend/api/catalog/media/2/thumb?v=20261005-card-gallery-3')
  expect(slides[1]?.detail).toBe('/backend/api/catalog/media/1/detail?v=20261005-card-gallery-3')
})
