export interface BackendCustomerSession{
  id:number
  company_name:string|null
  whatsapp:string|null
  country_code:string|null
  country_name:string|null
}

interface SessionResponse{
  customer:BackendCustomerSession
}

export class CustomerSessionApiError extends Error{
  constructor(public readonly status:number,message:string){
    super(message)
    this.name='CustomerSessionApiError'
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
  if(!response.ok)throw new CustomerSessionApiError(response.status,'CSRF token request failed.')
  const payload=await response.json() as {token?:unknown}
  if(typeof payload.token!=='string'||!payload.token)throw new CustomerSessionApiError(500,'CSRF token unavailable.')
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
    )?(payload as {message:string}).message:'Customer session request failed ('+response.status+').'
    throw new CustomerSessionApiError(response.status,message)
  }

  return payload as T
}

export async function consumeCustomerMagicLink(token:string):Promise<BackendCustomerSession>{
  const response=await requestJson<SessionResponse>('/api/customer/magic-link/consume',{
    method:'POST',
    body:JSON.stringify({token}),
  })
  return response.customer
}

export async function fetchCustomerSession():Promise<BackendCustomerSession|null>{
  try{
    const response=await requestJson<SessionResponse>('/api/customer/session')
    return response.customer
  }catch(error){
    if(error instanceof CustomerSessionApiError&&error.status===401)return null
    throw error
  }
}

export async function updateCustomerSession(
  patch:Partial<Pick<BackendCustomerSession,'company_name'|'whatsapp'|'country_code'|'country_name'>>,
):Promise<BackendCustomerSession>{
  const response=await requestJson<SessionResponse>('/api/customer/session',{
    method:'PATCH',
    body:JSON.stringify(patch),
  })
  return response.customer
}

export async function logoutCustomerSession():Promise<void>{
  await requestJson<{ok:true}>('/api/customer/logout',{method:'POST',body:'{}'})
}
