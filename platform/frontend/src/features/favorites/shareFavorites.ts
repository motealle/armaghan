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

const rawBase=(import.meta.env.VITE_ARMAGHAN_API_BASE as string|undefined)?.trim()??''
const API_BASE=(rawBase||'/backend').replace(/\/$/,'')
let csrfToken:string|null=null

function cookie(name:string):string|undefined{
  if(typeof document==='undefined')return undefined
  const item=document.cookie.split('; ').find(row=>row.startsWith(name+'='))
  return item?decodeURIComponent(item.slice(name.length+1)):undefined
}

async function fetchCsrfToken():Promise<string>{
  const response=await fetch(API_BASE+'/api/csrf-token',{
    headers:{Accept:'application/json'},
    credentials:'same-origin',
  })
  if(!response.ok)throw new FavoriteShareApiError(response.status,'CSRF token request failed.')
  const payload=await response.json() as {token?:unknown}
  if(typeof payload.token!=='string'||!payload.token)throw new FavoriteShareApiError(500,'CSRF token unavailable.')
  csrfToken=payload.token
  return payload.token
}

async function requestJson<T>(path:string,init:RequestInit={},allowCsrfRetry=true):Promise<T>{
  const method=(init.method??'GET').toUpperCase()
  const headers=new Headers(init.headers)
  headers.set('Accept','application/json')
  if(init.body&&!headers.has('Content-Type'))headers.set('Content-Type','application/json')

  if(!['GET','HEAD','OPTIONS'].includes(method)){
    const xsrf=cookie('XSRF-TOKEN')
    if(xsrf)headers.set('X-XSRF-TOKEN',xsrf)
    else headers.set('X-CSRF-TOKEN',csrfToken??await fetchCsrfToken())
  }

  const response=await fetch(API_BASE+path,{
    ...init,
    method,
    headers,
    credentials:'same-origin',
  })

  if(response.status===419&&!['GET','HEAD','OPTIONS'].includes(method)&&allowCsrfRetry){
    csrfToken=null
    await fetchCsrfToken()
    return requestJson<T>(path,init,false)
  }

  let payload:unknown=null
  try{payload=await response.json()}catch{}

  if(!response.ok){
    const message=(
      payload&&typeof payload==='object'&&'message' in payload&&typeof (payload as {message?:unknown}).message==='string'
    )?(payload as {message:string}).message:'Favorite share request failed ('+response.status+').'
    throw new FavoriteShareApiError(response.status,message)
  }

  return payload as T
}

export async function issueFavoriteShare(productCodes:string[]):Promise<IssuedFavoriteShare>{
  const response=await requestJson<{share:IssuedFavoriteShare}>('/api/favorite-shares',{
    method:'POST',
    body:JSON.stringify({product_codes:productCodes}),
  })
  // Keep links at the active root even during rollout of stale host configuration.
  const url=new URL(response.share.url)
  const match=/^#\/favorites\/share\/([A-Za-z0-9]{64})$/.exec(url.hash)
  if(!match||typeof window!=='undefined'&&url.origin!==window.location.origin)throw new FavoriteShareApiError(502,'Invalid share URL.')
  return {...response.share,url:new URL('/#/favorites/share/'+match[1],url.origin).href}
}

export async function resolveFavoriteShare(token:string):Promise<ResolvedFavoriteShare>{
  if(!/^[A-Za-z0-9]{64}$/.test(token))throw new FavoriteShareApiError(410,'Favorite share is unavailable.')
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
