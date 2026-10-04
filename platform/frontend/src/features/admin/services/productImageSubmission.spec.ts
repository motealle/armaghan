import { describe, expect, it, vi } from 'vitest'
import { ref } from 'vue'
import { CustomerSessionApiError } from '@/features/auth/services/customerSessionApi'
import { submitProductImage, validProductImage } from './productImageSubmission'
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
  it('does not upload when product validation fails',async()=>{
    const upload=vi.fn(),persisted=vi.fn()
    await expect(submitProductImage({current:null,dirty:true,file,persist:async()=>{throw new Error('code invalid')},persisted,upload,uploaded:vi.fn()})).rejects.toThrow('code invalid')
    expect(upload).not.toHaveBeenCalled();expect(persisted).not.toHaveBeenCalled()
  })
  it('retains the created product and file after an image failure; retry does not create twice',async()=>{
    let current:{id:number;revision:string}|null=null,pending:File|null=file
    const persist=vi.fn(async()=>({id:42,revision:'created'})),uploaded=vi.fn((row)=>{current=row;pending=null})
    const options=()=>({current,dirty:false,file:pending,persist,persisted:(row:{id:number;revision:string})=>{current=row},uploaded})
    const lost=vi.fn(async()=>{throw new CustomerSessionApiError(422,'image invalid',['image'])})
    await expect(submitProductImage({...options(),upload:lost})).rejects.toThrow('image invalid')
    expect(current).toEqual({id:42,revision:'created'});expect(pending).toBe(file);expect(lost).toHaveBeenCalledOnce();expect(uploaded).not.toHaveBeenCalled()
    // Only a confirmed retryable validation error is retried by the caller; ambiguous errors are blocked by productFailure.
    await submitProductImage({...options(),upload:async(row)=>({...row,revision:'uploaded'})})
    expect(persist).toHaveBeenCalledOnce();expect(pending).toBeNull()
  })
  it('updates dirty products before upload and leaves clean existing products untouched',async()=>{
    for(const dirty of [true,false]){
      const original={id:1,revision:'old'},fresh={id:1,revision:'fresh'},persist=vi.fn(async()=>fresh)
      const upload=vi.fn(async(row:typeof original)=>row)
      await submitProductImage({current:original,dirty,file,persist,persisted:vi.fn(),upload,uploaded:vi.fn()})
      expect(persist).toHaveBeenCalledTimes(dirty?1:0);expect(upload).toHaveBeenCalledWith(dirty?fresh:original,file)
    }
  })
  it('protects an unsent image even after product fields are checkpointed',()=>{
    const pending=ref<File|null>(file),guard=useProductDraftGuard(ref({code:'11001'}),ref(false),pending),close=vi.fn()
    guard.checkpoint();guard.requestClose(close);expect(close).not.toHaveBeenCalled();expect(guard.confirmClose.value).toBe(true)
    pending.value=null;guard.requestClose(close);expect(close).toHaveBeenCalledOnce()
  })
  it('rejects empty, unsupported and oversized files before product creation',()=>{
    expect(validProductImage(file)).toBe(true)
    for(const f of [new File([],'empty.jpg',{type:'image/jpeg'}),new File(['x'],'bad.svg',{type:'image/svg+xml'}),new File([new Uint8Array(8*1024*1024+1)],'huge.png',{type:'image/png'})])expect(validProductImage(f)).toBe(false)
  })
})
