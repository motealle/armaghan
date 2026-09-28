export const FAVORITES_SHARE_PREFIX='v1:'
export const FAVORITES_SHARE_MAX_ITEMS=80
export const FAVORITES_SHARE_MAX_URL=1800

function validCode(value:string){return /^\d{5}$/.test(value)}

export function encodeFavoriteCodes(codes:string[]):string{
  const unique=[...new Set(codes.filter(validCode))].slice(0,FAVORITES_SHARE_MAX_ITEMS)
  return FAVORITES_SHARE_PREFIX+unique.join(',')
}

export function decodeFavoriteCodes(value:unknown):string[]{
  if(typeof value!=='string'||!value.startsWith(FAVORITES_SHARE_PREFIX))return[]
  return [...new Set(value.slice(FAVORITES_SHARE_PREFIX.length).split(',').filter(validCode))].slice(0,FAVORITES_SHARE_MAX_ITEMS)
}

export function buildFavoritesShareUrl(codes:string[],baseDocumentUrl:string):string|null{
  const token=encodeFavoriteCodes(codes)
  if(token===FAVORITES_SHARE_PREFIX)return null
  const url=`${baseDocumentUrl}#/favorites?shared=${encodeURIComponent(token)}`
  return url.length<=FAVORITES_SHARE_MAX_URL?url:null
}
