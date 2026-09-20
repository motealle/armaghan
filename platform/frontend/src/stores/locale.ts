import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { detectInitialLocale, type Locale } from '@/services/localeDetection'
import {
  baseMessages,
  categorySubtitleTranslations,
  categoryTranslations,
  productNameTranslations,
  specTranslations,
  subcategoryTranslations,
  translationGroups,
  type TranslationMap,
} from '@/i18n/messages'

const MANUAL_KEY='armaghan:locale:manual'
const OVERRIDE_KEY='armaghan:test19:translations'

function readOverrides(): Record<Locale,TranslationMap> {
  try {
    const raw=localStorage.getItem(OVERRIDE_KEY)
    if(!raw)return {fa:{},ar:{},en:{},ku:{}}
    const parsed=JSON.parse(raw) as Partial<Record<Locale,TranslationMap>>
    return {fa:parsed.fa??{},ar:parsed.ar??{},en:parsed.en??{},ku:parsed.ku??{}}
  } catch {
    return {fa:{},ar:{},en:{},ku:{}}
  }
}

export const useLocaleStore=defineStore('locale',()=>{
  const locale=ref<Locale>('fa')
  const initialized=ref(false)
  const overrides=ref<Record<Locale,TranslationMap>>(readOverrides())
  const direction=computed<'ltr'|'rtl'>(()=>locale.value==='en'?'ltr':'rtl')
  const htmlLang=computed(()=>locale.value==='ku'?'ckb':locale.value)

  function apply(){
    document.documentElement.lang=htmlLang.value
    document.documentElement.dir=direction.value
    document.documentElement.dataset.locale=locale.value
  }

  function setManual(value:Locale){
    locale.value=value
    localStorage.setItem(MANUAL_KEY,value)
    apply()
  }

  async function initialize(){
    const manual=localStorage.getItem(MANUAL_KEY)
    if(manual==='fa'||manual==='ar'||manual==='en'||manual==='ku')locale.value=manual
    else locale.value=await detectInitialLocale()
    initialized.value=true
    apply()
  }

  function t(key:string, target:Locale=locale.value):string{
    const custom=overrides.value[target]?.[key]?.trim()
    return custom || baseMessages[target]?.[key] || baseMessages.fa[key] || key
  }

  function setOverride(target:Locale,key:string,value:string){
    overrides.value[target]={...overrides.value[target],[key]:value}
    localStorage.setItem(OVERRIDE_KEY,JSON.stringify(overrides.value))
  }

  function resetOverride(target:Locale,key:string){
    const next={...overrides.value[target]}
    delete next[key]
    overrides.value[target]=next
    localStorage.setItem(OVERRIDE_KEY,JSON.stringify(overrides.value))
  }

  function resetOverrides(target:Locale,keys:string[]){
    const next={...overrides.value[target]}
    keys.forEach(key=>delete next[key])
    overrides.value[target]=next
    localStorage.setItem(OVERRIDE_KEY,JSON.stringify(overrides.value))
  }

  function categoryName(code:string,fallback=''){return categoryTranslations[code]?.[locale.value] ?? fallback}
  function categorySubtitle(code:string,fallback=''){return categorySubtitleTranslations[code]?.[locale.value] ?? fallback}
  function subcategoryName(code:string,fallback=''){return subcategoryTranslations[code]?.[locale.value] ?? fallback}
  function productName(code:string,fallback=''){return productNameTranslations[code]?.[locale.value] ?? fallback}
  function specLabel(label:string){return specTranslations[label]?.[locale.value] ?? label}

  return{
    locale,initialized,direction,htmlLang,overrides,
    t,setManual,initialize,apply,
    setOverride,resetOverride,resetOverrides,
    categoryName,categorySubtitle,subcategoryName,productName,specLabel,
    baseMessages,translationGroups,
  }
})
