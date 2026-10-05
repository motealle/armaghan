import type { Product } from '@/types/domain'

export function cardMediaSources(product:Product){
  if(product.media?.length)return product.media
  if(!product.image)return []
  // A staged local product has no lightweight server variants. Preserve its actual gallery.
  const urls=[product.image,...(product.gallery||[])].filter((url,i,all)=>Boolean(url)&&all.indexOf(url)===i)
  return urls.map(url=>({card:url,thumb:url,detail:url,width:undefined,height:undefined}))
}

export function swipeDirection(dx:number,dy:number){
  if(Math.abs(dx)<40||Math.abs(dx)<=Math.abs(dy)*1.3)return 0
  return dx<0?1:-1
}
