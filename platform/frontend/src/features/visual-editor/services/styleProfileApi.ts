import type { VisualStyleProfile } from '../store'

export type StyleProfileChannel='staging'|'production'

export interface StyleProfileVersionSummary{
  id:number
  number:number
  schema:number
  source_test:string|null
  checksum:string
  created_at:string|null
}

export interface AdminStyleProfileResponse{
  profile:{id:number;slug:string;name:string;schema:number}
  draft:{
    styles:VisualStyleProfile['styles']
    texts:VisualStyleProfile['texts']
    css:string
    checksum:string
    updated_at:string|null
  }
  versions:StyleProfileVersionSummary[]
  publications:Record<string,{version:number;checksum:string;published_at:string|null}>
}

export interface PublicStyleProfileResponse{
  schema:number
  channel:StyleProfileChannel
  profile:null|{id:number;slug:string;name:string}
  version:null|{id:number;number:number;source_test:string|null;published_at:string|null}
  styles:VisualStyleProfile['styles']
  texts:VisualStyleProfile['texts']
  css:string
  checksum:string|null
}

export class StyleProfileApiError extends Error{
  constructor(
    public readonly status:number,
    message:string,
  ){
    super(message)
    this.name='StyleProfileApiError'
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

function isMutating(method:string):boolean{
  return !['GET','HEAD','OPTIONS'].includes(method.toUpperCase())
}

async function fetchCsrfToken(signal?:AbortSignal):Promise<string>{
  const response=await fetch(API_BASE+'/api/csrf-token',{
    method:'GET',
    headers:{Accept:'application/json'},
    credentials:'same-origin',
    signal,
  })
  if(!response.ok)throw new StyleProfileApiError(response.status,'CSRF token request failed.')
  const payload=await response.json() as {token?:unknown}
  if(typeof payload.token!=='string'||!payload.token)throw new StyleProfileApiError(500,'CSRF token is unavailable.')
  csrfToken=payload.token
  return payload.token
}

async function requestJson<T>(
  path:string,
  init:RequestInit={},
  signal?:AbortSignal,
  allowCsrfRetry=true,
):Promise<T>{
  const method=(init.method??'GET').toUpperCase()
  const headers=new Headers(init.headers)
  headers.set('Accept','application/json')
  if(init.body&&!headers.has('Content-Type'))headers.set('Content-Type','application/json')

  if(isMutating(method)){
    const xsrf=cookie('XSRF-TOKEN')
    if(xsrf)headers.set('X-XSRF-TOKEN',xsrf)
    if(!xsrf){
      const token=csrfToken??await fetchCsrfToken(signal)
      headers.set('X-CSRF-TOKEN',token)
    }
  }

  const response=await fetch(API_BASE+path,{
    ...init,
    method,
    headers,
    credentials:'same-origin',
    signal,
  })

  if(response.status===419&&isMutating(method)&&allowCsrfRetry){
    csrfToken=null
    await fetchCsrfToken(signal)
    return requestJson<T>(path,init,signal,false)
  }

  let payload:unknown=null
  try{payload=await response.json()}catch{}

  if(!response.ok){
    const message=(
      payload&&typeof payload==='object'&&'message' in payload&&typeof (payload as {message?:unknown}).message==='string'
    )?(payload as {message:string}).message:'Style Profile request failed ('+response.status+').'
    throw new StyleProfileApiError(response.status,message)
  }

  return payload as T
}

export function fetchPublicStyleProfile(
  channel:StyleProfileChannel='staging',
  signal?:AbortSignal,
){
  return requestJson<PublicStyleProfileResponse>('/api/style-profile/'+channel,{},signal)
}

export function fetchAdminStyleProfile(signal?:AbortSignal){
  return requestJson<AdminStyleProfileResponse>('/api/admin/style-profile',{},signal)
}

export function saveAdminStyleProfileDraft(
  profile:VisualStyleProfile,
  expectedChecksum:string|null,
  signal?:AbortSignal,
){
  return requestJson<Pick<AdminStyleProfileResponse,'profile'|'draft'>>(
    '/api/admin/style-profile/draft',
    {
      method:'PUT',
      body:JSON.stringify({
        name:'Test 27 Visual Style',
        source_test:'27',
        expected_checksum:expectedChecksum,
        styles:profile.styles,
        texts:profile.texts,
      }),
    },
    signal,
  )
}

export function publishAdminStyleProfile(
  channel:StyleProfileChannel='staging',
  signal?:AbortSignal,
){
  return requestJson<{version:StyleProfileVersionSummary;channel:StyleProfileChannel}>(
    '/api/admin/style-profile/publish/'+channel,
    {method:'POST',body:JSON.stringify({source_test:'27'})},
    signal,
  )
}

export function restoreAdminStyleProfileVersion(
  versionId:number,
  channel:StyleProfileChannel='staging',
  signal?:AbortSignal,
){
  return requestJson<{version:StyleProfileVersionSummary;channel:StyleProfileChannel;restored_from_version:number}>(
    '/api/admin/style-profile/versions/'+versionId+'/restore/'+channel,
    {method:'POST',body:JSON.stringify({source_test:'27'})},
    signal,
  )
}
