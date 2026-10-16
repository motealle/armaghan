// Self-hosted and loaded only for a HEIC/HEIF that the native browser cannot read.
// heic-to's CSP build decodes in its worker; no photo leaves this browser.
export async function decodeHeicToJpeg(file:File):Promise<File>{
  let timer:ReturnType<typeof setTimeout>|undefined
  try{
    const conversion=(async()=>{
      const {heicTo}=await import('heic-to/csp')
      return heicTo({blob:file,type:'image/jpeg',quality:0.92})
    })()
    const blob=await Promise.race([conversion,new Promise<never>((_,reject)=>{
      timer=setTimeout(()=>reject(new Error('Image conversion timed out')),45000)
    })])
    if(!blob.size||blob.type!=='image/jpeg')throw new Error('Invalid conversion output')
    return new File([blob],file.name.replace(/\.[^.]+$/,'')+'.jpg',{type:blob.type,lastModified:file.lastModified})
  }finally{if(timer)clearTimeout(timer)}
}
