<script setup lang="ts">
import { reactive, ref, watch, nextTick } from 'vue'
import { RotateCcw, Save } from '@lucide/vue'
import {capabilityDetails, clips, isShown, videoFile, aparatEmbed, localVideo} from '@/features/home/services/showcase'
import {useHomeMediaStore} from '@/features/home-media/store'
import {useVisualStyleStore} from '@/features/visual-editor/store'
import {translationTarget} from '@/features/visual-editor/translationTarget'
import {useVisualProfileSync} from '@/features/visual-editor/composables/useVisualProfileSync'
import VisualEditorSyncPanel from '@/features/visual-editor/VisualEditorSyncPanel.vue'
import { useLocaleStore } from '@/stores/locale'
import type { Locale } from '@/services/localeDetection'

const props=defineProps<{live?:boolean}>()
const emit=defineEmits<{'image-request':[]}>()
async function editOfficial(){if(props.live){await saveAll();if(!saved.value)return}emit('image-request');await nextTick();media.requestEdit('document.official')}
const locale=useLocaleStore()
const visual=useVisualStyleStore()
const sync=useVisualProfileSync(visual,ref(!!props.live),undefined,false)
const targetAliases:Record<string,string>={...Object.fromEntries(capabilityDetails.map(item=>[item.key,item.id])),heroSingleSlogan:'hero.title',aboutArmaghanTitle:'home.about.title',aboutArmaghanText:'home.about.text',whyArmaghanTitle:'home.why.title',whyArmaghanIntro:'home.why.intro',whyCapacityTitle:'home.why.item.1.title',whyCapacityText:'home.why.item.1.text',whyCustomTitle:'home.why.item.2.title',whyCustomText:'home.why.item.2.text',whyDirectTitle:'home.why.item.3.title',whyDirectText:'home.why.item.3.text',whyMarketTitle:'home.why.item.4.title',whyMarketText:'home.why.item.4.text',capabilitiesTitle:'home.capabilities.title',capabilitiesIntro:'home.capabilities.intro',productBannersTitle:'home.product-banners.title',productBannersIntro:'home.product-banners.intro',capabilityProductionTitle:'home.capability.production.title',capabilityProductionSummary:'home.capability.production.text',capabilityExportTitle:'home.capability.export.title',capabilityExportSummary:'home.capability.export.text',capabilityTradeTitle:'home.capability.trade.title',capabilityTradeSummary:'home.capability.trade.text'}
function currentText(key:string){return props.live?(visual.textOverride(targetAliases[key]??translationTarget(key),target.value)??visual.textOverride(translationTarget(key),target.value)??locale.baseValue(key,target.value)):(locale.overrides[target.value]?.[key]??locale.baseValue(key,target.value))}
function override(key:string,value:string){if(props.live){visual.setText(translationTarget(key),target.value,value);if(targetAliases[key])visual.setText(targetAliases[key]!,target.value,value)}else locale.setOverride(target.value,key,value)}
function clearOverride(key:string){if(props.live){visual.setText(translationTarget(key),target.value,'');if(targetAliases[key])visual.setText(targetAliases[key]!,target.value,'')}else locale.resetOverride(target.value,key)}
const target=ref<Locale>('fa')
const saved=ref(false)
const fields=[
  ...capabilityDetails.map(item=>({key:item.key,rows:1})),
  {key:'manufacturer',rows:1},
  {key:'heroSingleSlogan',rows:2},
  {key:'aboutArmaghanTitle',rows:1},
  {key:'aboutArmaghanText',rows:5},
  {key:'whyArmaghanTitle',rows:1},
  {key:'whyArmaghanIntro',rows:2},
  {key:'whyCapacityTitle',rows:1},
  {key:'whyCapacityText',rows:3},
  {key:'whyCustomTitle',rows:1},
  {key:'whyCustomText',rows:3},
  {key:'whyDirectTitle',rows:1},
  {key:'whyDirectText',rows:3},
  {key:'whyMarketTitle',rows:1},
  {key:'whyMarketText',rows:3},
  {key:'capabilitiesTitle',rows:1},
  {key:'capabilitiesIntro',rows:3},
  {key:'capabilityProductionTitle',rows:1},
  {key:'capabilityProductionSummary',rows:3},
  {key:'capabilityExportTitle',rows:1},
  {key:'capabilityExportSummary',rows:3},
  {key:'capabilityTradeTitle',rows:1},
  {key:'capabilityTradeSummary',rows:3},
  {key:'productBannersTitle',rows:1},
  {key:'productBannersIntro',rows:2},
  {key:'brandIntro',rows:1},
  {key:'brandCapabilities',rows:1},
  {key:'brandCapabilitiesText',rows:3},
  {key:'brandDocuments',rows:1},
  {key:'brandDocumentsText',rows:3},
  {key:'brandSales',rows:1},
  {key:'brandSalesText',rows:3},
] as const
const drafts=reactive<Record<string,string>>({})
const visible=reactive<Record<string,boolean>>({}),videoEnabled=ref(true),validation=ref('')
const videoDrafts=reactive<Record<string,{file:string;aparat:string;useAparat:boolean}>>({})
const media=useHomeMediaStore()


