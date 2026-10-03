<script setup lang="ts">
import {computed,onMounted,ref,watch} from 'vue'
import {requestJson} from '@/features/auth/services/customerSessionApi'
import {useLocaleStore} from '@/stores/locale'
import {useHomeMediaStore} from './store'
const locale=useLocaleStore(),publicMedia=useHomeMediaStore()
const words={fa:['تصاویر صفحه خانه','تصویر اصلی','درباره ارمغان','توانمندی تولید','آماده‌سازی صادرات','اسناد و تجارت','بنر نوزادی','بنر بچگانه','بنر زنانه','نسخه آزمایشی','صفحه اصلی','انتشار تصاویر','تصویر پیش‌فرض','بارگذاری تصویر','تصاویر ذخیره شدند؛ برای نمایش عمومی انتشار را بزنید.','تصاویر منتشر شدند.','عملیات انجام نشد؛ دوباره بارگذاری کنید.'],en:['Home images','Hero','About','Production','Export','Trade','Baby banner','Kids banner','Women banner','Test version','Main site','Publish images','Default image','Upload image','Saved; publish to show publicly.','Images published.','Action failed; reload and retry.'],ar:['صور الصفحة الرئيسية','الصورة الرئيسية','عن الشركة','الإنتاج','التصدير','التجارة','بانر الرضع','بانر الأطفال','بانر النساء','نسخة تجريبية','الموقع الرئيسي','نشر الصور','الصورة الافتراضية','رفع صورة','تم الحفظ؛ انشر لإظهار الصور.','تم نشر الصور.','تعذرت العملية؛ أعد التحميل.'],ku:['وێنەکانی ماڵەوە','وێنەی سەرەکی','دەربارە','بەرهەمهێنان','هەناردە','بازرگانی','ساوا','منداڵان','ژنان','تاقیکردنەوە','ماڵپەڕی سەرەکی','بڵاوکردنەوە','وێنەی بنەڕەتی','بارکردنی وێنە','پاشەکەوت کرا؛ بڵاوی بکەرەوە.','بڵاوکرایەوە.','سەرکەوتوو نەبوو؛ نوێ بکەرەوە.']}
const w=(i:number)=>words[locale.locale][i]??''
const targets=['hero','about','capability.production','capability.export','capability.trade','banner.1','banner.2','banner.3']
interface Image {id:number;target:string;url:string;channels:string[]}
interface Snapshot {revision:string;images:Image[]}
const state=ref<Snapshot|null>(null),channel=ref('production'),chosen=ref<Record<string,number>>({}),busy=ref(false),message=ref(''),error=ref('')
const selected=computed(()=>Object.values(chosen.value).filter(id=>id>0))
function reset(){chosen.value=Object.fromEntries(targets.map(t=>[t,state.value?.images.find(m=>m.target===t&&m.channels.includes(channel.value))?.id??0]))}
async function load(){try{state.value=await requestJson<Snapshot>('/api/admin/home-media');reset()}catch{error.value=w(16)}}
watch(channel,reset);onMounted(load)
async function upload(target:string,event:Event){const input=event.target as HTMLInputElement,file=input.files?.[0];if(!file||!state.value||busy.value)return;busy.value=true;error.value='';message.value='';try{const body=new FormData();body.set('revision',state.value.revision);body.set('target',target);body.set('image',file);state.value=await requestJson<Snapshot>('/api/admin/home-media',{method:'POST',body});const image=state.value.images.filter(m=>m.target===target).at(-1);if(image)chosen.value[target]=image.id;message.value=w(14)}catch{error.value=w(16)}finally{busy.value=false;input.value=''}}
async function publish(){if(!state.value||busy.value)return;busy.value=true;error.value='';message.value='';try{state.value=await requestJson<Snapshot>('/api/admin/home-media/publish/'+channel.value,{method:'POST',body:JSON.stringify({revision:state.value.revision,selection:selected.value})});reset();await publicMedia.refresh();message.value=w(15)}catch{error.value=w(16)}finally{busy.value=false}}
</script>
<template>
 <section class="admin-surface rounded-2xl p-4 space-y-3">
  <h2 class="font-bold">{{w(0)}}</h2>
  <p class="text-xs text-[var(--c-muted)]">JPEG / PNG / WebP · 8 MB · 5000 × 5000</p>
  <div class="flex flex-wrap gap-2"><select v-model="channel" :disabled="busy" class="field-input" :aria-label="w(11)"><option value="staging">{{w(9)}}</option><option value="production">{{w(10)}}</option></select><button class="mini-action" :disabled="!state||busy" @click="publish">{{w(11)}}</button><button class="mini-action" :disabled="busy" @click="load">{{locale.locale==='fa'?'بارگذاری مجدد':locale.locale==='ar'?'إعادة التحميل':locale.locale==='ku'?'نوێکردنەوە':'Reload'}}</button></div>
  <p v-if="message" role="status">{{message}}</p><p v-if="error" role="alert" class="text-red-700">{{error}}</p>
  <div v-if="state" class="grid gap-3 sm:grid-cols-2">
   <article v-for="(target,index) in targets" :key="target" class="rounded-xl border border-[var(--c-border)] p-3 space-y-2">
    <h3 class="text-sm font-bold">{{w(index+1)}}</h3>
    <select v-model.number="chosen[target]" :disabled="busy" class="field-input" :aria-label="w(index+1)"><option :value="0">{{w(12)}}</option><option v-for="image in state.images.filter(m=>m.target===target)" :key="image.id" :value="image.id">#{{image.id}}</option></select>
    <img v-if="chosen[target]" :src="state.images.find(m=>m.id===chosen[target])?.url" :alt="w(index+1)" class="w-full h-28 object-contain rounded-lg">
    <label class="block text-xs">{{w(13)}}<input type="file" accept="image/jpeg,image/png,image/webp" :disabled="busy" class="block max-w-full mt-2" @change="upload(target,$event)"></label>
   </article>
  </div>
 </section>
</template>
