import type { ViewportProfile } from '@/types/appearance'

export const TABLET_MIN_REM=48
export const DESKTOP_MIN_REM=64
export const TABLET_QUERY='(min-width: 48rem)'
export const DESKTOP_QUERY='(min-width: 64rem)'

export function profileForWidth(width:number):ViewportProfile{
  if(width>=1024)return 'desktop'
  if(width>=768)return 'tablet'
  return 'mobile'
}

export function currentViewportProfile():ViewportProfile{
  if(typeof window==='undefined')return 'desktop'
  const desktop=window.matchMedia(DESKTOP_QUERY)
  if(desktop.matches)return 'desktop'
  const tablet=window.matchMedia(TABLET_QUERY)
  return tablet.matches?'tablet':'mobile'
}

export function subscribeViewportProfile(listener:(profile:ViewportProfile)=>void):()=>void{
  if(typeof window==='undefined')return()=>{}
  const tablet=window.matchMedia(TABLET_QUERY)
  const desktop=window.matchMedia(DESKTOP_QUERY)
  const emit=()=>listener(desktop.matches?'desktop':tablet.matches?'tablet':'mobile')
  tablet.addEventListener?.('change',emit)
  desktop.addEventListener?.('change',emit)
  emit()
  return()=>{
    tablet.removeEventListener?.('change',emit)
    desktop.removeEventListener?.('change',emit)
  }
}
