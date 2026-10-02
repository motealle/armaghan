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

export const APPEARANCE_KEY='armaghan:test28:appearance'
export const APPEARANCE_SCHEMA_KEY='armaghan:test28:appearance-schema'
export const APPEARANCE_SCHEMA_VERSION='5'

export const customerAppearanceDefaults:AppearanceProfiles={
  mobile:{
    headerMode:'compact-drawer',
    showHamburger:false,
    showBrandText:false,
    showLanguage:true,
    showHelp:true,
    showAccount:true,
    heroMode:'single',
    homeProductGrid:'hidden',
    showCategoryNumbers:false,
    showSubcategoryCodes:false,
    showAbout:true,
    showWhy:true,
    showCapabilities:true,
    showProductBanners:true,
    showFooter:true,
  },
  tablet:{
    headerMode:'expanded',
    showHamburger:false,
    showBrandText:false,
    showLanguage:true,
    showHelp:true,
    showAccount:true,
    heroMode:'single',
    homeProductGrid:'hidden',
    showCategoryNumbers:false,
    showSubcategoryCodes:false,
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
    showSubcategoryCodes:false,
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
    showSubcategoryCodes:booleanOr(row.showSubcategoryCodes,fallback.showSubcategoryCodes),
    showAbout:booleanOr(row.showAbout,fallback.showAbout),
    showWhy:booleanOr(row.showWhy,fallback.showWhy),
    showCapabilities:booleanOr(row.showCapabilities,fallback.showCapabilities),
    showProductBanners:booleanOr(row.showProductBanners,fallback.showProductBanners),
    showFooter:booleanOr(row.showFooter,fallback.showFooter),
  }
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

function migrateLegacyProfiles(value:unknown):unknown{
  const source=(value&&typeof value==='object'?value:{}) as Partial<Record<ViewportProfile,unknown>>
  const mobile=(source.mobile&&typeof source.mobile==='object'?source.mobile:{}) as Partial<ViewportAppearance>
  const tablet=(source.tablet&&typeof source.tablet==='object'?source.tablet:{}) as Partial<ViewportAppearance>
  const desktop=(source.desktop&&typeof source.desktop==='object'?source.desktop:{}) as Partial<ViewportAppearance>
  return{
    ...source,
    mobile:{...mobile,headerMode:'compact-drawer',showHamburger:false},
    tablet:{...tablet,headerMode:'expanded',showHamburger:false},
    desktop:{...desktop,headerMode:'expanded',showHamburger:false},
  }
}

function migrateSchema4Profiles(value:unknown):unknown{
  const source=(value&&typeof value==='object'?value:{}) as Partial<Record<ViewportProfile,unknown>>
  const migrate=(profile:ViewportProfile)=>{
    const row=(source[profile]&&typeof source[profile]==='object'?source[profile]:{}) as Partial<ViewportAppearance>
    return {...row,showSubcategoryCodes:false}
  }
  return{mobile:migrate('mobile'),tablet:migrate('tablet'),desktop:migrate('desktop')}
}

function readStoredProfiles():AppearanceProfiles{
  if(typeof localStorage==='undefined')return cloneDefaults()
  try{
    const raw=localStorage.getItem(APPEARANCE_KEY)
    const schema=localStorage.getItem(APPEARANCE_SCHEMA_KEY)
    if(!raw){
      localStorage.setItem(APPEARANCE_SCHEMA_KEY,APPEARANCE_SCHEMA_VERSION)
      return cloneDefaults()
    }
    const parsed=JSON.parse(raw)
    const migrated=schema===APPEARANCE_SCHEMA_VERSION?parsed:schema==='4'?migrateSchema4Profiles(parsed):migrateLegacyProfiles(parsed)
    const sanitized=sanitizeAppearanceProfiles(migrated)
    if(schema!==APPEARANCE_SCHEMA_VERSION){
      localStorage.setItem(APPEARANCE_KEY,JSON.stringify(sanitized))
      localStorage.setItem(APPEARANCE_SCHEMA_KEY,APPEARANCE_SCHEMA_VERSION)
    }
    return sanitized
  }catch{
    localStorage.setItem(APPEARANCE_SCHEMA_KEY,APPEARANCE_SCHEMA_VERSION)
    return cloneDefaults()
  }
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
