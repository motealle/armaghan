<script setup lang="ts">
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { ImageOff, Maximize2, Shirt } from '@lucide/vue'
import PhotoSwipeLightbox from 'photoswipe/lightbox'
import 'photoswipe/style.css'
import type { Product } from '@/types/domain'
import { useLocaleStore } from '@/stores/locale'

const props=defineProps<{product:Product}>()
const locale=useLocaleStore()
const root=ref<HTMLElement|null>(null)
const active=ref(0)
const visible=ref(false)
const paused=ref(false)
const loaded=ref<Record<number,boolean>>({})
const failed=ref<Record<number,boolean>>({})
const images=computed(()=> {
  const gallery=(props.product.gallery ?? []).filter(Boolean)
  if(gallery.length)return gallery.slice(0,2)
  return props.product.image ? [props.product.image] : []
})
let observer:IntersectionObserver|undefined
let timer:number|undefined
let lightbox:PhotoSwipeLightbox|undefined
const reduceMotion=window.matchMedia('(prefers-reduced-motion: reduce)')

function stopTimer(){if(timer)window.clearInterval(timer);timer=undefined}
function startTimer(){
  stopTimer()
  if(images.value.length<2||paused.value||!visible.value||document.hidden||reduceMotion.matches)return
  timer=window.setInterval(()=>{active.value=(active.value+1)%images.value.length},4800)
}
function updateTimer(){startTimer()}
function onVisibility(){updateTimer()}
function markLoaded(index:number){loaded.value={...loaded.value,[index]:true};failed.value={...failed.value,[index]:false}}
function markFailed(index:number){failed.value={...failed.value,[index]:true};loaded.value={...loaded.value,[index]:false}}
function choose(index:number){active.value=index;startTimer()}

async function dimensions(src:string):Promise<{width:number;height:number}>{
  return new Promise(resolve=>{
    const image=new Image()
    image.onload=()=>resolve({width:image.naturalWidth||1600,height:image.naturalHeight||1200})
    image.onerror=()=>resolve({width:1600,height:1200})
    image.src=src
  })
}

async function openLightbox(index:number){
  if(!images.value.length)return
  paused.value=true
  stopTimer()
  const slides=await Promise.all(images.value.map(async(src)=>{
    const size=await dimensions(src)
    return{src,width:size.width,height:size.height,alt:props.product.name}
  }))
  lightbox?.destroy()
  lightbox=new PhotoSwipeLightbox({
    dataSource:slides,
    index,
    pswpModule:()=>import('photoswipe'),
    bgOpacity:.88,
    showHideAnimationType:'fade',
    closeOnVerticalDrag: true,
    pinchToClose: true,
    bgClickAction: 'close',
    tapAction: 'toggle-controls',
    imageClickAction: 'zoom-or-close',
    wheelToZoom:true,
    paddingFn:()=>({top:52,bottom:92,left:12,right:12}),
  })
  lightbox.on('uiRegister',()=>{
    lightbox?.pswp?.ui?.registerElement({
      name:'product-caption',
      order:9,
      isButton:false,
      appendTo:'root',
      html:'',
      onInit:(element,pswp)=>{
        element.className='pswp__product-caption'
        const render=()=>{
          element.replaceChildren()
          const box=document.createElement('div')
          box.className='pswp__product-caption-box'
          const title=document.createElement('strong')
          title.textContent=props.product.name
          const meta=document.createElement('span')
          meta.textContent=`${props.product.code} · ${pswp.currIndex+1}/${slides.length}`
          box.append(title,meta)
          element.append(box)
        }
        pswp.on('change',render)
        render()
      },
    })
  })
  lightbox.on('destroy',()=>{
    paused.value=false
    startTimer()
    lightbox=undefined
  })
  lightbox.init()
  await nextTick()
  lightbox.loadAndOpen(index)
}

