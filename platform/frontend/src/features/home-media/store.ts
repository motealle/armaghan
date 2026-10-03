import {defineStore} from 'pinia'
import {ref} from 'vue'
import {requestJson} from '@/features/auth/services/customerSessionApi'
export const useHomeMediaStore=defineStore('home-media',()=>{
 const images=ref<Record<string,string>>({})
 const channel=typeof window!=='undefined'&&window.location.pathname.startsWith('/t/')?'staging':'production'
 let loaded=false
 async function refresh(){try{images.value=(await requestJson<{images:Record<string,string>}>('/api/home-media/'+channel)).images}catch{/* Retain approved local media when public service is unavailable. */}}
 function ensure(){if(!loaded){loaded=true;void refresh()}}
 function resolve(target:string,fallback:string){return images.value[target]||fallback}
 return{images,channel,ensure,refresh,resolve}
})
