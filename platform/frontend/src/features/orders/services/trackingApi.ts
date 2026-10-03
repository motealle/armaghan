import { requestJson } from '@/features/auth/services/customerSessionApi'
export const orderStages=['inquiry','review','invoice','awaiting_deposit','production','quality','ready','shipped','delivered','cancelled'] as const
export type OrderStage=typeof orderStages[number]
export interface OrderQuote {currency:string;items:{product_code?:string|null;description:string;quantity:number;unit_price_minor:number}[];subtotal_minor:number;shipping_minor:number;discount_minor:number;tax_minor:number;total_minor:number}
export interface TrackedOrder {quote?:OrderQuote|null;documents?:{id:number;kind:string;visible_to_customer:boolean;download_url:string}[];id:number;reference:string;customer_id?:number;customer_name?:string|null;request_path:string;description:string;stage:OrderStage;invoice_confirmed_at:string|null;deposit_confirmed_at:string|null;revision?:string;events:{id:number;action:string;stage:OrderStage;note:string;visible_to_customer:boolean;created_at:string}[]}
export interface OrderPage {orders:TrackedOrder[];page:number;last_page:number}
export const fetchOrders=(admin=false,page=1)=>requestJson<OrderPage>((admin?'/api/admin/orders':'/api/customer/orders')+'?page='+page)
export async function createTrackedOrder(fields:{customer_id?:number;request_path:string;description:string},admin=false):Promise<{order:TrackedOrder}>{
 const bytes=await crypto.subtle.digest('SHA-256',new TextEncoder().encode(JSON.stringify([admin,fields])))
 const fingerprint=Array.from(new Uint8Array(bytes)).map(v=>v.toString(16).padStart(2,'0')).join('')
 const storageKey='armaghan:test29:order-submit:'+fingerprint
 let key:string|null=null
 try{key=sessionStorage.getItem(storageKey)}catch{}
 if(!key||!/^[-a-f0-9]{36}$/.test(key))key=crypto.randomUUID()
 try{sessionStorage.setItem(storageKey,key)}catch{}
 const result=await requestJson<{order:TrackedOrder}>(admin?'/api/admin/orders':'/api/customer/orders',{method:'POST',body:JSON.stringify({...fields,request_key:key})})
 try{sessionStorage.removeItem(storageKey)}catch{}
 return result
}
export const updateTrackedOrder=(order:TrackedOrder,fields:{action:string;stage?:string;note:string;visible_to_customer:boolean})=>requestJson<{order:TrackedOrder}>('/api/admin/orders/'+order.id,{method:'PATCH',body:JSON.stringify({...fields,revision:order.revision})})
export const stageLabels={fa:['درخواست اولیه','بررسی مشخصات','فاکتور','انتظار پیش‌پرداخت','تولید / آماده‌سازی','کنترل کیفیت','آماده ارسال','ارسال‌شده','تحویل‌شده','لغوشده'],en:['Inquiry','Specification review','Invoice','Awaiting deposit','Production / preparation','Quality control','Ready to ship','Shipped','Delivered','Cancelled'],ar:['طلب أولي','مراجعة المواصفات','الفاتورة','انتظار الدفعة المقدمة','الإنتاج / التجهيز','مراقبة الجودة','جاهز للشحن','تم الشحن','تم التسليم','ملغي'],ku:['داواکاری سەرەتایی','پشکنینی تایبەتمەندی','فاکتۆر','چاوەڕوانی پێشەکی','بەرهەمهێنان / ئامادەکردن','پشکنینی کوالێتی','ئامادەی ناردن','نێردراو','گەیەندراو','هەڵوەشاوە']}
