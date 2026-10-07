/** A failed public photo is retried twice, never cached as a permanent placeholder. */
export function createImageRecovery(retry:(url:string)=>void) {
  let attempts=0
  let timer:ReturnType<typeof setTimeout>|undefined
  function cancel(){if(timer!==undefined)clearTimeout(timer);timer=undefined}
  function fresh(source:string,attempt:number){
    const clean=source.replace(/([?&])image_retry=[^&]*(&?)/,(_,start,end)=>end?start:'').replace(/[?&]$/,'')
    return clean+(clean.includes('?')?'&':'?')+'image_retry='+Date.now().toString(36)+'-'+attempt
  }
  return {
    failed(source:string){
      if(timer!==undefined)return true
      if(attempts>=2||!source.includes('/backend/api/catalog/media/'))return false
      const attempt=++attempts
      timer=setTimeout(()=>{
        timer=undefined
        retry(fresh(source,attempt))
      },attempt===1?1500:4000)
      return true
    },
    reset(){cancel();attempts=0},
    retryNow(source:string){cancel();attempts=0;retry(fresh(source,0))},
    cancel,
  }
}
