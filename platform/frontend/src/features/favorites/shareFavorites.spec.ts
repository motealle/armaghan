import { beforeEach, describe, expect, it, vi } from 'vitest'

function jsonResponse(status:number,payload:unknown){
  return {
    ok:status>=200&&status<300,
    status,
    json:async()=>payload,
  } as Response
}

describe('persisted FavoriteShare API',()=>{
  beforeEach(()=>{
    vi.resetModules()
    vi.restoreAllMocks()
  })

  it('issues an ordered server-backed share through same-origin POST',async()=>{
    const fetchMock=vi.fn()
      .mockResolvedValueOnce(jsonResponse(200,{token:'csrf-token'}))
      .mockResolvedValueOnce(jsonResponse(200,{
        share:{
          id:7,
          url:'https://example.test/t/27/#/favorites/share/'+'A'.repeat(22),
          expires_at:'2026-10-09T00:00:00Z',
          owned:false,
        },
      }))
    vi.stubGlobal('fetch',fetchMock)

    const {issueFavoriteShare}=await import('./shareFavorites')
    const share=await issueFavoriteShare(['22003','11001'])

    expect(share.id).toBe(7)
    expect(share.url).toBe('https://example.test/#/s/'+'A'.repeat(22))
    expect(share.url).not.toContain('shared=v1:')
    expect(fetchMock).toHaveBeenNthCalledWith(2,
      '/backend/api/favorite-shares',
      expect.objectContaining({
        method:'POST',
        credentials:'same-origin',
      }),
    )
    expect(JSON.parse(String((fetchMock.mock.calls[1][1] as RequestInit).body))).toEqual({
      product_codes:['22003','11001'],
    })
  })

  it('resolves a short share token through a fixed POST body, not a tokenized backend path',async()=>{
    const token='B'.repeat(22)
    const fetchMock=vi.fn()
      .mockResolvedValueOnce(jsonResponse(200,{token:'csrf-token'}))
      .mockResolvedValueOnce(jsonResponse(200,{
        share:{
          id:9,
          expires_at:'2026-10-09T00:00:00Z',
          product_codes:['11002','11001'],
        },
      }))
    vi.stubGlobal('fetch',fetchMock)

    const {resolveFavoriteShare}=await import('./shareFavorites')
    const share=await resolveFavoriteShare(token)

    expect(share.product_codes).toEqual(['11002','11001'])
    expect(fetchMock).toHaveBeenNthCalledWith(2,
      '/backend/api/favorite-shares/resolve',
      expect.objectContaining({method:'POST'}),
    )
    expect(JSON.parse(String((fetchMock.mock.calls[1][1] as RequestInit).body))).toEqual({token})
  })

  it('keeps legacy 64-character share tokens resolvable',async()=>{
    const token='L'.repeat(64)
    const fetchMock=vi.fn()
      .mockResolvedValueOnce(jsonResponse(200,{token:'csrf-token'}))
      .mockResolvedValueOnce(jsonResponse(200,{
        share:{id:10,expires_at:'2026-10-09T00:00:00Z',product_codes:['11001']},
      }))
    vi.stubGlobal('fetch',fetchMock)

    const {resolveFavoriteShare}=await import('./shareFavorites')
    const share=await resolveFavoriteShare(token)

    expect(share.product_codes).toEqual(['11001'])
    expect(JSON.parse(String((fetchMock.mock.calls[1][1] as RequestInit).body))).toEqual({token})
  })

  it('rejects malformed share tokens before any network request',async()=>{
    const fetchMock=vi.fn()
    vi.stubGlobal('fetch',fetchMock)

    const {resolveFavoriteShare}=await import('./shareFavorites')

    await expect(resolveFavoriteShare('short')).rejects.toMatchObject({status:410})
    expect(fetchMock).not.toHaveBeenCalled()
  })

  it('revokes an owned share through the customer endpoint',async()=>{
    const fetchMock=vi.fn()
      .mockResolvedValueOnce(jsonResponse(200,{token:'csrf-token'}))
      .mockResolvedValueOnce(jsonResponse(200,{ok:true}))
    vi.stubGlobal('fetch',fetchMock)

    const {revokeFavoriteShare}=await import('./shareFavorites')
    await revokeFavoriteShare(11)

    expect(fetchMock).toHaveBeenNthCalledWith(2,
      '/backend/api/customer/favorite-shares/11',
      expect.objectContaining({method:'DELETE'}),
    )
  })
})
