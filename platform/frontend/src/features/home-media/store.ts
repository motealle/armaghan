import {defineStore} from 'pinia'
import {ref} from 'vue'
import {requestJson} from '@/features/auth/services/customerSessionApi'
export const useHomeMediaStore=defineStore('home-media',()=>{
 const images=ref<Record<string,string>>({})
 const srcsets=ref<Record<string,string>>({})
 const editTarget=ref<string|null>(null)
 const ready=ref(false),failed=ref(false)
 const channel=typeof window!=='undefined'&&window.location.pathname.startsWith('/t/')?'staging':'production'
 let loaded=false
 async function refresh(){
  try{
    const result=await requestJson<{images:Record<string,string>;srcsets?:Record<string,string>}>('/api/home-media/'+channel,{cache:'no-store'})
    images.value=result.images??{}
    srcsets.value=result.srcsets??{}
    ready.value=true
    failed.value=false
  }catch{failed.value=true} // Do not paint obsolete Test26 photographs during a slow/error response.
}
 function ensure(){if(!loaded){loaded=true;void refresh()}}
 function resolve(target:string,fallback:string){return images.value[target]||fallback}
 function srcset(target:string){return srcsets.value[target]}
 function requestEdit(target:string){editTarget.value=target}
 function closeEditor(){editTarget.value=null}
 return{images,srcset,channel,editTarget,ready,failed,ensure,refresh,resolve,requestEdit,closeEditor}
})
