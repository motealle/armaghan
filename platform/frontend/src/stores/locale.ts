import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { detectInitialLocale, type Locale } from '@/services/localeDetection'
import {
  baseMessages,
  contentTranslationGroups,
  categorySubtitleTranslations,
  categoryTranslations,
  productNameTranslations,
  orderStatusTranslations,
  specTranslations,
  subcategoryTranslations,
  translationGroups,
  type TranslationMap,
} from '@/i18n/messages'

const MANUAL_KEY='armaghan:locale:manual'
const OVERRIDE_KEY='armaghan:test25:translations'

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

  function baseValue(key:string,target:Locale=locale.value):string{
    const direct=baseMessages[target]?.[key]
    if(direct!==undefined)return direct
    const category=key.match(/^category\.([^.]*)\.(name|subtitle)$/)
    if(category){
      const [,code,field]=category
      return field==='name'?(categoryTranslations[code]?.[target]??''):(categorySubtitleTranslations[code]?.[target]??'')
    }
    const subcategory=key.match(/^subcategory\.(.+)$/)
    if(subcategory)return subcategoryTranslations[subcategory[1]]?.[target]??''
    const product=key.match(/^product\.(.+)$/)
    if(product)return productNameTranslations[product[1]]?.[target]??''
    const spec=key.match(/^spec\.(.+)$/)
    if(spec)return specTranslations[spec[1]]?.[target]??''
    const status=key.match(/^status\.(.+)$/)
    if(status)return orderStatusTranslations[status[1]]?.[target]??''
    return baseMessages.fa[key]??key
  }

  function t(key:string,target:Locale=locale.value):string{
    const custom=overrides.value[target]?.[key]?.trim()
    return custom||baseValue(key,target)
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

  function categoryName(code:string,fallback=''){return t(`category.${code}.name`)||fallback}
  function categorySubtitle(code:string,fallback=''){return t(`category.${code}.subtitle`)||fallback}
  function subcategoryName(code:string,fallback=''){return t(`subcategory.${code}`)||fallback}
  function productNameFor(code:string,target:Locale,fallback='',custom?:Partial<Record<Locale,string>>){
    return custom?.[target]?.trim() || t(`product.${code}`,target) || fallback
  }
  function productName(code:string,fallback='',custom?:Partial<Record<Locale,string>>){
    return productNameFor(code,locale.value,fallback,custom)
  }
  function specLabel(label:string){return t(`spec.${label}`)||label}
  function orderStatus(label:string){return t(`status.${label}`)||label}

  return{
    locale,initialized,direction,htmlLang,overrides,
    t,baseValue,setManual,initialize,apply,
    setOverride,resetOverride,resetOverrides,
    categoryName,categorySubtitle,subcategoryName,productName,productNameFor,specLabel,orderStatus,
    baseMessages,translationGroups:[...translationGroups,...contentTranslationGroups],
  }
})
