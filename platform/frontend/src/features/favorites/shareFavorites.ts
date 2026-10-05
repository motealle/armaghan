import { requestJson as authenticatedRequest, CustomerSessionApiError } from '@/features/auth/services/customerSessionApi'
export interface IssuedFavoriteShare{
  id:number
  url:string
  expires_at:string|null
  owned:boolean
}

export interface ResolvedFavoriteShare{
  id:number
  expires_at:string|null
  product_codes:string[]
}

export class FavoriteShareApiError extends Error{
  constructor(public readonly status:number,message:string){
    super(message)
    this.name='FavoriteShareApiError'
  }
}

const SHARE_TOKEN_PATTERN=/^(?:[A-Za-z0-9]{22}|[A-Za-z0-9]{64})$/
const SHARE_HASH_PATTERN=/^#\/(?:s|favorites\/share)\/((?:[A-Za-z0-9]{22}|[A-Za-z0-9]{64}))$/

async function requestJson<T>(path:string,init:RequestInit={}):Promise<T>{
  try{return await authenticatedRequest<T>(path,init)}
  catch(error){
    if(error instanceof CustomerSessionApiError)throw new FavoriteShareApiError(error.status,error.message)
    throw error
  }
}

export async function issueFavoriteShare(productCodes:string[]):Promise<IssuedFavoriteShare>{
  const response=await requestJson<{share:IssuedFavoriteShare}>('/api/favorite-shares',{
    method:'POST',
    body:JSON.stringify({product_codes:productCodes}),
  })
  // Keep links at the active root even during rollout of stale host configuration.
  const url=new URL(response.share.url)
  const match=SHARE_HASH_PATTERN.exec(url.hash)
  if(!match||typeof window!=='undefined'&&url.origin!==window.location.origin)throw new FavoriteShareApiError(502,'Invalid share URL.')
  return {...response.share,url:new URL('/#/s/'+match[1],url.origin).href}
}

export async function resolveFavoriteShare(token:string):Promise<ResolvedFavoriteShare>{
  if(!SHARE_TOKEN_PATTERN.test(token))throw new FavoriteShareApiError(410,'Favorite share is unavailable.')
  const response=await requestJson<{share:ResolvedFavoriteShare}>('/api/favorite-shares/resolve',{
    method:'POST',
    body:JSON.stringify({token}),
  })
  return response.share
}

export async function revokeFavoriteShare(id:number):Promise<void>{
  await requestJson<{ok:true}>('/api/customer/favorite-shares/'+id,{
    method:'DELETE',
  })
}
