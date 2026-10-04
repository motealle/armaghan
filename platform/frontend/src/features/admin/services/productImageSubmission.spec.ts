import { describe, expect, it, vi } from 'vitest'
import { ref } from 'vue'
import { CustomerSessionApiError } from '@/features/auth/services/customerSessionApi'
import { PRODUCT_IMAGE_SOURCE_MAX_BYTES, ProductImagePreparationError, submitProductImage, submitProductImages, validPreparedProductImage, validProductImage } from './productImageSubmission'
import { useProductDraftGuard } from './productEditorSafety'
const file=new File(['photo'],'product.jpg',{type:'image/jpeg'})

describe('staged product image submission',()=>{
  it('creates first and uploads using the returned ID and revision',async()=>{
    const events:string[]=[],product={id:42,revision:'created'},updated={...product,revision:'uploaded'}
    const result=await submitProductImage({current:null,dirty:false,file,
      persist:async()=>{events.push('create');return product},persisted:()=>events.push('checkpoint'),
      upload:async(row,image)=>{expect(row).toBe(product);expect(image).toBe(file);events.push('upload');return updated},uploaded:()=>events.push('uploaded')})
    expect(events).toEqual(['create','checkpoint','upload','uploaded']);expect(result).toBe(updated)
  })

  it('uploads several selected images sequentially using each fresh revision',async()=>{
    const files=[file,new File(['photo2'],'second.png',{type:'image/png'})]
    const seen:string[]=[]
    const result=await submitProductImages({
      current:{id:7,revision:'r0'},dirty:false,files,persist:vi.fn(),persisted:vi.fn(),
      prepare:async source=>source,
      upload:async(row,source)=>{seen.push(row.revision+':'+source.name);return {...row,revision:row.revision+'x'}},
      uploaded:vi.fn(),
    })
    expect(seen).toEqual(['r0:product.jpg','r0x:second.png'])
    expect(result.revision).toBe('r0xx')
  })

  it('does not upload when product validation fails',async()=>{
    const upload=vi.fn(),persisted=vi.fn()
    await expect(submitProductImage({current:null,dirty:true,file,persist:async()=>{throw new Error('code invalid')},persisted,upload,uploaded:vi.fn()})).rejects.toThrow('code invalid')
    expect(upload).not.toHaveBeenCalled();expect(persisted).not.toHaveBeenCalled()
  })

  it('retains created product and remaining files after an image failure',async()=>{
    const files=[file,new File(['second'],'second.jpg',{type:'image/jpeg'})]
    let current:{id:number;revision:string}|null=null
    const persist=vi.fn(async()=>({id:42,revision:'created'}))
    const upload=vi.fn(async(row:{id:number;revision:string},source:File)=>{
      if(source.name==='second.jpg')throw new CustomerSessionApiError(422,'image invalid',['image'])
      return {...row,revision:'uploaded'}
    })
    const uploaded:string[]=[]
    await expect(submitProductImages({current,dirty:false,files,persist,
      persisted:row=>{current=row},upload,uploaded:(row,source)=>{current=row;uploaded.push(source.name)}})).rejects.toThrow('image invalid')
    expect(persist).toHaveBeenCalledOnce()
    expect(uploaded).toEqual(['product.jpg'])
    expect(current).toEqual({id:42,revision:'uploaded'})
  })

  it('updates dirty products before upload and leaves clean existing products untouched',async()=>{
    for(const dirty of [true,false]){
      const original={id:1,revision:'old'},fresh={id:1,revision:'fresh'},persist=vi.fn(async()=>fresh)
      const upload=vi.fn(async(row:typeof original)=>row)
      await submitProductImage({current:original,dirty,file,persist,persisted:vi.fn(),upload,uploaded:vi.fn()})
      expect(persist).toHaveBeenCalledTimes(dirty?1:0);expect(upload).toHaveBeenCalledWith(dirty?fresh:original,file)
    }
  })

  it('protects unsent image count after product fields are checkpointed',()=>{
    const pendingCount=ref(1),guard=useProductDraftGuard(ref({code:'11001'}),ref(false),pendingCount),close=vi.fn()
    guard.checkpoint();guard.requestClose(close);expect(close).not.toHaveBeenCalled();expect(guard.confirmClose.value).toBe(true)
    pendingCount.value=0;guard.requestClose(close);expect(close).toHaveBeenCalledOnce()
  })

  it('accepts large camera sources for client optimization but keeps server payload bounded',()=>{
    expect(validProductImage(file)).toBe(true)
    const camera=new File([new Uint8Array(9*1024*1024)],'camera.jpg',{type:'image/jpeg'})
    expect(validProductImage(camera)).toBe(true)
    expect(validPreparedProductImage(camera)).toBe(false)
    for(const f of [
      new File([],'empty.jpg',{type:'image/jpeg'}),
      new File(['x'],'bad.svg',{type:'image/svg+xml'}),
      new File([new Uint8Array(PRODUCT_IMAGE_SOURCE_MAX_BYTES+1)],'huge.png',{type:'image/png'}),
    ])expect(validProductImage(f)).toBe(false)
  })

  it('preparation errors are distinguishable from uncertain network failures',()=>{
    expect(new ProductImagePreparationError()).toBeInstanceOf(Error)
  })
})
