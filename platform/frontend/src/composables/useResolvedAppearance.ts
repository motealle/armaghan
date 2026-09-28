import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { currentViewportProfile, subscribeViewportProfile } from '@/services/viewportProfile'
import { useAppearanceStore } from '@/stores/appearance'

export function useResolvedAppearance(){
  const appearance=useAppearanceStore()
  const profile=ref(currentViewportProfile())
  let unsubscribe:()=>void=()=>{}

  onMounted(()=>{unsubscribe=subscribeViewportProfile(value=>{profile.value=value})})
  onBeforeUnmount(()=>unsubscribe())

  const policy=computed(()=>appearance.resolved(profile.value))
  return{profile,policy}
}
