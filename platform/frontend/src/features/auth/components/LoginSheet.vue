<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { LogIn, UserPlus, ShieldCheck } from '@lucide/vue'
import { useRouter } from 'vue-router'
import { useSessionStore } from '@/stores/session'
import { useLocaleStore } from '@/stores/locale'
import { registerWithPassword, signInWithPassword, CustomerSessionApiError } from '@/features/auth/services/customerSessionApi'
import BaseModal from '@/components/ui/BaseModal.vue'
import GoogleGIcon from '@/components/icons/GoogleGIcon.vue'

const props=defineProps<{open:boolean}>()
const emit=defineEmits<{close:[]}>()
const session=useSessionStore()
const locale=useLocaleStore()
const router=useRouter()
const mode=ref<'signin'|'register'>('signin')
const email=ref('')
const password=ref('')
const confirmation=ref('')
const name=ref('')
const error=ref('')
const busy=ref(false)
const title=computed(()=>locale.t(mode.value==='register'?'register':'loginTitle'))
watch(()=>props.open,()=>{mode.value='signin';error.value='';password.value='';confirmation.value=''})
async function submit(){
  if(busy.value)return
  error.value='';busy.value=true
  try{
    if(mode.value==='register'){
      await registerWithPassword(name.value.trim(),email.value.trim(),password.value,confirmation.value)
    }else{
      const result=await signInWithPassword(email.value.trim(),password.value)
      if(result.redirect==='/backend/admin'){password.value='';emit('close');router.push('/admin');return}
    }
    if(!await session.hydrateFromBackend())throw new Error('Session unavailable')
    password.value='';confirmation.value='';emit('close');router.push('/tracking')
  }catch(e){
    error.value=e instanceof CustomerSessionApiError&&e.status===429?locale.t('loginRateLimited'):locale.t(mode.value==='register'?'registrationFailed':'invalidLogin')
  }finally{busy.value=false}
}
async function googleInfo(){
  if(busy.value)return
  error.value='';busy.value=true
  try{
    const response=await fetch('/backend/api/auth/google/status',{credentials:'same-origin',cache:'no-store'})
    const data=await response.json() as {enabled?:boolean}
    if(!response.ok||data.enabled!==true){error.value=locale.t('googleBackendRequired');return}
    const path=window.location.pathname.replace(/index\.html?$/,'')
    const returnPath=/^\/t\/(?:0[1-9]|[1-9][0-9]*)\/$/.test(path)?path:'/'
    window.location.assign('/backend/auth/google/redirect?return_path='+encodeURIComponent(returnPath))
  }catch{error.value=locale.t('googleBackendRequired')}
  finally{busy.value=false}
}
</script>
<template>
  <BaseModal :open="open" :title="title" @close="emit('close')">
    <div class="auth-tabs" role="tablist">
      <button type="button" role="tab" :aria-selected="mode==='signin'" :class="{active:mode==='signin'}" :disabled="busy" @click="mode='signin';error=''">{{locale.t('signIn')}}</button>
      <button type="button" role="tab" :aria-selected="mode==='register'" :class="{active:mode==='register'}" :disabled="busy" @click="mode='register';error=''">{{locale.t('register')}}</button>
    </div>
    <form class="mt-4 space-y-4" @submit.prevent="submit">
      <label v-if="mode==='register'" class="form-field">{{locale.t('fullName')}}<input v-model="name" autocomplete="name" maxlength="255" required :disabled="busy"></label>
      <label class="form-field">{{locale.t('email')}}<input v-model="email" type="email" autocomplete="username" dir="ltr" maxlength="255" required :disabled="busy"></label>
      <label class="form-field">{{locale.t('password')}}<input v-model="password" type="password" :autocomplete="mode==='register'?'new-password':'current-password'" dir="ltr" :minlength="mode==='register'?12:undefined" maxlength="255" required :disabled="busy"></label>
      <label v-if="mode==='register'" class="form-field">{{locale.t('confirmPassword')}}<input v-model="confirmation" type="password" autocomplete="new-password" dir="ltr" required :disabled="busy"></label>
      <p v-if="mode==='register'" class="text-xs leading-6 text-[var(--c-muted)]">{{locale.t('passwordRequirements')}}</p>
      <p v-if="error" role="alert" class="auth-error">{{error}}</p>
      <button class="auth-primary" :disabled="busy"><component :is="mode==='register'?UserPlus:LogIn" :size="19"/>{{locale.t(mode==='register'?'createAccount':'signIn')}}</button>
      <button type="button" class="flex min-h-11 w-full items-center justify-center gap-3 rounded-xl border border-[#747775] bg-white px-4 text-sm font-medium text-[#1f1f1f] shadow-sm" data-backend-endpoint="/backend/auth/google/redirect" :disabled="busy" @click="googleInfo"><GoogleGIcon/><span>{{locale.t('google')}}</span></button>
    </form>
    <RouterLink class="mini-action mt-4" to="/admin" @click="emit('close')">{{locale.t('adminOverview')}}</RouterLink>

    <p class="mt-2 text-xs leading-6 text-[var(--c-muted)]">{{locale.t('adminSignInHelp')}}</p>
  </BaseModal>
</template>
