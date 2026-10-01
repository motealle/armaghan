export interface EditableCandidate{
  id:string
  label:string
  textEditable:boolean
  tag:string
}

function editableFrom(element:Element|null):EditableCandidate|null{
  if(!element)return null
  const editable=element.closest<HTMLElement>('[data-style-id]')
  if(!editable||editable.closest('[data-visual-editor-ui]'))return null
  const id=editable.dataset.styleId?.trim()
  if(!id||!/^[a-z0-9._-]+$/i.test(id))return null
  return{
    id,
    label:editable.dataset.styleLabel?.trim()||id,
    textEditable:editable.dataset.editableText==='true',
    tag:editable.tagName.toLowerCase(),
  }
}

export function collectEditableCandidates(
  x:number,
  y:number,
  contactWidth=0,
  contactHeight=0,
):EditableCandidate[]{
  const radius=Math.min(22,Math.max(10,Math.ceil(Math.max(contactWidth,contactHeight)/2)))
  const offsets:[number,number][]=[
    [0,0],
    [-radius,0],[radius,0],[0,-radius],[0,radius],
    [-radius,-radius],[radius,-radius],[-radius,radius],[radius,radius],
  ]
  const seen=new Set<string>()
  const result:EditableCandidate[]=[]

  for(const [dx,dy] of offsets){
    for(const element of document.elementsFromPoint(x+dx,y+dy)){
      let current:Element|null=element
      while(current){
        const candidate=editableFrom(current)
        if(candidate&&!seen.has(candidate.id)){
          seen.add(candidate.id)
          result.push(candidate)
          if(result.length>=8)return result
        }
        current=current.parentElement
      }
    }
  }
  return result
}
