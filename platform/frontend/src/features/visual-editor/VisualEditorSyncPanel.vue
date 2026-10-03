<script setup lang="ts">
import { ref } from 'vue'
import { Cloud, Download, RefreshCw, RotateCcw, Upload, UploadCloud } from '@lucide/vue'
import type { StyleProfileVersionSummary } from './services/styleProfileApi'
import type { VisualSyncState } from './composables/useVisualProfileSync'
import { useVisualStyleStore } from './store'

defineProps<{
  state:VisualSyncState
  authenticated:boolean
  message:string
  versions:StyleProfileVersionSummary[]
  publishedVersion?:number
  channel?:string
  canPublish:boolean
}>()

const emit=defineEmits<{
  reconnect:[]
  publish:[]
  restore:[versionId:number]
  useServer:[]
  keepLocal:[]
}>()

const visual=useVisualStyleStore()
const fileInput=ref<HTMLInputElement|null>(null)
const fileStatus=ref('')

function exportJson(){
  const payload=JSON.stringify(visual.profile,null,2)
  const blob=new Blob([payload],{type:'application/json;charset=utf-8'})
  const url=URL.createObjectURL(blob)
  const anchor=document.createElement('a')
  anchor.href=url
  anchor.download='armaghan-style-profile.json'
  document.body.appendChild(anchor)
  anchor.click()
  anchor.remove()
  window.setTimeout(()=>URL.revokeObjectURL(url),0)
  fileStatus.value='فایل JSON تنظیمات ذخیره شد.'
}

async function importJson(event:Event){
  const input=event.target as HTMLInputElement
  const file=input.files?.[0]
  input.value=''
  if(!file)return
  if(file.size>512*1024){
    fileStatus.value='فایل بزرگ‌تر از حد مجاز ۵۱۲ کیلوبایت است.'
    return
  }
  try{
    const parsed=JSON.parse(await file.text())
    visual.replaceProfile(parsed)
    fileStatus.value='تنظیمات JSON بارگذاری شد و در همین مرورگر ذخیره شد.'
  }catch{
    fileStatus.value='فایل JSON معتبر نیست.'
  }
}
</script>

<template>
  <section class="visual-editor-sync-panel">
    <div class="visual-editor-sync-summary">
      <Cloud :size="18"/>
      <div>
        <b>ذخیره تنظیمات</b>
        <small>{{message}}</small>
      </div>
      <button type="button" class="visual-editor-sync-refresh" aria-label="بررسی دوباره اتصال" @click="emit('reconnect')">
        <RefreshCw :size="17"/>
      </button>
    </div>

    <div class="visual-editor-profile-file-actions">
      <button type="button" @click="exportJson"><Download :size="16"/>ذخیره فایل JSON</button>
      <button type="button" @click="fileInput?.click()"><Upload :size="16"/>بارگذاری JSON</button>
      <input ref="fileInput" type="file" accept="application/json,.json" hidden @change="importJson"/>
    </div>
    <small v-if="fileStatus" class="visual-editor-file-status">{{fileStatus}}</small>

    <div v-if="state==='conflict'" class="visual-editor-conflict-actions">
      <button type="button" @click="emit('useServer')">بارگذاری نسخه سرور</button>
      <button type="button" @click="emit('keepLocal')">جایگزینی با نسخه این دستگاه</button>
    </div>

    <template v-if="authenticated">
      <button
        type="button"
        class="visual-editor-publish-button"
        :disabled="!canPublish"
        @click="emit('publish')"
      ><UploadCloud :size="17"/>{{channel==='production'?'انتشار روی دامنه اصلی':'انتشار در نسخه آزمایشی'}}</button>

      <details v-if="versions.length" class="visual-editor-history">
        <summary>تاریخچه نسخه‌ها</summary>
        <div>
          <div v-for="version in versions.slice(0,10)" :key="version.id" class="visual-editor-history-row">
            <span>
              <b>نسخه {{version.number}}</b>
              <small>{{version.source_test?('Test '+version.source_test):'—'}} · {{version.checksum.slice(0,8)}}</small>
            </span>
            <em v-if="publishedVersion===version.number">منتشرشده</em>
            <button v-else type="button" title="بازگردانی به‌صورت نسخه جدید" @click="emit('restore',version.id)">
              <RotateCcw :size="16"/>
            </button>
          </div>
        </div>
      </details>
    </template>

    <p v-else class="visual-editor-local-note">تغییرات همین حالا خودکار در مرورگر ذخیره می‌شوند. فایل JSON هم برای بکاپ/انتقال دستی قابل ذخیره است؛ ذخیره مشترک پس از نشست واقعی Laravel فعال می‌شود.</p>
  </section>
</template>
