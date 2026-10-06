<script setup lang="ts">
import { computed } from 'vue'
import AdaptivePanel from '@/components/ui/AdaptivePanel.vue'
import { useLocaleStore } from '@/stores/locale'
import { useHomeMediaStore } from './store'
import { homeImageTargets } from './targets'
import HomeMediaPanel from './HomeMediaPanel.vue'

const locale=useLocaleStore(),homeMedia=useHomeMediaStore()
const open=computed(()=>Boolean(homeMedia.editTarget))
const item=computed(()=>homeImageTargets.find(row=>row.target===homeMedia.editTarget))
const title=computed(()=>item.value?.labels[locale.locale]??({fa:'ویرایش عکس صفحه اصلی',en:'Edit home image',ar:'تعديل صورة الصفحة',ku:'دەستکاری وێنە'})[locale.locale])
const help=computed(()=>({fa:'یک عکس انتخاب کنید؛ فایل بهینه می‌شود و همان لحظه روی همین بخش از سایت اعمال می‌شود.',en:'Choose an image; it is optimized and applied to this section immediately.',ar:'اختر صورة؛ سيتم تحسينها وتطبيقها مباشرة على هذا القسم.',ku:'وێنەیەک هەڵبژێرە؛ خۆکارانە باشتر دەکرێت و لەم بەشە جێبەجێ دەکرێت.'})[locale.locale])
</script>

<template>
  <AdaptivePanel :open="open" :title="title" wide @close="homeMedia.closeEditor()">
    <p class="mb-3 text-sm leading-7 text-[var(--c-muted)]">{{help}}</p>
    <HomeMediaPanel v-if="homeMedia.editTarget" :target="homeMedia.editTarget" :publication-channel="homeMedia.channel" quick/>
  </AdaptivePanel>
</template>
