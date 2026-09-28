import { describe, expect, it } from 'vitest'
import {
  FAVORITES_SHARE_MAX_ITEMS,
  buildFavoritesShareUrl,
  decodeFavoriteCodes,
  encodeFavoriteCodes,
} from './shareFavorites'

describe('Favorites sharing',()=>{
  it('encodes only unique five-digit product codes',()=>{
    expect(encodeFavoriteCodes(['11001','11001','bad','22003'])).toBe('v1:11001,22003')
  })

  it('rejects unknown versions and malformed codes',()=>{
    expect(decodeFavoriteCodes('v2:11001')).toEqual([])
    expect(decodeFavoriteCodes('v1:11001,nope,32003')).toEqual(['11001','32003'])
  })

  it('caps payload item count',()=>{
    const values=Array.from({length:FAVORITES_SHARE_MAX_ITEMS+20},(_,index)=>String(10000+index))
    expect(decodeFavoriteCodes(encodeFavoriteCodes(values))).toHaveLength(FAVORITES_SHARE_MAX_ITEMS)
  })

  it('builds a hash-router URL without personal metadata',()=>{
    const url=buildFavoritesShareUrl(['11001','22003'],'https://example.test/t/26/index.html')
    expect(url).toBe('https://example.test/t/26/index.html#/favorites?shared=v1%3A11001%2C22003')
    expect(url).not.toContain('name=')
    expect(url).not.toContain('email=')
  })
})
