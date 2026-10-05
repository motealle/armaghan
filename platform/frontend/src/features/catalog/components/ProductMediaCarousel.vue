<script setup lang="ts">
import { computed, onBeforeUnmount, ref, watch } from 'vue'
import { ChevronLeft, ChevronRight, ZoomIn } from '@lucide/vue'
import type PhotoSwipe from 'photoswipe'
import type { Product } from '@/types/domain'
import SmartImage from '@/components/media/SmartImage.vue'
import { landscapePlaceholder, productPlaceholder } from '@/data/productPlaceholders'
import { useDesignStore } from '@/stores/design'
import { useLocaleStore } from '@/stores/locale'
import { cardMediaSources, swipeDirection } from '../services/cardMedia'

const props=defineProps<{product:Product}>()
const locale=useLocaleStore()
const design=useDesignStore()
const productName=computed(()=>locale.productName(props.product.code,props.product.name,props.product.names))
const portraitOrSelectedPlaceholder=computed(()=>productPlaceholder(design.placeholderSet,props.product.subcategoryCode,design.placeholderOrientation))
const landscapeFallback=computed(()=>landscapePlaceholder(design.placeholderSet,props.product.subcategoryCode))
const slides=computed(()=>cardMediaSources(props.product))
const index=ref(0)
const selected=computed(()=>slides.value[index.value])
const productImage=computed(()=>selected.value?.thumb||portraitOrSelectedPlaceholder.value)
const fallbackImage=computed(()=>slides.value.length?portraitOrSelectedPlaceholder.value:landscapeFallback.value)
const srcset=computed(()=>{
  const slide=selected.value
  if(!slide||slide.thumb===slide.card)return undefined
  const width=slide.width
  if(width&&width<=320)return undefined
  return `${slide.thumb} ${Math.min(320,width||320)}w, ${slide.card} ${Math.min(800,width||800)}w`
})
const labels=computed(()=>({
  fa:{open:'نمایش بزرگ تصویر',next:'تصویر بعدی',prev:'تصویر قبلی',close:'بستن',zoom:'بزرگ‌نمایی',error:'تصویر در دسترس نیست'},
  en:{open:'Enlarge image',next:'Next image',prev:'Previous image',close:'Close',zoom:'Zoom',error:'Image unavailable'},
  ar:{open:'تكبير الصورة',next:'الصورة التالية',prev:'الصورة السابقة',close:'إغلاق',zoom:'تكبير',error:'الصورة غير متاحة'},
  ku:{open:'گەورەکردنی وێنە',next:'وێنەی دواتر',prev:'وێنەی پێشوو',close:'داخستن',zoom:'گەورەکردن',error:'وێنە بەردەست نییە'},
}[locale.locale]))
let viewer:PhotoSwipe|undefined
let disposed=false
const opening=ref(false)
watch(()=>slides.value.map(s=>s.card).join('|'),()=>{index.value=0;viewer?.close()})
function move(delta:number){index.value=(index.value+delta+slides.value.length)%slides.value.length}
let touchStart:{x:number;y:number}|undefined
let swiped=false
function start(event:TouchEvent){
  swiped=false
  const touch=event.touches.length===1?event.touches[0]:undefined
  touchStart=touch?{x:touch.clientX,y:touch.clientY}:undefined
}
function end(event:TouchEvent){
  const touch=event.changedTouches[0]
  if(!touchStart||!touch)return
  const delta=swipeDirection(touch.clientX-touchStart.x,touch.clientY-touchStart.y)
  if(delta&&slides.value.length>1){move(delta);swiped=true}
  touchStart=undefined
}
async function open(){
  if(swiped){swiped=false;return}
  if(opening.value||!slides.value.length)return
  opening.value=true
  try{
    const [{default:Gallery}]=await Promise.all([import('photoswipe'),import('photoswipe/style.css')])
    if(disposed)return
    // Only the enlarged viewer asks for detail files; cards never download them.
    const source=await Promise.all(slides.value.map(async slide=>{
      let width=slide.width, height=slide.height
      if(!width||!height){
        const size=await new Promise<{width:number;height:number}>(resolve=>{
          const image=new Image()
          let settled=false
          const finish=()=>{if(settled)return;settled=true;clearTimeout(timer);resolve({width:image.naturalWidth||800,height:image.naturalHeight||1200})}
          const timer=setTimeout(finish,10000)
          image.onload=finish;image.onerror=finish;image.src=slide.thumb
        })
        width=size.width;height=size.height
      }
      return {src:slide.detail,msrc:slide.thumb,alt:productName.value,width:Math.min(1600,width),height:Math.round(height*Math.min(1,1600/width))}
    }))
    if(disposed)return
    viewer=new Gallery({dataSource:source,index:index.value,loop:source.length>1,initialZoomLevel:'fit',secondaryZoomLevel:2,maxZoomLevel:4,showHideAnimationType:'none',closeTitle:labels.value.close,zoomTitle:labels.value.zoom,arrowPrevTitle:labels.value.prev,arrowNextTitle:labels.value.next,errorMsg:labels.value.error})
    viewer.on('change',()=>{if(viewer)index.value=viewer.currIndex})
    viewer.on('destroy',()=>{viewer=undefined})
    viewer.init()
  }finally{opening.value=false}
}
onBeforeUnmount(()=>{disposed=true;viewer?.destroy()})
</script>

<template>
  <div class="product-media-placeholder relative overflow-hidden" style="touch-action:pan-y" @touchstart.passive="start" @touchend.passive="end" @touchcancel="touchStart=undefined">
    <svg class="product-placeholder-svg hidden" viewBox="0 0 1 1" aria-hidden="true"><path d="M0 0h1v1H0z"/></svg>
    <SmartImage :key="productImage" :src="productImage" :srcset="srcset" sizes="(max-width: 767px) 46vw, (max-width: 1279px) 30vw, 280px" :fallback-src="fallbackImage" :alt="productName" aspect="product" fit="contain" />
    <button v-if="slides.length" type="button" class="absolute inset-0 z-30 focus-visible:outline-2 focus-visible:outline-offset-[-3px]" :aria-label="labels.open" :disabled="opening" @click.stop="open">
      <span class="absolute end-2 top-2 grid h-11 w-11 place-items-center rounded-full bg-black/60 text-white"><ZoomIn :size="19"/></span>
    </button>
    <template v-if="slides.length>1">
      <button type="button" class="absolute left-1 top-1/2 z-40 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full bg-black/60 text-white" :aria-label="labels.prev" @click.stop="move(-1)"><ChevronLeft :size="22"/></button>
      <button type="button" class="absolute right-1 top-1/2 z-40 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full bg-black/60 text-white" :aria-label="labels.next" @click.stop="move(1)"><ChevronRight :size="22"/></button>
      <div class="pointer-events-none absolute inset-x-0 bottom-2 z-40 flex justify-center gap-1.5" aria-hidden="true"><span v-for="(_,i) in slides" :key="i" class="h-1.5 w-1.5 rounded-full shadow" :class="i===index?'bg-white':'bg-black/40'"/></div>
    </template>
  </div>
</template>
