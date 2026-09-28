<script setup lang="ts">
import { computed, ref } from 'vue'
import { Check, Copy, MessageCircleMore, Share2, X } from '@lucide/vue'
import { useRoute } from 'vue-router'
import ProductGrid from '@/features/catalog/components/ProductGrid.vue'
import { buildFavoritesShareUrl, decodeFavoriteCodes } from '@/features/favorites/shareFavorites'
import { useCatalogStore } from '@/stores/catalog'
import { useCustomersStore } from '@/stores/customers'
import { useFavoritesStore } from '@/stores/favorites'
import { useLocaleStore } from '@/stores/locale'

const favorites=useFavoritesStore()
const catalog=useCatalogStore()
const customers=useCustomersStore()
const locale=useLocaleStore()
const route=useRoute()
const shareState=ref<'idle'|'copied'|'shared'|'error'>('idle')

const sharedCodes=computed(()=>decodeFavoriteCodes(route.query.shared))
const isShared=computed(()=>route.query.shared!==undefined)
const sharedItems=computed(()=>{
  const order=new Map(sharedCodes.value.map((code,index)=>[code,index]))
  return catalog.items
    .filter(product=>order.has(product.code))
    .sort((a,b)=>(order.get(a.code)??0)-(order.get(b.code)??0))
})
const visibleItems=computed(()=>isShared.value?sharedItems.value:favorites.items)

function fallbackCopy(value:string){
  const input=document.createElement('textarea')
  input.value=value
  input.setAttribute('readonly','')
  input.style.position='fixed'
  input.style.opacity='0'
  document.body.appendChild(input)
  input.select()
  const ok=document.execCommand('copy')
  input.remove()
  return ok
}

async function shareFavorites(){
  shareState.value='idle'
  const url=buildFavoritesShareUrl(
    favorites.items.map(product=>product.code),
    `${location.origin}${location.pathname}`,
  )
  if(!url){shareState.value='error';return}

  try{
    if(typeof navigator.share==='function'){
      await navigator.share({title:locale.t('favoriteTitle'),text:locale.t('shareFavoritesText'),url})
      shareState.value='shared'
      return
    }
    if(navigator.clipboard?.writeText){
      await navigator.clipboard.writeText(url)
      shareState.value='copied'
      return
    }
    shareState.value=fallbackCopy(url)?'copied':'error'
  }catch(error){
    if(error instanceof DOMException&&error.name==='AbortError')return
    try{
      if(navigator.clipboard?.writeText){await navigator.clipboard.writeText(url);shareState.value='copied';return}
      shareState.value=fallbackCopy(url)?'copied':'error'
    }catch{shareState.value='error'}
  }
}
</script>

<template>
  <section>
    <div class="mb-4 flex flex-wrap items-end gap-3">
      <div>
        <h1 class="text-[1.75rem] font-black leading-tight text-[var(--c-text)]">
          {{isShared?locale.t('sharedFavoritesTitle'):locale.t('favoriteTitle')}}
        </h1>
        <p class="mt-1 text-sm leading-6 text-[var(--c-muted)]">
          {{isShared?locale.t('sharedFavoritesHelp'):locale.t('favoriteHelp')}}
        </p>
      </div>

      <button
        v-if="!isShared&&favorites.items.length"
        type="button"
        class="favorites-share-button ms-auto"
        @click="shareFavorites"
      >
        <Check v-if="shareState==='copied'||shareState==='shared'" :size="18"/>
        <Share2 v-else :size="18"/>
        <span>{{shareState==='copied'?locale.t('shareCopied'):shareState==='shared'?locale.t('shareDone'):locale.t('shareFavorites')}}</span>
      </button>
      <span v-if="shareState==='error'" class="w-full text-xs font-bold text-rose-600" role="status" aria-live="polite">{{locale.t('shareFavoritesError')}}</span>
      <span v-else-if="shareState==='copied'||shareState==='shared'" class="sr-only" role="status" aria-live="polite">{{shareState==='copied'?locale.t('shareCopied'):locale.t('shareDone')}}</span>
    </div>

    <div v-if="isShared" class="shared-favorites-notice">
      <Copy :size="18"/>
      <span>{{locale.t('sharedFavoritesPrivacy')}}</span>
    </div>

    <div v-if="customers.currentVisitorMessage&&!isShared" class="favorites-message-bubble">
      <MessageCircleMore :size="20"/>
      <p>{{customers.currentVisitorMessage}}</p>
      <button :aria-label="locale.t('close')" @click="customers.clearCurrentVisitorMessage()"><X :size="17"/></button>
    </div>

    <ProductGrid v-if="visibleItems.length" :products="visibleItems"/>
    <div v-else class="rounded-2xl border border-dashed border-[var(--c-border)] bg-[var(--c-surface)] p-10 text-center text-sm text-[var(--c-muted)]">
      {{isShared?locale.t('sharedFavoritesInvalid'):locale.t('emptyFavorites')}}
    </div>
  </section>
</template>
