<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { ClipboardCopy, Globe2, KeyRound, LogIn, UserPlus } from '@lucide/vue'
import { useRouter } from 'vue-router'
import { useSessionStore } from '@/stores/session'
import { useCustomersStore } from '@/stores/customers'
import { useLocaleStore } from '@/stores/locale'
import BaseModal from '@/components/ui/BaseModal.vue'

const props=defineProps<{open:boolean}>()
const emit=defineEmits<{close:[]}>()
const session=useSessionStore()
const customers=useCustomersStore()
const locale=useLocaleStore()
const router=useRouter()
const mode=ref<'signin'|'register'|'magic'>('signin')
const username=ref('')
const password=ref('')
const name=ref('')
const email=ref('')
const error=ref('')
const message=ref('')
const magicUrl=ref('')

const title=computed(()=>mode.value==='register'?locale.t('register'):mode.value==='magic'?locale.t('magicLink'):locale.t('loginTitle'))

watch(()=>props.open,(open)=>{
  if(open){mode.value='signin';error.value='';message.value='';magicUrl.value=''}
})

function complete(){
  emit('close')
  router.push('/tracking')
}
function submit(){
  error.value='';message.value=''
  if(!session.login(username.value.trim(),password.value)){
    error.value=locale.t('invalidLogin');return
  }
  complete()
}
function register(){
  error.value='';message.value=''
  const nextCustomerId=Math.max(0,...customers.items.map(item=>item.id))+1
  const result=session.register(name.value,email.value,password.value,nextCustomerId)
  if(!result.ok){
    error.value=result.reason==='exists'?locale.t('invalidLogin'):locale.t('registerHelp')
    return
  }
  customers.add({name:name.value.trim(),email:email.value.trim().toLowerCase(),whatsapp:''})
  complete()
}
function makeMagic(){
  error.value='';message.value=''
  if(!email.value.includes('@')){error.value=locale.t('invalidLogin');return}
  magicUrl.value=session.createMagicLink(email.value,'expiring',72)
  message.value=locale.t('magicLinkReady')
}
async function copyMagic(){
  if(!magicUrl.value)return
  try{await navigator.clipboard.writeText(magicUrl.value);message.value=locale.t('magicLinkReady')}catch{}
}
function googleInfo(){
  error.value=locale.t('googleBackendRequired')
}
</script>

<template>
  <BaseModal :open="open" :title="title" @close="emit('close')">
    <div class="auth-tabs" role="tablist">
      <button :class="{active:mode==='signin'}" @click="mode='signin';error='';message=''">{{locale.t('signIn')}}</button>
      <button :class="{active:mode==='register'}" @click="mode='register';error='';message=''">{{locale.t('register')}}</button>
      <button :class="{active:mode==='magic'}" @click="mode='magic';error='';message=''">{{locale.t('magicLink')}}</button>
    </div>

    <form v-if="mode==='signin'" class="mt-4 space-y-4" @submit.prevent="submit">
      <label class="form-field">{{locale.t('username')}}<input v-model="username" autocomplete="username" autofocus/></label>
      <label class="form-field">{{locale.t('password')}}<input v-model="password" type="password" autocomplete="current-password"/></label>
      <p v-if="error" class="auth-error">{{error}}</p>
      <button class="auth-primary"><LogIn :size="19"/>{{locale.t('signIn')}}</button>
      <button type="button" class="modal-secondary-action w-full" data-backend-endpoint="/auth/google/redirect" @click="googleInfo">
        <Globe2 :size="17"/>{{locale.t('google')}}
      </button>
    </form>

    <form v-else-if="mode==='register'" class="mt-4 space-y-4" @submit.prevent="register">
      <p class="text-xs leading-6 text-[var(--c-muted)]">{{locale.t('registerHelp')}}</p>
      <label class="form-field">{{locale.t('fullName')}}<input v-model="name" autocomplete="name" autofocus/></label>
      <label class="form-field">{{locale.t('email')}}<input v-model="email" type="email" autocomplete="email"/></label>
      <label class="form-field">{{locale.t('password')}}<input v-model="password" type="password" autocomplete="new-password"/></label>
      <p v-if="error" class="auth-error">{{error}}</p>
      <button class="auth-primary"><UserPlus :size="19"/>{{locale.t('createAccount')}}</button>
    </form>

    <form v-else class="mt-4 space-y-4" @submit.prevent="makeMagic">
      <p class="text-xs leading-6 text-[var(--c-muted)]">{{locale.t('registerHelp')}}</p>
      <label class="form-field">{{locale.t('email')}}<input v-model="email" type="email" autocomplete="email" autofocus/></label>
      <p v-if="error" class="auth-error">{{error}}</p>
      <p v-if="message" class="auth-success">{{message}}</p>
      <button class="auth-primary"><KeyRound :size="19"/>{{locale.t('generateLink')}}</button>
      <div v-if="magicUrl" class="magic-link-box">
        <input :value="magicUrl" readonly dir="ltr"/>
        <button type="button" :aria-label="locale.t('copy')" @click="copyMagic"><ClipboardCopy :size="18"/></button>
      </div>
    </form>
  </BaseModal>
</template>
