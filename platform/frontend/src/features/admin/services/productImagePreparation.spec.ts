import { afterEach, describe, expect, it, vi } from 'vitest'
import { optimizeProductImage, validProductImage } from './productImageSubmission'
const png=Uint8Array.from(Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR4nGP4z8DwHwAFAAH/iZk9HQAAAABJRU5ErkJggg==','base64'))
afterEach(()=>vi.unstubAllGlobals())
function canvas(blob:Blob){const drawImage=vi.fn();vi.stubGlobal('document',{createElement:()=>({getContext:()=>({drawImage}),toBlob:(done:(value:Blob)=>void)=>done(blob)})});return drawImage}
describe('mobile product image preparation',()=>{
 it('accepts mobile PNG MIME aliases and canonicalizes small files from their bytes',async()=>{
  const source=new File([png],'۳۸۶_hgs.png',{type:'image/x-png'})
  expect(validProductImage(source)).toBe(true)
  vi.stubGlobal('createImageBitmap',vi.fn(async()=>({width:400,height:600,close:vi.fn()})))
  vi.stubGlobal('document',{})
  const result=await optimizeProductImage(source)
  expect(result.name).toBe(source.name);expect(result.type).toBe('image/png')
 })
 it('repairs a wrong extension without discarding a valid small PNG',async()=>{
  vi.stubGlobal('createImageBitmap',vi.fn(async()=>({width:400,height:600,close:vi.fn()})))
  vi.stubGlobal('document',{})
  const result=await optimizeProductImage(new File([png],'photo.jpg',{type:'image/jpeg'}))
  expect(result.name).toBe('photo.png');expect(result.type).toBe('image/png')
 })
 it('uses the mobile image decoder when bitmap decoding fails and resizes even if output is larger',async()=>{
  vi.stubGlobal('createImageBitmap',vi.fn(async()=>{throw new Error('unsupported')}))
  const draw=canvas(new Blob([new Uint8Array(100)],{type:'image/webp'}))
  const revoke=vi.fn();vi.spyOn(URL,'createObjectURL').mockReturnValue('blob:test');vi.spyOn(URL,'revokeObjectURL').mockImplementation(revoke)
  vi.stubGlobal('Image',class {naturalWidth=6000;naturalHeight=8000;onload:(()=>void)|null=null;onerror:(()=>void)|null=null;set src(value:string){if(value)queueMicrotask(()=>this.onload?.())}})
  const result=await optimizeProductImage(new File([png],'large.png',{type:'image/png'}))
  expect(result.type).toBe('image/webp');expect(result.size).toBe(100)
  expect(draw.mock.calls[0]?.slice(1)).toEqual([0,0,1440,1920]);expect(revoke).toHaveBeenCalledWith('blob:test')
 })
 it('supports browsers without bitmap decoding and a PNG-only canvas encoder',async()=>{
  vi.stubGlobal('createImageBitmap',undefined)
  canvas(new Blob([png],{type:'image/png'}))
  vi.spyOn(URL,'createObjectURL').mockReturnValue('blob:fallback');const revoke=vi.spyOn(URL,'revokeObjectURL').mockImplementation(()=>{})
  vi.stubGlobal('Image',class {naturalWidth=6000;naturalHeight=8000;onload:(()=>void)|null=null;onerror:(()=>void)|null=null;set src(value:string){if(value)queueMicrotask(()=>this.onload?.())}})
  const result=await optimizeProductImage(new File([png],'camera.png',{type:''}))
  expect(result.type).toBe('image/png');expect(result.name).toBe('camera.png');expect(revoke).toHaveBeenCalledWith('blob:fallback')
 })
 it('reports an undecodable image accurately and releases its temporary URL',async()=>{
  vi.stubGlobal('createImageBitmap',undefined)
  vi.spyOn(URL,'createObjectURL').mockReturnValue('blob:invalid');const revoke=vi.spyOn(URL,'revokeObjectURL').mockImplementation(()=>{})
  vi.stubGlobal('Image',class {onload:(()=>void)|null=null;onerror:(()=>void)|null=null;set src(value:string){if(value)queueMicrotask(()=>this.onerror?.())}})
  await expect(optimizeProductImage(new File([png],'bad.png',{type:'image/png'}))).rejects.toMatchObject({key:'adminImageContentInvalid'})
  expect(revoke).toHaveBeenCalledWith('blob:invalid')
 })
 it('rejects disguised non-image bytes before decoding or uploading',async()=>{
  const bitmap=vi.fn();vi.stubGlobal('createImageBitmap',bitmap)
  await expect(optimizeProductImage(new File(['<svg/>'],'photo.png',{type:'image/png'}))).rejects.toMatchObject({key:'adminImageContentInvalid'})
  expect(bitmap).not.toHaveBeenCalled()
 })
})
