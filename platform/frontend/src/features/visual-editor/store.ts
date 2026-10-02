import { computed, ref, watch } from 'vue'
import { defineStore } from 'pinia'
import type { Locale } from '@/services/localeDetection'
import { isBrandTokenId, tokenVar, type BrandTokenId } from './tokenRegistry'
import { visualTargetDefinition } from './targetRegistry'

const PROFILE_KEY='armaghan:test29:visual-style-profile:v1'
const EDITOR_KEY='armaghan:test29:visual-editor-enabled'

export interface ElementStyleOverride{
  textColor?:BrandTokenId
  backgroundColor?:BrandTokenId
  borderColor?:BrandTokenId
  hidden?:boolean
}

export interface VisualStyleProfile{
  schema:1
  styles:Record<string,ElementStyleOverride>
  texts:Record<string,Partial<Record<Locale,string>>>
}

interface StoredVisualStyleProfile extends VisualStyleProfile{
  css?:string
}

export const emptyVisualStyleProfile=():VisualStyleProfile=>({schema:1,styles:{},texts:{}})

function validId(id:string):boolean{
  return /^[a-z0-9._-]+$/i.test(id)
}

function sanitizeStyle(id:string,value:unknown):ElementStyleOverride{
  const source=(value&&typeof value==='object'?value:{}) as Record<string,unknown>
  const definition=visualTargetDefinition(id)
  const allowed=definition?.styleControls??null
  const result:ElementStyleOverride={}
  if(isBrandTokenId(source.textColor)&&(allowed===null||allowed.includes('textColor')))result.textColor=source.textColor
  if(isBrandTokenId(source.backgroundColor)&&(allowed===null||allowed.includes('backgroundColor')))result.backgroundColor=source.backgroundColor
  if(isBrandTokenId(source.borderColor)&&(allowed===null||allowed.includes('borderColor')))result.borderColor=source.borderColor
  if(typeof source.hidden==='boolean'&&definition?.hideable!==false)result.hidden=source.hidden
  return result
}

export function sanitizeVisualStyleProfile(value:unknown):VisualStyleProfile{
  const parsed=(value&&typeof value==='object'?value:{}) as Partial<StoredVisualStyleProfile>
  const styles:Record<string,ElementStyleOverride>={}
  for(const [id,item] of Object.entries(parsed.styles??{})){
    if(!validId(id))continue
    const sanitized=sanitizeStyle(id,item)
    if(Object.keys(sanitized).length)styles[id]=sanitized
  }
  const texts:VisualStyleProfile['texts']={}
  for(const [id,item] of Object.entries(parsed.texts??{})){
    if(!validId(id)||!item||typeof item!=='object')continue
    const row:Partial<Record<Locale,string>>={}
    for(const locale of ['fa','ar','en','ku'] as Locale[]){
      const candidate=(item as Record<string,unknown>)[locale]
      if(typeof candidate==='string')row[locale]=candidate.slice(0,4000)
    }
    if(Object.keys(row).length)texts[id]=row
  }
  return{schema:1,styles,texts}
}

export function visualProfilesEqual(a:VisualStyleProfile,b:VisualStyleProfile):boolean{
  return JSON.stringify(a)===JSON.stringify(b)
}

function readProfile():VisualStyleProfile{
  if(typeof localStorage==='undefined')return emptyVisualStyleProfile()
  try{
    const raw=localStorage.getItem(PROFILE_KEY)
    if(!raw)return emptyVisualStyleProfile()
    return sanitizeVisualStyleProfile(JSON.parse(raw))
  }catch{
    return emptyVisualStyleProfile()
  }
}

export const defaultHiddenTargetIds:string[]=[
  'home.about.eyebrow','home.why.eyebrow','home.capabilities.eyebrow','home.product-banners.eyebrow',
]
export function targetIsHidden(profile:VisualStyleProfile,id:string):boolean{
  return profile.styles[id]?.hidden??defaultHiddenTargetIds.includes(id)
}

function compileCss(profile:VisualStyleProfile):string{
  const blocks:string[]=[]
  for(const [id,style] of Object.entries(profile.styles)){
    if(!validId(id))continue
    const declarations:string[]=[]
    if(style.textColor)declarations.push(`color:${tokenVar(style.textColor)}!important`)
    if(style.backgroundColor)declarations.push(`background:${tokenVar(style.backgroundColor)}!important`)
    if(style.borderColor)declarations.push(`border-color:${tokenVar(style.borderColor)}!important`)
    if(style.hidden===true)declarations.push('display:none!important')
    if(style.hidden===false)declarations.push('display:revert!important')
    if(declarations.length)blocks.push(`[data-style-id="${id}"]{${declarations.join(';')}}`)
  }
  return blocks.join('\n')
}

export const useVisualStyleStore=defineStore('visual-style',()=>{
  const enabled=ref(typeof sessionStorage!=='undefined'&&sessionStorage.getItem(EDITOR_KEY)==='1')
  const profile=ref<VisualStyleProfile>(readProfile())
  const compiledCss=computed(()=>compileCss(profile.value))
  const hasOverrides=computed(()=>Object.keys(profile.value.styles).length>0||Object.keys(profile.value.texts).length>0)

  function setEnabled(value:boolean){
    enabled.value=value
  }

  function replaceProfile(value:unknown){
    profile.value=sanitizeVisualStyleProfile(value)
  }

  function patchStyle(id:string,patch:Partial<ElementStyleOverride>){
    if(!validId(id))return
    const current=profile.value.styles[id]??{}
    const next=sanitizeStyle(id,{...current,...patch})
    const styles={...profile.value.styles}
    if(Object.keys(next).length)styles[id]=next
    else delete styles[id]
    profile.value={...profile.value,styles}
  }

  function clearStyleProperty(id:string,key:keyof ElementStyleOverride){
    const current={...(profile.value.styles[id]??{})}
    delete current[key]
    const styles={...profile.value.styles}
    if(Object.keys(current).length)styles[id]=current
    else delete styles[id]
    profile.value={...profile.value,styles}
  }

  function setText(id:string,locale:Locale,value:string){
    if(!validId(id))return
    const nextTexts={...profile.value.texts}
    const row={...(nextTexts[id]??{})}
    if(value.trim())row[locale]=value.slice(0,4000)
    else delete row[locale]
    if(Object.keys(row).length)nextTexts[id]=row
    else delete nextTexts[id]
    profile.value={...profile.value,texts:nextTexts}
  }

  function textOverride(id:string,locale:Locale):string|undefined{
    return profile.value.texts[id]?.[locale]
  }

  function resolveText(id:string,locale:Locale,fallback:string):string{
    return textOverride(id,locale)??fallback
  }

  function resetElement(id:string){
    const styles={...profile.value.styles}
    const texts={...profile.value.texts}
    delete styles[id]
    delete texts[id]
    profile.value={...profile.value,styles,texts}
  }

  function resetAll(){
    profile.value=emptyVisualStyleProfile()
  }

  watch(enabled,(value)=>{
    if(typeof sessionStorage==='undefined')return
    try{sessionStorage.setItem(EDITOR_KEY,value?'1':'0')}catch{}
  })

  watch(profile,(value)=>{
    if(typeof localStorage==='undefined')return
    const payload:StoredVisualStyleProfile={...value,css:compileCss(value)}
    try{localStorage.setItem(PROFILE_KEY,JSON.stringify(payload))}catch{}
  },{deep:true})

  return{
    enabled,profile,compiledCss,hasOverrides,
    setEnabled,replaceProfile,patchStyle,clearStyleProperty,
    setText,textOverride,resolveText,resetElement,resetAll,
  }
})
