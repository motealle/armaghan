export const PRODUCT_IMAGE_SOURCE_MAX_BYTES=20*1024*1024
export const PRODUCT_IMAGE_UPLOAD_MAX_BYTES=8*1024*1024
const PRODUCT_IMAGE_TYPES=['image/jpeg','image/png','image/webp']
const PRODUCT_IMAGE_EXTENSIONS=['jpg','jpeg','png','webp','heic','heif','avif','gif','bmp']
const PRODUCT_CAMERA_IMAGE_TYPES=[...PRODUCT_IMAGE_TYPES,'image/heic','image/heif','image/heic-sequence','image/heif-sequence','image/avif','image/gif','image/bmp','image/x-ms-bmp']

function productImageExtension(file:File){return file.name.split('.').pop()?.toLowerCase()??''}
function supportedProductImageInput(file:File){
  // Picker metadata is advisory. Actual bytes and server validation decide acceptance.
  return PRODUCT_CAMERA_IMAGE_TYPES.includes(file.type.toLowerCase())||PRODUCT_IMAGE_EXTENSIONS.includes(productImageExtension(file))
}

export class ProductImagePreparationError extends Error{
  constructor(public readonly key='adminImagePreparationFailed'){super('Product image could not be prepared safely.');this.name='ProductImagePreparationError'}
}

export function validProductImage(file:File){
  return supportedProductImageInput(file)&&file.size>0&&file.size<=PRODUCT_IMAGE_SOURCE_MAX_BYTES
}

export function validPreparedProductImage(file:File){
  return PRODUCT_IMAGE_TYPES.includes(file.type.toLowerCase())&&file.size>0&&file.size<=PRODUCT_IMAGE_UPLOAD_MAX_BYTES
}

// Keep provider-backed files alive only until their bytes have been copied. A File
// wrapper around another File may retain its backing store; ArrayBuffer does not.
const retainedImages=new WeakSet<File>()
export async function retainProductImage(file:File):Promise<File>{
  if(retainedImages.has(file))return file
  if(!validProductImage(file))throw new ProductImagePreparationError('adminImageLimits')
  async function read(useReader:boolean):Promise<ArrayBuffer>{
    return new Promise((resolve,reject)=>{
      let reader:FileReader|undefined
      const timer=setTimeout(()=>{reader?.abort();reject(new Error('image read timeout'))},15000)
      const done=(bytes:ArrayBuffer)=>{clearTimeout(timer);bytes.byteLength===file.size?resolve(bytes):reject(new Error('incomplete image read'))}
      const failed=(error:unknown)=>{clearTimeout(timer);reject(error)}
      try{
        if(useReader){
          reader=new FileReader()
          reader.onload=()=>reader?.result instanceof ArrayBuffer?done(reader.result):failed(new Error('invalid image read'))
          reader.onerror=()=>failed(reader?.error);reader.onabort=()=>failed(new Error('image read aborted'))
          reader.readAsArrayBuffer(file)
        }else file.arrayBuffer().then(done,failed)
      }catch(error){failed(error)}
    })
  }
  let bytes:ArrayBuffer
  try{bytes=await read(false)}catch{
    try{bytes=await read(true)}catch{throw new ProductImagePreparationError('adminImageReadFailed')}
  }
  const retained=new File([bytes],file.name,{type:file.type,lastModified:file.lastModified})
  retainedImages.add(retained)
  return retained
}

async function canonicalProductImage(file:File){
  const bytes=new Uint8Array(await file.slice(0,16).arrayBuffer())
  const png=[137,80,78,71,13,10,26,10].every((value,index)=>bytes[index]===value)
  const jpeg=bytes[0]===255&&bytes[1]===216&&bytes[2]===255
  const webp=String.fromCharCode(...bytes.slice(0,4))==='RIFF'&&String.fromCharCode(...bytes.slice(8,12))==='WEBP'
  const brand=String.fromCharCode(...bytes.slice(4,12))
  const heif=brand.startsWith('ftyp')&&['heic','heix','hevc','hevx','mif1','msf1'].includes(brand.slice(4))
  const avif=brand.startsWith('ftyp')&&['avif','avis'].includes(brand.slice(4))
  const gif=['GIF87a','GIF89a'].includes(String.fromCharCode(...bytes.slice(0,6)))
  const bmp=bytes[0]===66&&bytes[1]===77
  const type=png?'image/png':jpeg?'image/jpeg':webp?'image/webp':avif?'image/avif':gif?'image/gif':bmp?'image/bmp':heif?'image/heic':null
  if(!type)throw new ProductImagePreparationError('adminImageContentInvalid')
  const extension=type.split('/')[1]!.replace('jpeg','jpg')
  const base=file.name.replace(/\.[^.]+$/,'')||'product'
  return new File([file],base+'.'+extension,{type,lastModified:file.lastModified})
}

