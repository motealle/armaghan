import { brandTokens, type BrandTokenId } from './tokenRegistry'

export const NORMAL_TEXT_MIN_CONTRAST=4.5

interface Rgb{
  r:number
  g:number
  b:number
}

function hexToRgb(hex:string):Rgb|null{
  const value=hex.trim().replace('#','')
  if(!/^[0-9a-f]{6}$/i.test(value))return null
  return{
    r:Number.parseInt(value.slice(0,2),16),
    g:Number.parseInt(value.slice(2,4),16),
    b:Number.parseInt(value.slice(4,6),16),
  }
}

function linearize(channel:number):number{
  const value=channel/255
  return value<=0.04045?value/12.92:((value+0.055)/1.055)**2.4
}

function luminance(rgb:Rgb):number{
  return 0.2126*linearize(rgb.r)+0.7152*linearize(rgb.g)+0.0722*linearize(rgb.b)
}

export function contrastRatio(foregroundHex:string,backgroundHex:string):number|null{
  const foreground=hexToRgb(foregroundHex)
  const background=hexToRgb(backgroundHex)
  if(!foreground||!background)return null
  const lighter=Math.max(luminance(foreground),luminance(background))
  const darker=Math.min(luminance(foreground),luminance(background))
  return (lighter+0.05)/(darker+0.05)
}

export function tokenHex(id:BrandTokenId):string{
  return brandTokens.find(token=>token.id===id)?.value??'#000000'
}

export function tokenContrastRatio(text:BrandTokenId,background:BrandTokenId):number{
  return contrastRatio(tokenHex(text),tokenHex(background))??1
}

export function passesNormalTextContrast(text:BrandTokenId,background:BrandTokenId):boolean{
  return tokenContrastRatio(text,background)>=NORMAL_TEXT_MIN_CONTRAST
}