function syncDrafts(){
  for(const item of fields)drafts[item.key]=currentText(item.key)
  for(const item of capabilityDetails)visible[item.id]=isShown(visual.profile,item.id,item.defaultHidden)
  videoEnabled.value=isShown(visual.profile,'home.videos')
  for(const clip of clips)videoDrafts[clip.id]={file:videoFile(visual.profile,clip.id,clip.file),aparat:visual.profile.texts['home.videos.aparat']?.[clip.id]??'',useAparat:isShown(visual.profile,`home.videos.${clip.id}.aparat`,true)}
  saved.value=false
}
watch(target,syncDrafts,{immediate:true})
async function saveAll(){
  validation.value=''
  if(props.live){
   for(const clip of clips){const item=videoDrafts[clip.id]!;if(!localVideo(item.file)||(item.aparat&&!aparatEmbed(item.aparat))||(item.useAparat&&!aparatEmbed(item.aparat))){validation.value='مسیر فایل باید داخل /vids و با پسوند mp4 باشد؛ برای آپارات، لینک معتبر همان ویدئو را درج کنید.';return}}
   for(const item of capabilityDetails)visual.patchStyle(item.id,{hidden:!visible[item.id]})
   visual.patchStyle('home.videos',{hidden:!videoEnabled.value})
   for(const clip of clips){const item=videoDrafts[clip.id]!;visual.setText('home.videos.file',clip.id,item.file);visual.setText('home.videos.aparat',clip.id,item.aparat);visual.patchStyle(`home.videos.${clip.id}.aparat`,{hidden:!item.useAparat})}
  }
  for(const item of fields)override(item.key,drafts[item.key]??'')
  saved.value=props.live?await sync.saveNow():true
  if(props.live&&saved.value){await sync.publishStaging();saved.value=sync.state.value==='synced'}
}
async function reset(key:string){
  clearOverride(key)
  drafts[key]=locale.baseValue(key,target.value)
  saved.value=false
  if(props.live)await sync.saveNow()
}
watch(()=>sync.state.value,(state)=>{if(props.live&&state==='synced')syncDrafts()})
</script>

