import { requestJson } from '@/features/auth/services/customerSessionApi'
export interface AdminIdentity { name:string; email:string; is_owner?:boolean }
export interface AdminCustomer {
  id:number; name:string|null; email:string|null; company_name:string|null; whatsapp:string|null;
  country_code:string|null; country_name:string|null; notes:string|null; priority:number;
  active:boolean; direct_link_enabled:boolean; revision:string;
}
export type CustomerFields=Pick<AdminCustomer,'company_name'|'whatsapp'|'country_code'|'country_name'|'notes'|'priority'|'active'|'direct_link_enabled'>
export interface CustomerPage { customers:AdminCustomer[]; page:number; last_page:number; total:number }
export const fetchAdminSession=()=>requestJson<{admin:AdminIdentity}>('/api/admin/session')
export const logoutAdmin=()=>requestJson('/api/admin/logout',{method:'POST',body:'{}'})
export const fetchAdminCustomers=(page=1,search='')=>requestJson<CustomerPage>('/api/admin/customers?page='+page+'&search='+encodeURIComponent(search))
export const createAdminCustomer=(fields:CustomerFields)=>requestJson<{customer:AdminCustomer}>('/api/admin/customers',{method:'POST',body:JSON.stringify(fields)})
export const updateAdminCustomer=(id:number,fields:CustomerFields,revision:string)=>requestJson<{customer:AdminCustomer}>('/api/admin/customers/'+id,{method:'PATCH',body:JSON.stringify({...fields,revision})})

export interface AdminMedia { id:number; url:string; thumb_url:string }
export interface ProductFields {
  subcategory_id:number; code:string; name_fa:string; name_ar:string|null; name_en:string|null; name_ku:string|null;
  availability:'available'|'unavailable'|'made_to_order'; active:boolean; sort_order:number;
}
export interface AdminProduct extends ProductFields { id:number; revision:string; category_code:string; subcategory_code:string; media:AdminMedia[] }
export interface AdminSubcategory { id:number; code:string; name:string; category_code:string; category_name:string; active:boolean }
export interface ProductPage { products:AdminProduct[]; page:number; last_page:number; total:number }
export const fetchProductTaxonomy=()=>requestJson<{subcategories:AdminSubcategory[]}>('/api/admin/product-taxonomy')
export const fetchAdminProducts=(page=1,search='',subcategory='',perPage=25)=>requestJson<ProductPage>('/api/admin/products?page='+page+'&search='+encodeURIComponent(search)+'&subcategory_id='+encodeURIComponent(subcategory)+'&per_page='+perPage)
export const createAdminProduct=(fields:ProductFields)=>requestJson<{product:AdminProduct}>('/api/admin/products',{method:'POST',body:JSON.stringify(fields)})
export const updateAdminProduct=(id:number,fields:ProductFields,revision:string)=>requestJson<{product:AdminProduct}>('/api/admin/products/'+id,{method:'PATCH',body:JSON.stringify({...fields,revision})})
export function uploadAdminProductImage(product:AdminProduct,file:File){
  const body=new FormData();body.append('image',file);body.append('revision',product.revision)
  return requestJson<{product:AdminProduct}>('/api/admin/products/'+product.id+'/images',{method:'POST',body})
}
export const orderAdminProductImages=(product:AdminProduct,media_ids:number[])=>requestJson<{product:AdminProduct}>('/api/admin/products/'+product.id+'/images/order',{method:'PUT',body:JSON.stringify({revision:product.revision,media_ids})})

export interface AdminUser { id:number; name:string; email:string; role:'admin'|'customer'; active:boolean; is_owner:boolean; protected:boolean; revision:string }
export interface UserPage { users:AdminUser[]; page:number; last_page:number; total:number }
export const fetchAdminUsers=(page=1,search='')=>requestJson<UserPage>('/api/admin/users?page='+page+'&search='+encodeURIComponent(search))
export const createAdminUser=(fields:{name:string;email:string;role:string;active:boolean;password:string;password_confirmation:string})=>requestJson<{user:AdminUser}>('/api/admin/users',{method:'POST',body:JSON.stringify(fields)})
export const updateAdminUser=(user:AdminUser,fields:{name:string;role:string;active:boolean})=>requestJson<{user:AdminUser}>('/api/admin/users/'+user.id,{method:'PATCH',body:JSON.stringify({...fields,revision:user.revision})})
