import type { Locale } from '@/services/localeDetection'
import type { VisualStyleProfile } from '@/features/visual-editor/store'
export const clips=[{id:'fa',label:'فارسی',file:'/vids/farsi.mp4'},{id:'ar',label:'العربية',file:'/vids/arabic.mp4'},{id:'ku',label:'کوردی',file:'/vids/kordi.mp4'}] as const
export const capabilityDetails=Object.entries({production:7,export:8,trade:8}).flatMap(([group,count])=>Array.from({length:count},(_,i)=>({id:`home.capability.${group}.detail.${i+1}`,key:`capability${group[0]!.toUpperCase()+group.slice(1)}Detail${i+1}`,group,index:i+1,defaultHidden:group==='production'?[1,4].includes(i+1):group==='export'?[3,4,5,6].includes(i+1):i+1!==2})))
export const isShown=(profile:VisualStyleProfile,id:string,defaultHidden=false)=>!(profile.styles[id]?.hidden??defaultHidden)
export function aparatEmbed(value:string){
 try{const u=new URL(value);if(u.protocol!=='https:'||!['aparat.com','www.aparat.com'].includes(u.hostname)||u.port||u.username||u.password)return null
 const id=u.pathname.match(/^\/(?:_\/)?v\/([a-zA-Z0-9]+)\/?$/)?.[1]??u.pathname.match(/^\/video\/video\/embed\/(?:videohash\/([a-zA-Z0-9]+)\/vt\/frame|vt\/frame\/showvideo\/yes\/videohash\/([a-zA-Z0-9]+))\/?$/)?.slice(1).find(Boolean)
 return id?'https://www.aparat.com/video/video/embed/videohash/'+id+'/vt/frame':null
 }catch{return null}
}
export function localVideo(value:string){return /^\/vids\/(?:[A-Za-z0-9_-]+\/)*[A-Za-z0-9_.-]+\.mp4$/.test(value)&&!value.includes('..')?value:null}
export const videoFile=(profile:VisualStyleProfile,id:Locale,fallback:string)=>localVideo(profile.texts['home.videos.file']?.[id]??'')??fallback
export const videoEmbed=(profile:VisualStyleProfile,id:Locale)=>isShown(profile,`home.videos.${id}.aparat`,true)?aparatEmbed(profile.texts['home.videos.aparat']?.[id]??''):null
export function showcaseWords(locale:Locale){return {
 fa:{title:'ارمغان را از نزدیک ببینید',edit:'ویرایش ویدئو و جزئیات',play:'پخش ویدئو',previous:'ویدئوی قبلی',next:'ویدئوی بعدی',error:'پخش ویدئو ممکن نشد. دوباره تلاش کنید یا فایل را مستقیم باز کنید.',direct:'تماشای مستقیم',local:'پخش از سایت',official:'مدارک رسمی شرکت'},
 ar:{title:'تعرّف على أرمغان عن قرب',edit:'تعديل الفيديو والتفاصيل',play:'تشغيل الفيديو',previous:'الفيديو السابق',next:'الفيديو التالي',error:'تعذر تشغيل الفيديو. حاول مجدداً أو افتح الملف مباشرة.',direct:'مشاهدة مباشرة',local:'التشغيل من الموقع',official:'الوثائق الرسمية للشركة'},
 en:{title:'Meet Armaghan',edit:'Edit videos and details',play:'Play video',previous:'Previous video',next:'Next video',error:'Video could not play. Retry or open the file directly.',direct:'Watch directly',local:'Play from this site',official:'Official company documents'},
 ku:{title:'ئەرمەغان لە نزیکەوە ببینە',edit:'دەستکاری ڤیدیۆ و وردەکاری',play:'پەخشکردنی ڤیدیۆ',previous:'ڤیدیۆی پێشوو',next:'ڤیدیۆی دواتر',error:'ڤیدیۆ پەخش نەکرا. دووبارە هەوڵ بدە یان ڕاستەوخۆ بیکەرەوە.',direct:'بینینی ڕاستەوخۆ',local:'پەخش لە ماڵپەڕ',official:'بەڵگەنامە فەرمییەکانی کۆمپانیا'},
 }[locale]}
