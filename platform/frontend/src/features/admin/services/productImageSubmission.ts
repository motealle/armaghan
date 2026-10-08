export const PRODUCT_IMAGE_SOURCE_MAX_BYTES=20*1024*1024
export const PRODUCT_IMAGE_UPLOAD_MAX_BYTES=8*1024*1024
const PRODUCT_IMAGE_TYPES=['image/jpeg','image/png','image/webp']
const PRODUCT_IMAGE_EXTENSIONS=['jpg','jpeg','png','webp']

function productImageExtension(file:File){return file.name.split('.').pop()?.toLowerCase()??''}
function supportedProductImageInput(file:File){
  // Picker metadata is advisory. Actual bytes and server validation decide acceptance.
  return PRODUCT_IMAGE_TYPES.includes(file.type.toLowerCase())||PRODUCT_IMAGE_EXTENSIONS.includes(productImageExtension(file))
}

export class ProductImagePreparationError extends Error{
  constructor(public readonly key='adminImagePreparationFailed'){super('Product image could not be prepared safely.');this.name='ProductImagePreparationError'}
}

export function validProductImage(file:File){
  return supportedProductImageInput(file)&&file.size>0&&file.size<=PRODUCT_IMAGE_SOURCE_MAX_BYTES
}

export function validPreparedProductImage(file:File){
  return supportedProductImageInput(file)&&file.size>0&&file.size<=PRODUCT_IMAGE_UPLOAD_MAX_BYTES
}

async function canonicalProductImage(file:File){
  const bytes=new Uint8Array(await file.slice(0,12).arrayBuffer())
  const png=[137,80,78,71,13,10,26,10].every((value,index)=>bytes[index]===value)
  const jpeg=bytes[0]===255&&bytes[1]===216&&bytes[2]===255
  const webp=String.fromCharCode(...bytes.slice(0,4))==='RIFF'&&String.fromCharCode(...bytes.slice(8,12))==='WEBP'
  const type=png?'image/png':jpeg?'image/jpeg':webp?'image/webp':null
  if(!type)throw new ProductImagePreparationError('adminImageContentInvalid')
  const extension=png?'png':jpeg?'jpg':'webp'
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
  const canonical=await canonicalProductImage(file)
  const image=await decodeProductImage(canonical)
  try{
    const scale=Math.min(1,1920/Math.max(image.width,image.height))
    if(scale===1&&canonical.size<=1_250_000)return canonical
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
    const result=scale===1&&canonical.size<prepared.size?canonical:prepared
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
