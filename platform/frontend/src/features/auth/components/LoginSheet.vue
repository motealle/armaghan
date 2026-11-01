<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { LogIn, UserPlus, ShieldCheck } from '@lucide/vue'
import { useRouter } from 'vue-router'
import { useSessionStore } from '@/stores/session'
import { useAdminStore } from '@/features/admin/store'
import { useLocaleStore } from '@/stores/locale'
import { registerWithPassword, registerWithPhone, signInWithPassword, CustomerSessionApiError } from '@/features/auth/services/customerSessionApi'
import BaseModal from '@/components/ui/BaseModal.vue'
import { phoneIdentifier } from '../services/simpleLogin'
import GoogleGIcon from '@/components/icons/GoogleGIcon.vue'

const props=defineProps<{open:boolean}>()
const emit=defineEmits<{close:[]}>()
const session=useSessionStore()
const admin=useAdminStore()
const locale=useLocaleStore()
const router=useRouter()
const mode=ref<'signin'|'register'>('signin')
const email=ref('')
const username=ref('')
const phone=ref('')
const dialCode=ref('+964')
const phoneSignIn=ref(true)
const signupMethod=ref<'phone'|'email'>('phone')
const showPassword=ref(false)
const password=ref('')
const confirmation=ref('')
const name=ref('')
const error=ref('')
const busy=ref(false)
const title=computed(()=>locale.t(mode.value==='register'?'register':'loginTitle'))
watch(()=>props.open,()=>{mode.value='signin';signupMethod.value='phone';error.value='';password.value='';confirmation.value='';showPassword.value=false})
function fullPhone(){return phoneIdentifier(phone.value,dialCode.value)}
async function submit(){
  if(busy.value)return
  error.value='';busy.value=true
  try{
    if(mode.value==='register'){
      if(signupMethod.value==='phone'){
        await registerWithPhone(username.value.trim(),fullPhone(),password.value)
      }else{
        await registerWithPassword(name.value.trim(),email.value.trim(),password.value,confirmation.value)
      }
    }else{
      const result=await signInWithPassword(phoneSignIn.value?fullPhone():email.value.trim(),password.value)
      if(result.redirect==='/backend/admin'){await admin.hydrate();password.value='';emit('close');router.push('/admin');return}
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
      <div v-if="mode==='register'" class="flex justify-center">
        <button type="button" class="text-sm underline underline-offset-4" :disabled="busy" @click="signupMethod=signupMethod==='phone'?'email':'phone';error=''">
          {{locale.t(signupMethod==='phone'?'registerWithEmail':'registerWithPhone')}}
        </button>
      </div>
      <template v-if="(mode==='register'&&signupMethod==='phone')||(mode==='signin'&&phoneSignIn)">
        <label class="form-field">{{locale.t('mobilePhone')}}
          <span class="flex gap-2" dir="ltr">
            <input v-model="dialCode" list="phone-countries" class="!w-24 !shrink-0 !flex-none" type="tel" inputmode="tel" autocomplete="tel-country-code" maxlength="6" :aria-label="locale.t('callingCode')" :disabled="busy" required>
            <datalist id="phone-countries"><option value="+964">العراق</option><option value="+98">ایران</option><option value="+971">الإمارات</option><option value="+90">Türkiye</option></datalist>
            <input v-model="phone" type="tel" inputmode="tel" :autocomplete="mode==='signin'?'username':'tel-national'" dir="ltr" placeholder="07… / 09…" maxlength="30" required :disabled="busy">
          </span>
        </label>
        <p class="text-xs text-[var(--c-muted)]">{{locale.t('simplePhoneHelp')}}</p>
        <details v-if="mode==='register'" class="text-xs"><summary>{{locale.t('optionalUsername')}}</summary><label class="form-field mt-2">{{locale.t('username')}}<input v-model="username" autocomplete="username" maxlength="32" :disabled="busy"></label></details>
      </template>
      <template v-if="mode==='register'&&signupMethod==='email'">
        <label class="form-field">{{locale.t('fullName')}}<input v-model="name" autocomplete="name" maxlength="255" required :disabled="busy"></label>
        <label class="form-field">{{locale.t('email')}}<input v-model="email" type="email" autocomplete="email" dir="ltr" maxlength="255" required :disabled="busy"></label>
      </template>
      <label v-if="mode==='signin'&&!phoneSignIn" class="form-field">{{locale.t('loginIdentifier')}}<input v-model="email" type="text" autocomplete="username" dir="ltr" maxlength="255" required :disabled="busy"></label>
      <button v-if="mode==='signin'" type="button" class="text-xs underline underline-offset-4" :disabled="busy" @click="phoneSignIn=!phoneSignIn;error=''">{{locale.t(phoneSignIn?'useOtherLogin':'usePhoneLogin')}}</button>
      <label class="form-field">{{locale.t('password')}}
        <span class="flex items-center gap-2">
          <input v-model="password" :type="showPassword?'text':'password'" :autocomplete="mode==='register'?'new-password':'current-password'" dir="ltr" :minlength="mode==='register'?(signupMethod==='phone'?4:12):undefined" maxlength="255" required :disabled="busy">
          <button type="button" class="mini-action shrink-0" :disabled="busy" @click="showPassword=!showPassword">{{locale.t(showPassword?'hidePassword':'showPassword')}}</button>
        </span>
      </label>
      <label v-if="mode==='register'&&signupMethod==='email'" class="form-field">{{locale.t('confirmPassword')}}<input v-model="confirmation" type="password" autocomplete="new-password" dir="ltr" required :disabled="busy"></label>
      <p v-if="mode==='register'" class="text-xs leading-6 text-[var(--c-muted)]">{{locale.t(signupMethod==='phone'?'phonePasswordRequirements':'passwordRequirements')}}</p>
      <p v-if="error" role="alert" class="auth-error">{{error}}</p>
      <button class="auth-primary" :disabled="busy"><component :is="mode==='register'?UserPlus:LogIn" :size="19"/>{{locale.t(mode==='register'?'createAccount':'signIn')}}</button>
    </form>
    <details class="mt-4 text-xs"><summary>{{locale.t('otherLoginMethods')}}</summary>
      <button type="button" class="flex min-h-11 w-full items-center justify-center gap-3 rounded-xl border border-[#747775] bg-white px-4 text-sm font-medium text-[#1f1f1f] shadow-sm" data-backend-endpoint="/backend/auth/google/redirect" :disabled="busy" @click="googleInfo"><GoogleGIcon/><span>{{locale.t('google')}}</span></button>
    <RouterLink class="mini-action mt-3" to="/admin" @click="emit('close')">{{locale.t('adminOverview')}}</RouterLink>
    </details>
  </BaseModal>
</template>
