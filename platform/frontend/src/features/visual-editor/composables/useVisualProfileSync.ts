import { computed, onUnmounted, ref, watch, type Ref } from 'vue'
import {
  fetchAdminStyleProfile,
  publishAdminStyleProfile,
  restoreAdminStyleProfileVersion,
  saveAdminStyleProfileDraft,
  StyleProfileApiError,
  type AdminStyleProfileResponse,
  type StyleProfileVersionSummary,
} from '../services/styleProfileApi'
import {
  sanitizeVisualStyleProfile,
  visualProfilesEqual,
  type VisualStyleProfile,
} from '../store'

export type VisualSyncState='checking'|'local'|'saving'|'synced'|'conflict'|'error'

interface VisualStoreLike{
  profile:VisualStyleProfile
  replaceProfile:(value:unknown)=>void
}

export function useVisualProfileSync(
  visual:VisualStoreLike,
  enabled:Ref<boolean>,
){
  const state=ref<VisualSyncState>('checking')
  const authenticated=ref(false)
  const message=ref('در حال بررسی اتصال…')
  const checksum=ref<string|null>(null)
  const remoteDraft=ref<VisualStyleProfile|null>(null)
  const versions=ref<StyleProfileVersionSummary[]>([])
  const publications=ref<AdminStyleProfileResponse['publications']>({})
  const initialized=ref(false)
  let saveTimer:number|undefined
  let saveController:AbortController|undefined
  let suppressNextWatch=false

  const canPublish=computed(()=>authenticated.value&&state.value!=='saving'&&state.value!=='conflict')
  const statusLabel=computed(()=>{
    switch(state.value){
      case 'checking':return 'بررسی اتصال'
      case 'saving':return 'در حال ذخیره'
      case 'synced':return 'همگام با سرور'
      case 'conflict':return 'نیاز به انتخاب نسخه'
      case 'error':return 'خطای همگام‌سازی'
      default:return 'ذخیره محلی'
    }
  })

  function setLocal(reason='تغییرات روی همین مرورگر محفوظ است.'){
    authenticated.value=false
    state.value='local'
    message.value=reason
  }

  function applyAdminSnapshot(data:AdminStyleProfileResponse,allowAdopt:boolean){
    authenticated.value=true
    checksum.value=data.draft.checksum
    remoteDraft.value=sanitizeVisualStyleProfile({
      schema:1,
      styles:data.draft.styles,
      texts:data.draft.texts,
    })
    versions.value=data.versions
    publications.value=data.publications

    const local=sanitizeVisualStyleProfile(visual.profile)
    const remote=remoteDraft.value
    const localEmpty=Object.keys(local.styles).length===0&&Object.keys(local.texts).length===0
    const remoteEmpty=Object.keys(remote.styles).length===0&&Object.keys(remote.texts).length===0

    if(visualProfilesEqual(local,remote)){
      state.value='synced'
      message.value='نسخه این مرورگر با پیش‌نویس سرور یکسان است.'
      return
    }

    if(allowAdopt&&localEmpty&&!remoteEmpty){
      suppressNextWatch=true
      visual.replaceProfile(remote)
      state.value='synced'
      message.value='پیش‌نویس سرور روی این مرورگر بارگذاری شد.'
      return
    }

    if(remoteEmpty&&!localEmpty){
      state.value='local'
      message.value='سرور آماده است؛ تغییرات محلی در اولین ذخیره به پیش‌نویس منتقل می‌شود.'
      queueSave()
      return
    }

    if(localEmpty&&remoteEmpty){
      state.value='synced'
      message.value='پروفایل خالی محلی و سرور همگام‌اند.'
      return
    }

    state.value='conflict'
    message.value='نسخه این دستگاه با پیش‌نویس سرور فرق دارد؛ یکی را صریحاً انتخاب کنید.'
  }

  async function connect(allowAdopt=true){
    state.value='checking'
    message.value='در حال بررسی نشست ادمین Laravel…'
    const controller=new AbortController()
    try{
      const data=await fetchAdminStyleProfile(controller.signal)
      applyAdminSnapshot(data,allowAdopt)
    }catch(error){
      if(error instanceof StyleProfileApiError&&(error.status===401||error.status===403)){
        setLocal('بک‌اند در دسترس است، اما این ورود آزمایشی هنوز نشست واقعی Laravel نیست؛ ذخیره محلی ادامه دارد.')
      }else{
        setLocal('بک‌اند فعلاً در این نسخه در دسترس نیست؛ ذخیره محلی بدون وقفه ادامه دارد.')
      }
    }finally{
      initialized.value=true
    }
  }

  async function saveNow(force=false){
    if(!authenticated.value||(state.value==='conflict'&&!force))return false
    saveController?.abort()
    const controller=new AbortController()
    saveController=controller
    state.value='saving'
    message.value='در حال ذخیره پیش‌نویس روی Laravel/SQLite…'
    try{
      const result=await saveAdminStyleProfileDraft(
        sanitizeVisualStyleProfile(visual.profile),
        checksum.value,
        controller.signal,
      )
      checksum.value=result.draft.checksum
      remoteDraft.value=sanitizeVisualStyleProfile({
        schema:1,
        styles:result.draft.styles,
        texts:result.draft.texts,
      })
      state.value='synced'
      message.value='پیش‌نویس روی سرور ذخیره شد.'
      return true
    }catch(error){
      if(error instanceof DOMException&&error.name==='AbortError')return false
      if(error instanceof StyleProfileApiError&&error.status===409){
        state.value='conflict'
        message.value='هم‌زمان نسخه جدیدتری روی سرور ذخیره شده است؛ نسخه را انتخاب کنید.'
        try{
          const latest=await fetchAdminStyleProfile()
          checksum.value=latest.draft.checksum
          remoteDraft.value=sanitizeVisualStyleProfile({schema:1,styles:latest.draft.styles,texts:latest.draft.texts})
          versions.value=latest.versions
          publications.value=latest.publications
        }catch{}
        return false
      }
      if(error instanceof StyleProfileApiError&&(error.status===401||error.status===403)){
        setLocal('نشست واقعی ادمین Laravel فعال نیست؛ تغییرات محلی محفوظ ماند.')
        return false
      }
      state.value='error'
      message.value='ذخیره سرور ناموفق بود؛ نسخه محلی از بین نرفته است.'
      return false
    }
  }

  function queueSave(){
    if(!initialized.value||!authenticated.value||state.value==='conflict')return
    if(saveTimer!==undefined)window.clearTimeout(saveTimer)
    saveTimer=window.setTimeout(()=>{void saveNow()},900)
  }

  async function useServerVersion(){
    if(!remoteDraft.value)return
    suppressNextWatch=true
    visual.replaceProfile(remoteDraft.value)
    state.value='synced'
    message.value='نسخه سرور روی این دستگاه بارگذاری شد.'
  }

  async function keepLocalVersion(){
    if(!authenticated.value)return
    state.value='saving'
    message.value='در حال جایگزینی پیش‌نویس سرور با نسخه این دستگاه…'
    await saveNow(true)
  }

  async function publishStaging(){
    if(!canPublish.value)return
    const saved=await saveNow()
    if(!saved&&state.value!=='synced')return
    try{
      await publishAdminStyleProfile('staging')
      const latest=await fetchAdminStyleProfile()
      applyAdminSnapshot(latest,false)
      message.value='نسخه فعلی در کانال staging منتشر شد.'
    }catch{
      state.value='error'
      message.value='انتشار staging انجام نشد؛ پیش‌نویس محفوظ است.'
    }
  }

  async function restoreVersion(versionId:number){
    if(!authenticated.value)return
    try{
      state.value='saving'
      message.value='در حال بازگردانی نسخه…'
      await restoreAdminStyleProfileVersion(versionId,'staging')
      const latest=await fetchAdminStyleProfile()
      remoteDraft.value=sanitizeVisualStyleProfile({schema:1,styles:latest.draft.styles,texts:latest.draft.texts})
      suppressNextWatch=true
      visual.replaceProfile(remoteDraft.value)
      checksum.value=latest.draft.checksum
      versions.value=latest.versions
      publications.value=latest.publications
      state.value='synced'
      message.value='نسخه انتخاب‌شده به‌صورت نسخه جدید بازگردانی و در staging منتشر شد.'
    }catch{
      state.value='error'
      message.value='بازگردانی نسخه انجام نشد؛ نسخه محلی محفوظ است.'
    }
  }

  watch(
    ()=>visual.profile,
    ()=>{
      if(suppressNextWatch){
        suppressNextWatch=false
        return
      }
      queueSave()
    },
    {deep:true},
  )

  watch(enabled,(value)=>{
    if(value&&!initialized.value)void connect(true)
  },{immediate:true})

  onUnmounted(()=>{
    if(saveTimer!==undefined)window.clearTimeout(saveTimer)
    saveController?.abort()
  })

  return{
    state,authenticated,message,checksum,versions,publications,
    canPublish,statusLabel,connect,saveNow,publishStaging,
    restoreVersion,useServerVersion,keepLocalVersion,
  }
}
