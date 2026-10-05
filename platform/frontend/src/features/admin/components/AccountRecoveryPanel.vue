<script setup lang="ts">
import {verifyAccountBackup} from '../services/accountBackup'
import {ref,watch,onMounted} from 'vue'
import {Download,Trash2,Undo2,Upload} from '@lucide/vue'
import AdaptivePanel from '@/components/ui/AdaptivePanel.vue'
import {useLocaleStore} from '@/stores/locale'
import {CustomerSessionApiError} from '@/features/auth/services/customerSessionApi'
import {prepareAccountBackup,deleteBackedUpAccount,fetchAccountArchives,restoreAccountArchive,importAccountBackup,type AccountResource,type AccountBackup,type AccountArchive} from '../services/adminApi'
const props=defineProps<{resource:AccountResource;target:{id:number;revision:string;label:string}|null}>()
const emit=defineEmits<{close:[];saved:[];busy:[boolean]}>()
const locale=useLocaleStore(),busy=ref(false),error=ref(''),backup=ref<AccountBackup|null>(null),downloaded=ref(false),confirmed=ref(false),archives=ref<AccountArchive[]>([])
watch(()=>props.target,()=>{backup.value=null;downloaded.value=false;confirmed.value=false;error.value=''})
function fail(e:unknown){error.value=locale.t(e instanceof CustomerSessionApiError&&e.status===409?'adminConflict':e instanceof CustomerSessionApiError&&[401,403].includes(e.status)?'adminPermissionDenied':'adminRequestFailed')}
async function load(){try{archives.value=(await fetchAccountArchives(props.resource)).archives}catch(e){fail(e)}}
async function action(work:()=>Promise<void>){if(busy.value)return;busy.value=true;emit('busy',true);error.value='';try{await work()}catch(e){fail(e)}finally{busy.value=false;emit('busy',false)}}
async function download(){await action(async()=>{
  if(!props.target)return
  // A fresh receipt is required for each download; no local-only deletion path.
  backup.value=null;downloaded.value=false;confirmed.value=false
  const issued=await prepareAccountBackup(props.resource,props.target)
  await verifyAccountBackup(issued.backup,issued.backup_sha256)
  const blob=new Blob([issued.backup],{type:'application/json;charset=utf-8'})
  const url=URL.createObjectURL(blob),link=document.createElement('a')
  link.href=url;link.download=issued.filename;document.body.appendChild(link);link.click();link.remove()
  setTimeout(()=>URL.revokeObjectURL(url),30_000)
  backup.value=issued;downloaded.value=true
})}
async function remove(){await action(async()=>{
  if(!backup.value||!downloaded.value||!confirmed.value)return
  await deleteBackedUpAccount(backup.value)
  backup.value=null;emit('close');emit('saved');await load()
})}
async function restore(id:string){await action(async()=>{await restoreAccountArchive(id);await load();emit('saved')})}
async function importFile(event:Event){const input=event.target as HTMLInputElement,file=input.files?.[0];input.value='';if(!file)return
  await action(async()=>{if(file.size>100_000)throw new Error('Backup too large');await importAccountBackup(await file.text());await load();emit('saved')})
}
onMounted(()=>load())
</script>
<template>
  <section class="admin-surface space-y-3 rounded-2xl p-3" :aria-busy="busy">
    <details><summary class="cursor-pointer font-bold">{{locale.t('accountRecoveryTitle')}} ({{archives.length}})</summary>
      <p class="my-2 text-sm">{{locale.t('accountRecoveryHelp')}}</p>
      <div v-for="row in archives" :key="row.id" class="flex items-center justify-between gap-3 py-2"><span>{{row.label}}</span><button class="mini-action" :disabled="busy" @click="restore(row.id)"><Undo2 :size="16"/>{{locale.t('accountUndo')}}</button></div>
      <p v-if="!archives.length" class="text-sm">{{locale.t('accountTrashEmpty')}}</p>
    </details>
    <label class="mini-action cursor-pointer"><Upload :size="16"/>{{locale.t('accountImportBackup')}}<input type="file" accept=".json,application/json" class="sr-only" :disabled="busy" @change="importFile"></label>
    <p v-if="error&&!target" class="auth-error" role="alert">{{error}}</p>
    <AdaptivePanel :open="!!target" :title="locale.t('accountDeleteTitle')" @close="!busy&&emit('close')">
      <div class="space-y-4">
        <b>{{target?.label}}</b>
        <p>{{locale.t('accountDeleteWarning')}}</p>
        <p class="text-sm">{{locale.t('accountBackupPrivacy')}}</p>
        <button class="mini-action" :disabled="busy" @click="download"><Download :size="17"/>{{locale.t('accountDownloadBackup')}}</button>
        <label class="flex items-start gap-2"><input v-model="confirmed" type="checkbox" :disabled="busy||!downloaded">{{locale.t('accountBackupConfirmed')}}</label>
        <p v-if="error" class="auth-error" role="alert">{{error}}</p>
        <div class="flex justify-end gap-2"><button class="mini-action" :disabled="busy" @click="emit('close')">{{locale.t('cancel')}}</button><button class="mini-action text-rose-600" :disabled="busy||!downloaded||!confirmed" @click="remove"><Trash2 :size="17"/>{{locale.t('accountDeleteTitle')}}</button></div>
      </div>
    </AdaptivePanel>
  </section>
</template>
