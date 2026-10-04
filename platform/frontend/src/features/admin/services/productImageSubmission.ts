// Explicit photo submission: persist first, then upload once using the returned canonical ID/revision.
// Callers checkpoint each confirmed stage so an image failure never recreates a product.
export async function submitProductImage<T>(options:{
  current:T|null; dirty:boolean; file:File|null;
  persist:()=>Promise<T>; persisted:(product:T)=>void;
  upload:(product:T,file:File)=>Promise<T>; uploaded:(product:T)=>void;
}){
  let product=options.current
  if(!product||options.dirty){
    product=await options.persist()
    options.persisted(product)
  }
  if(options.file){
    product=await options.upload(product,options.file)
    options.uploaded(product)
  }
  return product
}
export function validProductImage(file:File){
  return ['image/jpeg','image/png','image/webp'].includes(file.type)&&file.size>0&&file.size<=8*1024*1024
}
