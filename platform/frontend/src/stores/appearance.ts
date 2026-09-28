import { defineStore } from 'pinia'
import { ref, watch } from 'vue'
import type {
  AppearanceProfiles,
  HeaderMode,
  HeroMode,
  HomeProductGridMode,
  ViewportAppearance,
  ViewportProfile,
} from '@/types/appearance'

export const APPEARANCE_KEY='armaghan:test26:appearance'

export const customerAppearanceDefaults:AppearanceProfiles={
  mobile:{
    headerMode:'compact-drawer',
    showHamburger:true,
    showBrandText:false,
    showLanguage:true,
    showHelp:true,
    showAccount:true,
    heroMode:'single',
    homeProductGrid:'hidden',
    showCategoryNumbers:false,
    showAbout:true,
    showWhy:true,
    showCapabilities:true,
    showProductBanners:true,
    showFooter:true,
  },
  tablet:{
    headerMode:'compact-drawer',
    showHamburger:true,
    showBrandText:false,
    showLanguage:true,
    showHelp:true,
    showAccount:true,
    heroMode:'single',
    homeProductGrid:'hidden',
    showCategoryNumbers:false,
    showAbout:true,
    showWhy:true,
    showCapabilities:true,
    showProductBanners:true,
    showFooter:true,
  },
  desktop:{
    headerMode:'expanded',
    showHamburger:false,
    showBrandText:false,
    showLanguage:true,
    showHelp:true,
    showAccount:true,
    heroMode:'single',
    homeProductGrid:'hidden',
    showCategoryNumbers:false,
    showAbout:true,
    showWhy:true,
    showCapabilities:true,
    showProductBanners:true,
    showFooter:true,
  },
}

function cloneProfile(value:ViewportAppearance):ViewportAppearance{return {...value}}
function cloneDefaults():AppearanceProfiles{
  return{
    mobile:cloneProfile(customerAppearanceDefaults.mobile),
    tablet:cloneProfile(customerAppearanceDefaults.tablet),
    desktop:cloneProfile(customerAppearanceDefaults.desktop),
  }
}
function isHeaderMode(value:unknown):value is HeaderMode{return value==='compact-drawer'||value==='expanded'}
function isHeroMode(value:unknown):value is HeroMode{return value==='single'||value==='carousel'}
function isGridMode(value:unknown):value is HomeProductGridMode{return value==='hidden'||value==='recommended-6'}
function booleanOr(value:unknown,fallback:boolean){return typeof value==='boolean'?value:fallback}

export function sanitizeViewportAppearance(value:unknown,fallback:ViewportAppearance):ViewportAppearance{
  const row=(value&&typeof value==='object'?value:{}) as Partial<ViewportAppearance>
  const result:ViewportAppearance={
    headerMode:isHeaderMode(row.headerMode)?row.headerMode:fallback.headerMode,
    showHamburger:booleanOr(row.showHamburger,fallback.showHamburger),
    showBrandText:booleanOr(row.showBrandText,fallback.showBrandText),
    showLanguage:booleanOr(row.showLanguage,fallback.showLanguage),
    showHelp:booleanOr(row.showHelp,fallback.showHelp),
    showAccount:booleanOr(row.showAccount,fallback.showAccount),
    heroMode:isHeroMode(row.heroMode)?row.heroMode:fallback.heroMode,
    homeProductGrid:isGridMode(row.homeProductGrid)?row.homeProductGrid:fallback.homeProductGrid,
    showCategoryNumbers:booleanOr(row.showCategoryNumbers,fallback.showCategoryNumbers),
    showAbout:booleanOr(row.showAbout,fallback.showAbout),
    showWhy:booleanOr(row.showWhy,fallback.showWhy),
    showCapabilities:booleanOr(row.showCapabilities,fallback.showCapabilities),
    showProductBanners:booleanOr(row.showProductBanners,fallback.showProductBanners),
    showFooter:booleanOr(row.showFooter,fallback.showFooter),
  }
  if(result.headerMode==='compact-drawer')result.showHamburger=true
  return result
}

export function sanitizeAppearanceProfiles(value:unknown):AppearanceProfiles{
  const source=(value&&typeof value==='object'?value:{}) as Partial<Record<ViewportProfile,unknown>>
  return{
    mobile:sanitizeViewportAppearance(source.mobile,customerAppearanceDefaults.mobile),
    tablet:sanitizeViewportAppearance(source.tablet,customerAppearanceDefaults.tablet),
    desktop:sanitizeViewportAppearance(source.desktop,customerAppearanceDefaults.desktop),
  }
}

function readStoredProfiles():AppearanceProfiles{
  if(typeof localStorage==='undefined')return cloneDefaults()
  try{
    const raw=localStorage.getItem(APPEARANCE_KEY)
    return raw?sanitizeAppearanceProfiles(JSON.parse(raw)):cloneDefaults()
  }catch{return cloneDefaults()}
}

export const useAppearanceStore=defineStore('appearance',()=>{
  const profiles=ref<AppearanceProfiles>(readStoredProfiles())

  function updateProfile(profile:ViewportProfile,patch:Partial<ViewportAppearance>){
    profiles.value={
      ...profiles.value,
      [profile]:sanitizeViewportAppearance({...profiles.value[profile],...patch},customerAppearanceDefaults[profile]),
    }
  }
  function resetProfile(profile:ViewportProfile){
    profiles.value={...profiles.value,[profile]:cloneProfile(customerAppearanceDefaults[profile])}
  }
  function resetAll(){profiles.value=cloneDefaults()}
  function resolved(profile:ViewportProfile):ViewportAppearance{return profiles.value[profile]}

  watch(profiles,(value)=>{
    if(typeof localStorage!=='undefined')localStorage.setItem(APPEARANCE_KEY,JSON.stringify(value))
  },{deep:true})

  return{profiles,updateProfile,resetProfile,resetAll,resolved}
})
