import type { Product } from '@/types/domain'
import { freshProductMediaUrl } from './catalogApi'

export function cardMediaSources(product:Product){
  if(product.media?.length)return product.media.map(slide=>({...slide,thumb:freshProductMediaUrl(slide.thumb),card:freshProductMediaUrl(slide.card),detail:freshProductMediaUrl(slide.detail)}))
  // Upgrade older persisted server galleries immediately while the catalog refreshes.
  if(product.image?.includes('/backend/api/catalog/media/')){
    const bases=[product.image,...(product.gallery||[])].map(url=>url.match(/^(.*\/backend\/api\/catalog\/media\/\d+)\/(?:thumb|card|detail)(?:[?#].*)?$/)?.[1]).filter((base):base is string=>Boolean(base))
    return [...new Set(bases)].map(base=>({thumb:freshProductMediaUrl(base+'/thumb'),card:freshProductMediaUrl(base+'/card'),detail:freshProductMediaUrl(base+'/detail'),width:undefined,height:undefined}))
  }
  if(!product.image)return []
  // A staged local product has no lightweight server variants. Preserve its actual gallery.
  const urls=[product.image,...(product.gallery||[])].filter((url,i,all)=>Boolean(url)&&all.indexOf(url)===i)
  return urls.map(url=>({card:url,thumb:url,detail:url,width:undefined,height:undefined}))
}

export function swipeDirection(dx:number,dy:number){
  if(Math.abs(dx)<40||Math.abs(dx)<=Math.abs(dy)*1.3)return 0
  return dx<0?1:-1
}
