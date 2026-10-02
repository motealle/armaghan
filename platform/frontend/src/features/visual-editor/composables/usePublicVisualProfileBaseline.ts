import { onMounted, onUnmounted } from 'vue'
import { fetchPublicStyleProfile } from '../services/styleProfileApi'
import {
  sanitizeVisualStyleProfile,
  visualProfilesEqual,
  type VisualStyleProfile,
} from '../store'

const BASELINE_KEY='armaghan:test29:visual-style-public-baseline:v1'

interface BaselineRecord{
  checksum:string
  profile:VisualStyleProfile
}

interface VisualStoreLike{
  profile:VisualStyleProfile
  hasOverrides:boolean
  replaceProfile:(value:unknown)=>void
}

function readBaseline():BaselineRecord|null{
  if(typeof localStorage==='undefined')return null
  try{
    const parsed=JSON.parse(localStorage.getItem(BASELINE_KEY)??'null') as Partial<BaselineRecord>|null
    if(!parsed||typeof parsed.checksum!=='string'||!parsed.profile)return null
    return{
      checksum:parsed.checksum,
      profile:sanitizeVisualStyleProfile(parsed.profile),
    }
  }catch{
    return null
  }
}

function writeBaseline(value:BaselineRecord){
  if(typeof localStorage==='undefined')return
  localStorage.setItem(BASELINE_KEY,JSON.stringify(value))
}

export function usePublicVisualProfileBaseline(visual:VisualStoreLike){
  const controller=new AbortController()

  onMounted(async()=>{
    try{
      const data=await fetchPublicStyleProfile(window.location.pathname==='/'?'production':'staging',controller.signal)
      if(!data.checksum||!data.profile)return

      const server=sanitizeVisualStyleProfile({
        schema:1,
        styles:data.styles,
        texts:data.texts,
      })
      const previous=readBaseline()
      const current=sanitizeVisualStyleProfile(visual.profile)
      const safeToAdopt=!visual.hasOverrides||Boolean(previous&&visualProfilesEqual(current,previous.profile))

      if(!safeToAdopt)return

      visual.replaceProfile(server)
      writeBaseline({checksum:data.checksum,profile:server})
    }catch{
      // Test 27 must remain fully usable when the Laravel API is not deployed yet.
    }
  })

  onUnmounted(()=>controller.abort())
}
