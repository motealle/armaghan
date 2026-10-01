<script setup lang="ts">
import { Cloud, RefreshCw, RotateCcw, UploadCloud } from '@lucide/vue'
import type { StyleProfileVersionSummary } from './services/styleProfileApi'
import type { VisualSyncState } from './composables/useVisualProfileSync'

defineProps<{
  state:VisualSyncState
  authenticated:boolean
  message:string
  versions:StyleProfileVersionSummary[]
  publishedVersion?:number
  canPublish:boolean
}>()

const emit=defineEmits<{
  reconnect:[]
  publish:[]
  restore:[versionId:number]
  useServer:[]
  keepLocal:[]
}>()
</script>

<template>
  <section class="visual-editor-sync-panel">
    <div class="visual-editor-sync-summary">
      <Cloud :size="18"/>
      <div>
        <b>ذخیره مشترک</b>
        <small>{{message}}</small>
      </div>
      <button type="button" class="visual-editor-sync-refresh" aria-label="بررسی دوباره اتصال" @click="emit('reconnect')">
        <RefreshCw :size="17"/>
      </button>
    </div>

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
      ><UploadCloud :size="17"/>انتشار نسخه فعلی در staging</button>

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

    <p v-else class="visual-editor-local-note">تا زمان فعال‌شدن نشست واقعی Laravel، ادیتور بدون توقف روی همین مرورگر ذخیره می‌کند.</p>
  </section>
</template>
