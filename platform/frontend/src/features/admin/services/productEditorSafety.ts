import { computed, ref, type Ref } from 'vue'
import { CustomerSessionApiError } from '@/features/auth/services/customerSessionApi'

export function productFailure(error:unknown):{key:string;block:boolean}{
  if(!(error instanceof CustomerSessionApiError))return {key:'adminProductUncertain',block:true}
  if(error.status===409)return {key:'adminProductConflict',block:true}
  if(error.status>=500)return {key:'adminProductUncertain',block:true}
  if(error.status===413)return {key:'adminImageLimits',block:false}
  if(error.status===429)return {key:'adminProductRateLimit',block:false}
  if(error.status===422){
    const fields=error.fields
    if(fields.includes('code'))return {key:'adminProductCodeInvalid',block:false}
    if(fields.includes('image'))return {key:'adminImageLimits',block:false}
    if(fields.includes('subcategory_id'))return {key:'adminProductCategoryInvalid',block:false}
    if(fields.some(field=>field.startsWith('specifications')))return {key:'adminProductSpecsInvalid',block:false}
    return {key:'adminProductFieldsInvalid',block:false}
  }
  return {key:'adminRequestFailed',block:false}
}

// A baseline belongs to this open form; it never becomes a second persisted product store.
export function useProductDraftGuard(draft:Ref<unknown>,busy:Ref<boolean>,pending?:Ref<unknown>){
  const baseline=ref(''),confirmClose=ref(false)
  const dirty=computed(()=>Boolean(pending?.value)||(draft.value!==null&&JSON.stringify(draft.value)!==baseline.value))
  function checkpoint(){baseline.value=JSON.stringify(draft.value)??'';confirmClose.value=false}
  function requestClose(close:()=>void){
    if(busy.value)return
    if(dirty.value){confirmClose.value=true;return}
    close()
  }
  function discard(close:()=>void){if(!busy.value){confirmClose.value=false;close()}}
  return {dirty,confirmClose,checkpoint,requestClose,discard}
}
