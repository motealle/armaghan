export async function compressImage(file: File, maxEdge = 1200, quality = 0.78): Promise<string> {
  const dataUrl = await new Promise<string>((resolve,reject)=>{
    const reader=new FileReader()
    reader.onerror=()=>reject(reader.error)
    reader.onload=()=>resolve(String(reader.result))
    reader.readAsDataURL(file)
  })
  const image = await new Promise<HTMLImageElement>((resolve,reject)=>{
    const img=new Image()
    img.onload=()=>resolve(img)
    img.onerror=()=>reject(new Error('Invalid image'))
    img.src=dataUrl
  })
  const scale=Math.min(1,maxEdge/Math.max(image.naturalWidth,image.naturalHeight))
  const canvas=document.createElement('canvas')
  canvas.width=Math.max(1,Math.round(image.naturalWidth*scale))
  canvas.height=Math.max(1,Math.round(image.naturalHeight*scale))
  const ctx=canvas.getContext('2d')
  if(!ctx)throw new Error('Canvas unavailable')
  ctx.drawImage(image,0,0,canvas.width,canvas.height)
  return canvas.toDataURL('image/webp',quality)
}