<template>
  <section class="space-y-4">
    <VisualEditorSyncPanel v-if="live" :state="sync.state.value" :authenticated="sync.authenticated.value" :message="sync.message.value" :versions="sync.versions.value" :published-version="sync.publications.value[sync.channel.value]?.version" :channel="sync.channel.value" :can-publish="sync.canPublish.value" @reconnect="sync.connect(true)" @publish="sync.publishStaging" @restore="sync.restoreVersion" @use-server="sync.useServerVersion" @keep-local="sync.keepLocalVersion"/>
    <fieldset class="contents" :disabled="live&&(!sync.authenticated.value||['checking','saving','conflict'].includes(sync.state.value))">

    <div class="flex flex-wrap items-end gap-3">
      <div>
        <h2 class="text-xl font-black text-[var(--c-text)]">{{locale.t('brandIntro')}}</h2>
        <p class="mt-1 text-sm leading-6 text-[var(--c-muted)]">{{locale.t('translationsHelp')}}</p>
      </div>
      <label class="form-field ms-auto min-w-36">{{locale.t('language')}}
        <select v-model="target">
          <option value="fa">فارسی</option><option value="ar">العربية</option><option value="en">English</option><option value="ku">کوردی</option>
        </select>
      </label>
    </div>

    <section v-if="live" class="admin-surface space-y-3 rounded-2xl p-4">
     <h3 class="text-lg font-bold">ویدئوهای صفحه اول</h3><label class="flex min-h-12 items-center gap-2"><input v-model="videoEnabled" type="checkbox">نمایش بخش ویدئوها</label>
     <p class="text-sm leading-7">هر سه کلیپ در همه زبان‌های سایت قابل انتخاب‌اند. پخش از هاست پیش‌فرض است؛ اگر لینک آپارات دارید، گزینه آن کلیپ را روشن کنید.</p>
     <article v-for="clip in clips" :key="clip.id" class="space-y-2 rounded-xl border border-[var(--c-border)] p-3">
      <h4 class="font-bold">{{clip.label}}</h4>
      <template v-if="videoDrafts[clip.id]"><label class="form-field">مسیر فایل روی هاست<input v-model="videoDrafts[clip.id]!.file" dir="ltr" maxlength="1000"></label>
      <label class="form-field">لینک ویدئو در آپارات (اختیاری)<input v-model="videoDrafts[clip.id]!.aparat" :aria-label="'لینک آپارات · '+clip.label" type="url" dir="ltr" maxlength="1000" placeholder="https://www.aparat.com/v/…"></label>
      <label class="flex min-h-12 items-center gap-2"><input v-model="videoDrafts[clip.id]!.useAparat" :data-video-provider="clip.id" type="checkbox">پخش این کلیپ از آپارات</label></template>
     </article>
    </section>
    <section v-if="live" class="admin-surface space-y-2 rounded-2xl p-4"><h3 class="text-lg font-bold">نمایش جزئیات توانمندی‌ها</h3><p class="text-sm">هر مورد را مستقل روشن یا خاموش کنید؛ متن آن در بخش ویرایش متن پایین همین صفحه قابل تغییر است.</p><label v-for="item in capabilityDetails" :key="item.id" class="flex min-h-12 items-center gap-3 border-b border-[var(--c-border)] py-2"><input v-model="visible[item.id]" :data-showcase-toggle="item.id" type="checkbox"><span>{{locale.baseValue(item.key,target)}}</span></label>
    <button type="button" class="mini-action mt-2" @click="editOfficial">ذخیره و بارگذاری عکس مدارک رسمی</button><p class="text-xs">عکس این بخش برای همه بازدیدکنندگان قابل مشاهده خواهد بود.</p></section>
    <p v-if="validation" class="auth-error" role="alert">{{validation}}</p>
    <p v-if="saved" role="status">تغییرات ذخیره و منتشر شد.</p>
    <div class="grid gap-3 lg:grid-cols-2">
      <article v-for="item in fields" :key="item.key" class="admin-surface rounded-2xl p-4">
        <div class="mb-2 flex items-center gap-2">
          <b class="text-sm">{{locale.baseValue(item.key,target)}}</b>
        </div>
        <textarea :aria-label="locale.baseValue(item.key,target)" :data-field-key="item.key" v-model="drafts[item.key]" :rows="item.rows" class="w-full resize-y rounded-xl border border-[var(--c-border)] bg-[var(--c-surface-2)] px-3 py-2 text-sm outline-none"/>
        <button class="mini-action mt-2" type="button" @click="reset(item.key)"><RotateCcw :size="14"/>{{locale.t('resetTranslation')}}</button>
      </article>
    </div>

    <div class="sticky bottom-2 flex justify-end">
      <button class="inline-flex min-h-11 items-center gap-2 rounded-xl bg-[var(--c-primary)] px-5 text-sm font-black text-white shadow-lg" type="button" @click="saveAll">
        <Save :size="17"/>{{live?'ذخیره و انتشار تغییرات':locale.t('save')}}
      </button>
    </div>
    </fieldset>
  </section>
</template>
