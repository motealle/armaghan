/** Keep shared-host photo requests from starting in a viewport-sized burst. */
const waiting:Array<{start:()=>void;active:boolean;cancelled:boolean}>=[]
let running=0
function pump(){
  while(running<2&&waiting.length){
    const job=waiting.shift()!
    if(job.cancelled)continue
    job.active=true;running++;job.start()
  }
}
export function acquirePhotoSlot(start:()=>void){
  const job={start,active:false,cancelled:false}
  waiting.push(job);pump()
  return ()=>{
    if(job.cancelled)return
    job.cancelled=true
    if(job.active)running--
    pump()
  }
}
