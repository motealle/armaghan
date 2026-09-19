export type Locale = 'fa' | 'ar' | 'en' | 'ku'

const ARAB_COUNTRIES = new Set([
  'AE','SA','QA','KW','OM','BH','EG','JO','LB','SY','IQ','YE','PS','SD','LY','TN','DZ','MA','MR','SO','DJ','KM',
])
const IRAQ_KURDISH_REGIONS = ['Erbil','Arbil','Sulaymaniyah','Sulaimaniyah','Duhok','Dohuk','Halabja']
const IRAN_KURDISH_REGIONS = ['Kurdistan','Kordestan','Kermanshah','Ilam']

function includesRegion(region: string, candidates: string[]): boolean {
  const normalized=region.trim().toLowerCase()
  return candidates.some((candidate)=>normalized.includes(candidate.toLowerCase()))
}

export function browserLocale(): Locale {
  const langs=[...(navigator.languages ?? []),navigator.language].filter(Boolean).map((x)=>x.toLowerCase())
  if(langs.some((x)=>x.startsWith('ckb')||x.startsWith('ku')))return 'ku'
  if(langs.some((x)=>x.startsWith('ar')))return 'ar'
  if(langs.some((x)=>x.startsWith('fa')))return 'fa'
  return 'en'
}

export function geoLocale(input:{countryCode?:string;region?:string}):Locale {
  const country=(input.countryCode??'').toUpperCase()
  const region=input.region??''
  const browser=browserLocale()

  if(country==='IQ'&&includesRegion(region,IRAQ_KURDISH_REGIONS))return 'ku'
  if(country==='IR'&&includesRegion(region,IRAN_KURDISH_REGIONS))return 'ku'
  // West Azerbaijan is linguistically mixed; only select Kurdish there when
  // the user's own browser locale also indicates Kurdish.
  if(country==='IR'&&/west azerbaijan|azarbayjan-e gharbi/i.test(region)&&browser==='ku')return 'ku'
  if(country==='IR')return 'fa'
  if(ARAB_COUNTRIES.has(country))return 'ar'
  if(country)return 'en'
  return browser
}

export async function detectInitialLocale(timeoutMs=2400):Promise<Locale>{
  const fallback=browserLocale()
  const controller=new AbortController()
  const timer=window.setTimeout(()=>controller.abort(),timeoutMs)
  try{
    const response=await fetch('https://ipwho.is/',{signal:controller.signal,cache:'no-store'})
    if(!response.ok)return fallback
    const data=await response.json() as {success?:boolean;country_code?:string;region?:string}
    if(data.success===false)return fallback
    return geoLocale({countryCode:data.country_code,region:data.region})
  }catch{
    return fallback
  }finally{
    window.clearTimeout(timer)
  }
}
