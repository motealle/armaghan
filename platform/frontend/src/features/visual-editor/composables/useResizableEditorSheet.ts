import { onMounted, onUnmounted, ref, watch, type Ref } from 'vue'

interface ResizeState{
  pointerId:number
  startY:number
  startHeight:number
}

function clampHeight(value:number):number{
  const min=Math.min(190,window.innerHeight*.34)
  const max=Math.max(min,window.innerHeight*.78)
  return Math.min(max,Math.max(min,value))
}

export function useResizableEditorSheet(enabled:Ref<boolean>){
  const height=ref(0)
  let resizeState:ResizeState|null=null

  function setDefaultHeight(){
    height.value=clampHeight(window.innerHeight*.48)
  }

  function applyFrame(){
    if(!enabled.value){
      document.documentElement.classList.remove('visual-editor-active')
      document.documentElement.style.removeProperty('--visual-editor-sheet-height')
      return
    }
    document.documentElement.classList.add('visual-editor-active')
    document.documentElement.style.setProperty('--visual-editor-sheet-height',`${height.value}px`)
  }

  function onResizeStart(event:PointerEvent){
    event.preventDefault()
    resizeState={pointerId:event.pointerId,startY:event.clientY,startHeight:height.value}
    ;(event.currentTarget as HTMLElement).setPointerCapture(event.pointerId)
  }

  function onResizeMove(event:PointerEvent){
    if(!resizeState||resizeState.pointerId!==event.pointerId)return
    height.value=clampHeight(resizeState.startHeight+(resizeState.startY-event.clientY))
  }

  function onResizeEnd(event:PointerEvent){
    if(!resizeState||resizeState.pointerId!==event.pointerId)return
    resizeState=null
    const handle=event.currentTarget as HTMLElement
    if(handle.hasPointerCapture(event.pointerId))handle.releasePointerCapture(event.pointerId)
  }

  watch(enabled,applyFrame)
  watch(height,applyFrame)

  onMounted(()=>{
    setDefaultHeight()
    window.addEventListener('resize',setDefaultHeight)
    applyFrame()
  })

  onUnmounted(()=>{
    window.removeEventListener('resize',setDefaultHeight)
    document.documentElement.classList.remove('visual-editor-active')
    document.documentElement.style.removeProperty('--visual-editor-sheet-height')
  })

  return{
    height,
    onResizeStart,
    onResizeMove,
    onResizeEnd,
  }
}
