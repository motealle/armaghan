import { requestJson } from '@/features/auth/services/customerSessionApi'
export interface AdminIdentity { name:string; email:string }
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