type DecodedImage={source:CanvasImageSource;width:number;height:number;dispose:()=>void}
async function decodeProductImage(file:File):Promise<DecodedImage>{
  if(typeof createImageBitmap==='function'){
    try{
      const bitmap=await createImageBitmap(file)
      return {source:bitmap,width:bitmap.width,height:bitmap.height,dispose:()=>bitmap.close()}
    }catch{ /* Mobile browsers can decode images that createImageBitmap cannot. */ }
  }
  if(typeof Image==='undefined')throw new ProductImagePreparationError()
  const url=URL.createObjectURL(file),image=new Image()
  try{
    await new Promise<void>((resolve,reject)=>{
      const timer=setTimeout(()=>{image.onload=null;image.onerror=null;image.src='';reject(new ProductImagePreparationError())},15000)
      image.onload=()=>{clearTimeout(timer);resolve()}
      image.onerror=()=>{clearTimeout(timer);reject(new ProductImagePreparationError('adminImageContentInvalid'))}
      image.src=url
    })
    if(!image.naturalWidth||!image.naturalHeight)throw new ProductImagePreparationError('adminImageContentInvalid')
    return {source:image,width:image.naturalWidth,height:image.naturalHeight,dispose:()=>{image.onload=null;image.onerror=null;image.src=''}}
  }finally{URL.revokeObjectURL(url)}
}

export async function optimizeProductImage(file:File):Promise<File>{
  if(!validProductImage(file))throw new ProductImagePreparationError('adminImageLimits')
  file=await retainProductImage(file)
  const canonical=await canonicalProductImage(file)
  // The server still decodes and validates uploads. If a mobile browser's decoder
  // refuses valid JPEG/PNG/WebP bytes, send the canonical original when <= 8 MiB.
  // Noncanonical formats must be converted before the GD-only server sees them.
  let image:DecodedImage
  try{image=await decodeProductImage(canonical)}catch(error){
    if(validPreparedProductImage(canonical))return canonical
    if(canonical.type!=='image/heic')throw error
    try{
      const {decodeHeicToJpeg}=await import('./heicDecoder')
      image=await decodeProductImage(await decodeHeicToJpeg(canonical))
    }catch{throw new ProductImagePreparationError()}
  }
  try{
    if(image.width*image.height>64_000_000)throw new ProductImagePreparationError('adminImageLimits')
    const scale=Math.min(1,1920/Math.max(image.width,image.height))
    if(scale===1&&validPreparedProductImage(canonical)&&canonical.size<=1_250_000)return canonical
    if(typeof document==='undefined')throw new ProductImagePreparationError()
    const width=Math.max(1,Math.round(image.width*scale)),height=Math.max(1,Math.round(image.height*scale))
    const canvas=document.createElement('canvas');canvas.width=width;canvas.height=height
    const context=canvas.getContext('2d')
    if(!context)throw new ProductImagePreparationError()
    context.drawImage(image.source,0,0,width,height)
    const blob=await new Promise<Blob|null>((resolve,reject)=>{
      const timer=setTimeout(()=>reject(new ProductImagePreparationError()),15000)
      try{canvas.toBlob(value=>{clearTimeout(timer);resolve(value)},'image/webp',0.82)}catch(error){clearTimeout(timer);reject(error)}
    })
    if(!blob||!PRODUCT_IMAGE_TYPES.includes(blob.type)||!blob.size)throw new ProductImagePreparationError()
    const extension=blob.type==='image/jpeg'?'jpg':blob.type==='image/png'?'png':'webp'
    const prepared=new File([blob],canonical.name.replace(/\.[^.]+$/,'')+'.'+extension,{type:blob.type,lastModified:file.lastModified})
    // A resized image must not revert to an oversized original just because its bytes are smaller.
    const result=scale===1&&validPreparedProductImage(canonical)&&canonical.size<prepared.size?canonical:prepared
    if(!validPreparedProductImage(result))throw new ProductImagePreparationError('adminImageLimits')
    return result
  }catch(error){
    if(error instanceof ProductImagePreparationError)throw error
    throw new ProductImagePreparationError()
  }finally{image.dispose()}
}

export async function submitProductImages<T>(options:{
  current:T|null; dirty:boolean; files:File[];
  persist:()=>Promise<T>; persisted:(product:T)=>void;
  prepare?:(file:File)=>Promise<File>;
  upload:(product:T,file:File)=>Promise<T>;
  uploaded:(product:T,source:File,index:number)=>void;
}){
  let product=options.current
  if(!product||options.dirty){
    product=await options.persist()
    options.persisted(product)
  }
  for(let index=0;index<options.files.length;index+=1){
    const source=options.files[index]!
    const prepared=options.prepare?await options.prepare(source):source
    product=await options.upload(product,prepared)
    options.uploaded(product,source,index)
  }
  return product
}

// Backwards-compatible single-image wrapper for existing callers/tests.
export async function submitProductImage<T>(options:{
  current:T|null; dirty:boolean; file:File|null;
  persist:()=>Promise<T>; persisted:(product:T)=>void;
  upload:(product:T,file:File)=>Promise<T>; uploaded:(product:T)=>void;
}){
  return submitProductImages({
    current:options.current,dirty:options.dirty,files:options.file?[options.file]:[],
    persist:options.persist,persisted:options.persisted,upload:options.upload,
    uploaded:(product)=>options.uploaded(product),
  })
}
