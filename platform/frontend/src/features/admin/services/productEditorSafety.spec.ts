import { describe, expect, it, vi, afterEach } from 'vitest'
import { ref } from 'vue'
import { CustomerSessionApiError, requestJson } from '@/features/auth/services/customerSessionApi'
import { productFailure, useProductDraftGuard } from './productEditorSafety'
afterEach(()=>vi.restoreAllMocks())
describe('product form preservation',()=>{
  it('protects edited existing and newly entered products until explicit discard',()=>{
    for(const code of ['11001','']){
      const draft=ref({code,specifications:[{definition_id:12,value_text:'original'}]}),busy=ref(false)
      const guard=useProductDraftGuard(draft,busy),close=vi.fn()
      guard.checkpoint();expect(guard.dirty.value).toBe(false);draft.value.code='changed'
      guard.requestClose(close);expect(close).not.toHaveBeenCalled();expect(guard.confirmClose.value).toBe(true)
      guard.confirmClose.value=false;expect(draft.value.code).toBe('changed');expect(guard.dirty.value).toBe(true)
      guard.requestClose(close);guard.discard(close);expect(close).toHaveBeenCalledOnce()
    }
  })
  it('detects specification edits and clears dirty state only at a confirmed checkpoint',()=>{
    const draft=ref({code:'11001',specifications:[{value_text:'before'}]}),guard=useProductDraftGuard(draft,ref(false)),close=vi.fn()
    guard.checkpoint();draft.value.specifications[0]!.value_text='after';expect(guard.dirty.value).toBe(true)
    guard.requestClose(close);expect(close).not.toHaveBeenCalled();guard.checkpoint();guard.requestClose(close);expect(close).toHaveBeenCalledOnce()
  })
  it('cannot discard or close during a request',()=>{
    const draft=ref({code:''}),busy=ref(true),guard=useProductDraftGuard(draft,busy),close=vi.fn()
    guard.checkpoint();draft.value.code='new';guard.requestClose(close);guard.discard(close)
    expect(close).not.toHaveBeenCalled();expect(guard.dirty.value).toBe(true)
  })
})
describe('safe product errors',()=>{
  it('blocks blind repetition after ambiguous writes and stale revisions',()=>{
    for(const error of [new TypeError('network'),new CustomerSessionApiError(500,'unknown'),new CustomerSessionApiError(409,'stale')])expect(productFailure(error).block).toBe(true)
    expect(productFailure(new TypeError('network')).key).toBe('adminProductUncertain')
  })
  it('maps validation, media size and rate limits without displaying raw messages',()=>{
    for(const [field,key] of [['code','adminProductCodeInvalid'],['subcategory_id','adminProductCategoryInvalid'],['specifications.0.value_text','adminProductSpecsInvalid'],['image','adminImageLimits'],['name_fa','adminProductFieldsInvalid']])expect(productFailure(new CustomerSessionApiError(422,'SECRET HTML',[field!]))).toEqual({key,block:false})
    expect(productFailure(new CustomerSessionApiError(413,'large')).key).toBe('adminImageLimits')
    expect(productFailure(new CustomerSessionApiError(429,'rate')).key).toBe('adminProductRateLimit')
  })
  it('retains valid validation identifiers only',async()=>{
    const fetch=vi.spyOn(globalThis,'fetch').mockResolvedValue(new Response(JSON.stringify({message:'invalid',errors:{code:['private'],image:['untrusted'],'<script>':['unsafe'],'specifications.0.value_text':['bad']}}),{status:422}))
    await expect(requestJson('/api/admin/products')).rejects.toMatchObject({status:422,fields:['code','image','specifications.0.value_text']});expect(fetch).toHaveBeenCalledOnce()
  })
  it('does not automatically repeat an upload with a lost response',async()=>{
    const fetch=vi.spyOn(globalThis,'fetch').mockImplementation(async(url)=>{if(String(url).endsWith('/api/csrf-token'))return new Response(JSON.stringify({token:'csrf'}),{status:200});throw new TypeError('response lost')})
    const body=new FormData();body.append('image',new File(['bytes'],'image.jpg',{type:'image/jpeg'}))
    await expect(requestJson('/api/admin/products/1/images',{method:'POST',body})).rejects.toThrow('response lost')
    expect(fetch.mock.calls.filter(([url])=>String(url).endsWith('/images'))).toHaveLength(1)
  })
})
