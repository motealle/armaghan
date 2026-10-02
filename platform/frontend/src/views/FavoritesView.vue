<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Check, Copy, MessageCircleMore, Share2, ShieldOff, X } from '@lucide/vue'
import { useRoute } from 'vue-router'
import ProductGrid from '@/features/catalog/components/ProductGrid.vue'
import WhatsAppIcon from '@/components/icons/WhatsAppIcon.vue'
import {
  issueFavoriteShare,
  resolveFavoriteShare,
  revokeFavoriteShare,
  type IssuedFavoriteShare,
} from '@/features/favorites/shareFavorites'
import { whatsappUrl } from '@/services/whatsapp'
import { useCatalogStore } from '@/stores/catalog'
import { useCustomersStore } from '@/stores/customers'
import { useFavoritesStore } from '@/stores/favorites'
import { useLocaleStore } from '@/stores/locale'

const favorites=useFavoritesStore()
const catalog=useCatalogStore()
const customers=useCustomersStore()
const locale=useLocaleStore()
const route=useRoute()

const shareState=ref<'idle'|'copied'|'shared'|'revoked'|'error'>('idle')
const resolveState=ref<'idle'|'loading'|'ready'|'error'>('idle')
const resolvedCodes=ref<string[]>([])
const issuedShare=ref<IssuedFavoriteShare|null>(null)
const issuedCodesKey=ref('')

function decodeLegacyFavoriteCodes(value:unknown):string[]{
  if(typeof value!=='string'||!value.startsWith('v1:'))return[]
  return [...new Set(value.slice(3).split(',').filter(code=>/^\d{5}$/.test(code)))].slice(0,80)
}

const shareToken=computed(()=>typeof route.params.token==='string'?route.params.token:'')
const legacyCodes=computed(()=>decodeLegacyFavoriteCodes(route.query.shared))
const isBackendShare=computed(()=>Boolean(shareToken.value))
const isShared=computed(()=>isBackendShare.value||route.query.shared!==undefined)
const sharedCodes=computed(()=>isBackendShare.value?resolvedCodes.value:legacyCodes.value)
const sharedItems=computed(()=>{
  const order=new Map(sharedCodes.value.map((code,index)=>[code,index]))
  return catalog.items
    .filter(product=>order.has(product.code))
    .sort((a,b)=>(order.get(a.code)??0)-(order.get(b.code)??0))
})
const visibleItems=computed(()=>isShared.value?sharedItems.value:favorites.items)
const currentCodes=computed(()=>favorites.items.map(product=>product.code))
const currentCodesKey=computed(()=>currentCodes.value.join(','))

watch(shareToken,async token=>{
  resolvedCodes.value=[]
  if(!token){resolveState.value='idle';return}
  resolveState.value='loading'
  try{
    const share=await resolveFavoriteShare(token)
    resolvedCodes.value=share.product_codes
    resolveState.value='ready'
  }catch{
    resolveState.value='error'
  }
},{immediate:true})

watch(currentCodesKey,()=>{
  if(issuedCodesKey.value&&issuedCodesKey.value!==currentCodesKey.value){
    issuedShare.value=null
    issuedCodesKey.value=''
    shareState.value='idle'
  }
})

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

async function ensurePersistedShare():Promise<IssuedFavoriteShare|null>{
  if(!currentCodes.value.length)return null
  if(issuedShare.value&&issuedCodesKey.value===currentCodesKey.value)return issuedShare.value
  try{
    const share=await issueFavoriteShare(currentCodes.value)
    issuedShare.value=share
    issuedCodesKey.value=currentCodesKey.value
    return share
  }catch{
    shareState.value='error'
    return null
  }
}

async function shareFavorites(){
  shareState.value='idle'
  const share=await ensurePersistedShare()
  if(!share)return

  try{
    if(typeof navigator.share==='function'){
      await navigator.share({
        title:locale.t('favoriteTitle'),
        text:locale.t('shareFavoritesText'),
        url:share.url,
      })
      shareState.value='shared'
      return
    }
    if(navigator.clipboard?.writeText){
      await navigator.clipboard.writeText(share.url)
      shareState.value='copied'
      return
    }
    shareState.value=fallbackCopy(share.url)?'copied':'error'
  }catch(error){
    if(error instanceof DOMException&&error.name==='AbortError')return
    try{
      if(navigator.clipboard?.writeText){
        await navigator.clipboard.writeText(share.url)
        shareState.value='copied'
        return
      }
      shareState.value=fallbackCopy(share.url)?'copied':'error'
    }catch{
      shareState.value='error'
    }
  }
}

async function shareViaWhatsApp(){
  shareState.value='idle'
  const share=await ensurePersistedShare()
  if(!share)return
  const message=locale.t('shareFavoritesText')+'\n'+share.url
  window.open(whatsappUrl(message),'_blank','noopener')
  shareState.value='shared'
}

async function revokeIssuedShare(){
  const share=issuedShare.value
  if(!share?.owned)return
  try{
    await revokeFavoriteShare(share.id)
    issuedShare.value=null
    issuedCodesKey.value=''
    shareState.value='revoked'
  }catch{
    shareState.value='error'
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

      <div v-if="!isShared&&favorites.items.length" class="ms-auto flex flex-wrap gap-2">
        <button type="button" class="favorites-share-button" @click="shareFavorites">
          <Check v-if="shareState==='copied'||shareState==='shared'" :size="18"/>
          <Share2 v-else :size="18"/>
          <span>{{shareState==='copied'?locale.t('shareCopied'):shareState==='shared'?locale.t('shareDone'):locale.t('shareFavorites')}}</span>
        </button>
        <button type="button" class="favorites-share-button" @click="shareViaWhatsApp">
          <WhatsAppIcon :size="20"/>
          <span>{{locale.t('continueWhatsApp')}}</span>
        </button>
        <button
          v-if="issuedShare?.owned"
          type="button"
          class="favorites-share-button"
          @click="revokeIssuedShare"
        >
          <ShieldOff :size="18"/>
          <span>{{locale.t('revoke')}}</span>
        </button>
      </div>

      <span v-if="shareState==='error'" class="w-full text-xs font-bold text-rose-600" role="status" aria-live="polite">{{locale.t('shareFavoritesError')}}</span>
      <span v-else-if="shareState==='revoked'" class="w-full text-xs font-bold text-[var(--c-secondary)]" role="status" aria-live="polite">{{locale.t('revoke')}}</span>
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

    <div v-if="isBackendShare&&resolveState==='loading'" class="rounded-2xl border border-dashed border-[var(--c-border)] bg-[var(--c-surface)] p-10 text-center text-sm text-[var(--c-muted)]">
      {{locale.t('sharedFavoritesHelp')}}
    </div>
    <ProductGrid v-else-if="visibleItems.length" :products="visibleItems"/>
    <div v-else class="rounded-2xl border border-dashed border-[var(--c-border)] bg-[var(--c-surface)] p-10 text-center text-sm text-[var(--c-muted)]">
      {{isShared?locale.t('sharedFavoritesInvalid'):locale.t('emptyFavorites')}}
    </div>
  </section>
</template>
