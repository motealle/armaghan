export const PRODUCT_IMAGE_SOURCE_MAX_BYTES=20*1024*1024
export const PRODUCT_IMAGE_UPLOAD_MAX_BYTES=8*1024*1024
const PRODUCT_IMAGE_TYPES=['image/jpeg','image/png','image/webp']
const PRODUCT_IMAGE_EXTENSIONS=['jpg','jpeg','png','webp']

function productImageExtension(file:File){return file.name.split('.').pop()?.toLowerCase()??''}
function supportedProductImageInput(file:File){
  const type=file.type.toLowerCase()
  return PRODUCT_IMAGE_TYPES.includes(type)||type==='image/jpg'||((type===''||type==='application/octet-stream')&&PRODUCT_IMAGE_EXTENSIONS.includes(productImageExtension(file)))
}

export class ProductImagePreparationError extends Error{
  constructor(message='Product image could not be prepared safely.'){super(message);this.name='ProductImagePreparationError'}
}

export function validProductImage(file:File){
  return supportedProductImageInput(file)&&file.size>0&&file.size<=PRODUCT_IMAGE_SOURCE_MAX_BYTES
}

export function validPreparedProductImage(file:File){
  return supportedProductImageInput(file)&&file.size>0&&file.size<=PRODUCT_IMAGE_UPLOAD_MAX_BYTES
}

export async function optimizeProductImage(file:File):Promise<File>{
  if(!validProductImage(file))throw new ProductImagePreparationError()
  if(typeof createImageBitmap!=='function'||typeof document==='undefined') {
    if(!validPreparedProductImage(file))throw new ProductImagePreparationError()
    return file
  }

  let bitmap:ImageBitmap
  try{bitmap=await createImageBitmap(file)}catch{
    if(!validPreparedProductImage(file))throw new ProductImagePreparationError()
    return file
  }

  try{
    const maxEdge=1920
    const scale=Math.min(1,maxEdge/Math.max(bitmap.width,bitmap.height))
    const width=Math.max(1,Math.round(bitmap.width*scale))
    const height=Math.max(1,Math.round(bitmap.height*scale))
    const needsCompression=scale<1||file.size>1_250_000
    if(!needsCompression){
      if(!validPreparedProductImage(file))throw new ProductImagePreparationError()
      return file
    }
    const canvas=document.createElement('canvas')
    canvas.width=width;canvas.height=height
    const context=canvas.getContext('2d')
    if(!context){
      if(!validPreparedProductImage(file))throw new ProductImagePreparationError()
      return file
    }
    context.drawImage(bitmap,0,0,width,height)
    const blob=await new Promise<Blob|null>(resolve=>canvas.toBlob(resolve,'image/webp',0.82))
    if(!blob||blob.size===0){
      if(!validPreparedProductImage(file))throw new ProductImagePreparationError()
      return file
    }
    const base=file.name.replace(/\.[^.]+$/,'')||'product'
    const outputType=PRODUCT_IMAGE_TYPES.includes(blob.type)?blob.type:file.type
    const extension=outputType==='image/jpeg'?'jpg':outputType==='image/png'?'png':'webp'
    const prepared=new File([blob],base+'.'+extension,{type:outputType,lastModified:file.lastModified})
    const result=prepared.size<file.size?prepared:file
    if(!validPreparedProductImage(result))throw new ProductImagePreparationError()
    return result
  }finally{bitmap.close()}
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