watch(images,()=>{
  active.value=0
  loaded.value={}
  failed.value={}
  startTimer()
})
watch([visible,paused],updateTimer)

onMounted(()=>{
  observer=new IntersectionObserver(([entry])=>{visible.value=Boolean(entry?.isIntersecting)},{rootMargin:'80px',threshold:.2})
  if(root.value)observer.observe(root.value)
  document.addEventListener('visibilitychange',onVisibility)
  reduceMotion.addEventListener?.('change',updateTimer)
  images.value.forEach(src=>{const image=new Image();image.src=src})
})
onBeforeUnmount(()=>{
  stopTimer()
  observer?.disconnect()
  lightbox?.destroy()
  document.removeEventListener('visibilitychange',onVisibility)
  reduceMotion.removeEventListener?.('change',updateTimer)
})
</script>

<template>
  <div
    ref="root"
    class="product-media relative overflow-hidden bg-[var(--c-media-bg)]"
    @mouseenter="paused=true"
    @mouseleave="paused=false"
    @focusin="paused=true"
    @focusout="paused=false"
  >
    <button
      type="button"
      class="group relative block aspect-[4/3] w-full overflow-hidden text-start"
      :aria-label="locale.t('tapImage')"
      @click="openLightbox(active)"
    >
      <div class="absolute inset-0 grid place-items-center text-[var(--c-primary)]">
        <div class="absolute -start-10 -top-10 h-28 w-28 rounded-full bg-white/65" />
        <div class="absolute -bottom-12 -end-7 h-32 w-32 rounded-full bg-[color-mix(in_srgb,var(--c-primary)_7%,transparent)]" />
        <div class="relative flex flex-col items-center gap-3">
          <div class="fallback-clay-icon">
            <Shirt v-if="images.length" :size="38" :stroke-width="1.5" />
            <ImageOff v-else :size="38" :stroke-width="1.5" />
          </div>
          <span class="text-[10px] font-bold text-[var(--c-muted)]">{{ product.subcategoryName }}</span>
        </div>
      </div>
      <template v-for="(src,index) in images" :key="src">
        <div v-if="!loaded[index] && !failed[index] && index===active" class="skeleton absolute inset-0 z-10" />
        <template v-if="!failed[index]">
          <img
            :src="src"
            alt=""
            aria-hidden="true"
            class="absolute inset-0 z-[12] h-full w-full scale-110 object-cover opacity-0 blur-2xl transition-opacity duration-500"
            :class="index===active&&loaded[index]?'opacity-20':'pointer-events-none opacity-0'"
            :loading="index===0?'eager':'lazy'"
            draggable="false"
          />
          <img
            :src="src"
            :alt="index===active ? product.name : ''"
            class="absolute inset-0 z-20 h-full w-full object-contain p-2.5 transition-opacity duration-500 sm:p-3"
            :class="index===active&&loaded[index]?'opacity-100':'pointer-events-none opacity-0'"
            :loading="index===0?'eager':'lazy'"
            draggable="false"
            @load="markLoaded(index)"
            @error="markFailed(index)"
          />
        </template>
      </template>
      <span class="absolute bottom-2 end-2 z-30 grid h-8 w-8 place-items-center rounded-lg bg-slate-950/60 text-white opacity-90 backdrop-blur-md transition group-hover:bg-slate-950/75">
        <Maximize2 :size="16" />
      </span>
    </button>
    <div v-if="images.length>1" class="absolute bottom-3 start-3 z-30 flex items-center gap-1.5 rounded-full bg-white/88 px-2 py-1 shadow-sm backdrop-blur-sm" @click.stop>
      <button
        v-for="(_,index) in images"
        :key="index"
        type="button"
        class="h-1.5 rounded-full transition-all"
        :class="index===active?'w-5 bg-[var(--c-primary)]':'w-1.5 bg-slate-400'"
        :aria-label="`تصویر ${index+1}`"
        @click="choose(index)"
      />
    </div>
  </div>
</template>
