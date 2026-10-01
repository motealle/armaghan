import { onMounted, onUnmounted, ref, watch, type Ref } from 'vue'
import { collectEditableCandidates, type EditableCandidate } from '../selection'

export function useVisualEditorSelection(enabled:Ref<boolean>){
  const selected=ref<EditableCandidate|null>(null)
  const candidates=ref<EditableCandidate[]>([])
  let pointerStart:{x:number;y:number}|null=null
  let listening=false

  function clearSelection(){
    selected.value=null
    candidates.value=[]
  }

  function choose(candidate:EditableCandidate){
    selected.value=candidate
    candidates.value=[]
  }

  function chooseHidden(id:string){
    choose({id,label:`عنصر مخفی · ${id}`,textEditable:false,tag:'hidden'})
  }

  function selectAt(event:PointerEvent){
    const list=collectEditableCandidates(event.clientX,event.clientY,event.width,event.height)
    if(!list.length){
      clearSelection()
      return
    }
    if(list.length===1){
      choose(list[0]!)
      return
    }
    selected.value=null
    candidates.value=list
  }

  function onPointerDown(event:PointerEvent){
    const target=event.target as Element|null
    if(target?.closest('[data-visual-editor-ui]'))return
    pointerStart={x:event.clientX,y:event.clientY}
  }

  function onPointerUp(event:PointerEvent){
    if(!pointerStart)return
    const target=event.target as Element|null
    const start=pointerStart
    pointerStart=null
    if(target?.closest('[data-visual-editor-ui]'))return
    if(Math.hypot(event.clientX-start.x,event.clientY-start.y)>10)return
    event.preventDefault()
    event.stopPropagation()
    selectAt(event)
  }

  function onClickCapture(event:MouseEvent){
    const target=event.target as Element|null
    if(target?.closest('[data-visual-editor-ui]'))return
    event.preventDefault()
    event.stopPropagation()
  }

  function addListeners(){
    if(listening)return
    document.addEventListener('pointerdown',onPointerDown,true)
    document.addEventListener('pointerup',onPointerUp,true)
    document.addEventListener('click',onClickCapture,true)
    listening=true
  }

  function removeListeners(){
    if(!listening)return
    document.removeEventListener('pointerdown',onPointerDown,true)
    document.removeEventListener('pointerup',onPointerUp,true)
    document.removeEventListener('click',onClickCapture,true)
    pointerStart=null
    listening=false
  }

  function clearMarkedTargets(){
    document.querySelectorAll<HTMLElement>('[data-ve-selected]').forEach(element=>{
      delete element.dataset.veSelected
    })
  }

  function markSelected(){
    clearMarkedTargets()
    if(!enabled.value||!selected.value)return
    document.querySelectorAll<HTMLElement>(`[data-style-id="${selected.value.id}"]`).forEach(element=>{
      element.dataset.veSelected='true'
    })
  }

  watch(enabled,value=>{
    if(value)addListeners()
    else{
      removeListeners()
      clearSelection()
      clearMarkedTargets()
    }
  })

  watch(selected,markSelected)

  onMounted(()=>{
    if(enabled.value)addListeners()
    markSelected()
  })

  onUnmounted(()=>{
    removeListeners()
    clearMarkedTargets()
  })

  return{
    selected,
    candidates,
    choose,
    chooseHidden,
    clearSelection,
  }
}
