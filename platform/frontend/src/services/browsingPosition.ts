import type {Router} from 'vue-router'

type Route={path:string;fullPath:string}
type Position={left:number;top:number}
type Saved={key:string;left:number;top:number}
const SAFE_PATHS=new Set(['/','/products','/production','/favorites','/tracking'])
const storageKey=()=>`armaghan:test29:browsing:${window.location.pathname}`
let cancelPending:undefined|(()=>void)

export function safePositionKey(route:Route):string|null{
  if(!SAFE_PATHS.has(route.path)||route.fullPath.length>1024||route.fullPath.includes('#'))return null
  if(route.path!=='/products')return route.fullPath===route.path?route.path:null
  const url=new URL(route.fullPath,'https://armaghan.invalid')
  if([...url.searchParams.keys()].some(key=>!['category','subcategory','availability','q'].includes(key)))return null
  return route.fullPath
}
function readPositions():Saved[]{
  try{
    const data:unknown=JSON.parse(localStorage.getItem(storageKey())||'[]')
    return Array.isArray(data)?data.filter((x):x is Saved=>!!x&&typeof x.key==='string'&&Number.isFinite(x.top)&&Number.isFinite(x.left)&&x.top>=0&&x.top<=10_000_000&&x.left>=0).slice(-20):[]
  }catch{return[]}
}
export function capturePosition(route:Route):void{
  const key=safePositionKey(route)
  if(!key)return
  try{
    const rows=readPositions().filter(row=>row.key!==key)
    rows.push({key,left:Math.max(0,window.scrollX),top:Math.max(0,window.scrollY)})
    localStorage.setItem(storageKey(),JSON.stringify(rows.slice(-20)))
  }catch{/* Storage restrictions must not prevent navigation. */}
}
export function storedPosition(route:Route):Position|null{
  const key=safePositionKey(route)
  const row=key?readPositions().find(item=>item.key===key):undefined
  return row?{left:row.left,top:row.top}:null
}
export function waitForPosition(route:Route,position:Position):Promise<Position|false>{
  cancelPending?.()
  return new Promise(resolve=>{
    let finished=false
    let resize:ResizeObserver|undefined,mutation:MutationObserver|undefined
    let timer:ReturnType<typeof setTimeout>|undefined
    const finish=(result:Position|false)=>{
      if(finished)return
      finished=true;resize?.disconnect();mutation?.disconnect();if(timer)clearTimeout(timer)
      window.removeEventListener('wheel',cancel);window.removeEventListener('touchstart',cancel);window.removeEventListener('keydown',keyCancel)
      if(cancelPending===cancel)cancelPending=undefined
      resolve(result)
    }
    const cancel=()=>finish(false)
    const keyCancel=(event:KeyboardEvent)=>{if(['ArrowUp','ArrowDown','PageUp','PageDown','Home','End',' '].includes(event.key))cancel()}
    const maximum=()=>Math.max(0,document.documentElement.scrollHeight-window.innerHeight)
    const check=()=>{
      const ready=route.path!=='/products'||!!document.querySelector('.products-page[data-browse-ready="true"]')
      if(ready&&maximum()>=position.top)finish(position)
    }
    cancelPending=cancel
    window.addEventListener('wheel',cancel,{passive:true});window.addEventListener('touchstart',cancel,{passive:true});window.addEventListener('keydown',keyCancel)
    if(typeof ResizeObserver!=='undefined'){resize=new ResizeObserver(check);resize.observe(document.documentElement)}
    if(typeof MutationObserver!=='undefined'){mutation=new MutationObserver(check);mutation.observe(document.documentElement,{childList:true,subtree:true,attributes:true,attributeFilter:['data-browse-ready']})}
    timer=setTimeout(()=>finish({left:position.left,top:Math.min(position.top,maximum())}),20_000)
    check()
  })
}
export function scrollForNavigation(to:Route,initial:boolean,saved:Position|null):Position|Promise<Position|false>{
  const navigation=performance.getEntriesByType('navigation')[0] as PerformanceNavigationTiming|undefined
  const restore=initial&&['reload','back_forward'].includes(navigation?.type||'')?storedPosition(to):null
  const position=saved??restore
  if(position&&safePositionKey(to)&&position.top>0)return waitForPosition(to,position)
  return {left:0,top:0}
}
export function installPositionPersistence(router:Router):void{
  const save=()=>{if(!cancelPending)capturePosition(router.currentRoute.value)}
  // Save once when leaving/backgrounding rather than writing storage on every scroll frame.
  window.addEventListener('pagehide',save)
  document.addEventListener('visibilitychange',()=>{if(document.visibilityState==='hidden')save()})
  router.beforeEach((_to,from)=>{
    cancelPending?.()
    if(from.matched.length)capturePosition(from)
  })
}
