<script setup lang="ts">
import {inputDigits} from '@/features/auth/services/simpleLogin'
import AdaptivePanel from '@/components/ui/AdaptivePanel.vue'
import OrderProgress from './OrderProgress.vue'
import CustomerOrderDetails from './CustomerOrderDetails.vue'
import {useRoute} from 'vue-router'
import {Eye} from '@lucide/vue'
import OrderCommercialPanel from './OrderCommercialPanel.vue'
import OrderCustomerPicker from './OrderCustomerPicker.vue'
import {trackingCopy} from '../services/trackingCopy'
import {computed,onMounted,onBeforeUnmount,ref,watch} from 'vue'
import {useAdminStore} from '@/features/admin/store'
import {useLocaleStore} from '@/stores/locale'
import {CustomerSessionApiError} from '@/features/auth/services/customerSessionApi'
import type {AdminCustomer} from '@/features/admin/services/adminApi'
import {createTrackedOrder,fetchOrders,fetchCustomerOrderView,updateTrackedOrder,orderStages,stageLabels,type TrackedOrder,type OrderPage} from '../services/trackingApi'
import {requestPathTitle} from '@/services/whatsapp'
import type {RequestPath} from '@/types/domain'
const props=defineProps<{admin?:boolean;initialCustomer?:AdminCustomer|null;startCreate?:boolean}>(),locale=useLocaleStore(),adminIdentity=useAdminStore()
const route=useRoute()
const customerFilter=ref(props.initialCustomer?.id),reference=ref(typeof route.query.order==='string'&&/^AT-[A-Z0-9]+$/i.test(route.query.order)?route.query.order.toUpperCase():'')
const previewOpen=ref(false),previewBusy=ref(false),previewError=ref(''),previewOrder=ref<TrackedOrder|null>(null)
let previewRequest=0,loadRequest=0
async function preview(row:TrackedOrder){const request=++previewRequest;previewOpen.value=true;previewOrder.value=null;previewBusy.value=true;previewError.value='';try{const response=await fetchCustomerOrderView(row.id);if(request===previewRequest&&adminIdentity.identity)previewOrder.value=response.order}catch{if(request===previewRequest)previewError.value=copy.value.previewFailed}finally{if(request===previewRequest)previewBusy.value=false}}
function closePreview(){previewRequest++;previewOpen.value=false;previewOrder.value=null;previewBusy.value=false;previewError.value=''}
watch(()=>adminIdentity.identity,value=>{if(!value){closePreview();loadRequest++;page.value={orders:[],page:1,last_page:1}}})
onBeforeUnmount(()=>{previewRequest++;loadRequest++})
const copy=computed(()=>trackingCopy[locale.locale]),success=ref('')
const page=ref<OrderPage>({orders:[],page:1,last_page:1}),busy=ref(false),error=ref(''),selected=ref<number|null>(null),createOpen=ref(!!props.initialCustomer&&props.startCreate!==false)
const customer=ref<AdminCustomer|null>(props.initialCustomer??null),description=ref(''),path=ref<RequestPath>('simple')
const note=ref(''),stage=ref('review'),visible=ref(false),action=ref('note')
const words=computed(()=>locale.locale==='fa'?{title:'سفارش‌ها و خط زمانی',empty:'هنوز سفارشی برای پیگیری ثبت نشده است.',create:'ثبت درخواست جدید',note:'توضیح مرحله',visible:'نمایش این توضیح به مشتری',invoice:'تأیید دستی فاکتور',deposit:'تأیید دستی پیش‌پرداخت',stage:'تغییر مرحله',manual:'وضعیت و تأییدها دستی ثبت می‌شوند؛ تأیید پیش‌پرداخت به معنی بررسی بانکی خودکار نیست.',private:'یادداشت داخلی',registered:'ثبت درخواست برای پیگیری'}:locale.locale==='ar'?{title:'الطلبات والخط الزمني',empty:'لا توجد طلبات مسجلة.',create:'طلب جديد',note:'ملاحظة المرحلة',visible:'إظهار الملاحظة للعميل',invoice:'تأكيد الفاتورة يدوياً',deposit:'تأكيد الدفعة يدوياً',stage:'تغيير المرحلة',manual:'التأكيدات يدوية ولا تعني تحققاً مصرفياً آلياً.',private:'ملاحظة داخلية',registered:'تسجيل الطلب'}:locale.locale==='ku'?{title:'داواکاری و هێڵی کات',empty:'هێشتا داواکاری تۆمار نەکراوە.',create:'داواکاری نوێ',note:'تێبینی قۆناغ',visible:'پیشاندانی تێبینی بۆ کڕیار',invoice:'پشتڕاستکردنەوەی فاکتۆر بە دەست',deposit:'پشتڕاستکردنەوەی پێشەکی بە دەست',stage:'گۆڕینی قۆناغ',manual:'پشتڕاستکردنەوەکان بە دەستە و پشکنینی بانکی نییە.',private:'تێبینی ناوخۆیی',registered:'تۆمارکردنی داواکاری'}:{title:'Orders & timeline',empty:'No orders registered yet.',create:'New request',note:'Stage note',visible:'Show this note to the customer',invoice:'Manually confirm invoice',deposit:'Manually confirm deposit',stage:'Change stage',manual:'Confirmations are manual; deposit confirmation is not an automated bank check.',private:'Internal note',registered:'Register request'})
const paths:RequestPath[]=['simple','available','unavailable','custom','brand','packaging']
function confirmation(value:string|null){return value?'✓ '+new Date(value).toLocaleDateString(locale.locale==='ku'?'ar':locale.locale):'—'}
function label(value:string){return stageLabels[locale.locale][orderStages.indexOf(value as typeof orderStages[number])]??value}
function fail(e:unknown){error.value=locale.t(e instanceof CustomerSessionApiError&&e.status===409?'adminConflict':e instanceof CustomerSessionApiError&&e.status===403?'adminPermissionDenied':'adminRequestFailed')}
async function load(number=1){reference.value=inputDigits(reference.value.trim()).toUpperCase();const request=++loadRequest;busy.value=true;error.value='';try{const response=await fetchOrders(!!props.admin,number,{customerId:customerFilter.value,reference:reference.value});if(request===loadRequest){page.value=response;if(reference.value&&response.orders[0])selected.value=response.orders[0].id}}catch(e){if(request===loadRequest){page.value={orders:[],page:1,last_page:1};fail(e)}}finally{if(request===loadRequest)busy.value=false}}
function allOrders(){customerFilter.value=undefined;reference.value='';createOpen.value=false;selected.value=null;load()}
watch(()=>route.query.order,value=>{reference.value=typeof value==='string'&&/^AT-[A-Z0-9]+$/i.test(value)?value.toUpperCase():'';selected.value=null;load()})
function open(row:TrackedOrder){selected.value=selected.value===row.id?null:row.id;note.value='';action.value='note';stage.value=['cancelled','delivered'].includes(row.stage)?'review':orderStages[Math.min(orderStages.indexOf(row.stage)+1,8)]!;visible.value=false;error.value=''}
async function create(){if(busy.value||description.value.trim().length<3||props.admin&&!customer.value?.active)return;busy.value=true;error.value='';success.value='';try{const created=await createTrackedOrder({request_path:path.value,description:description.value.trim(),...(props.admin?{customer_id:customer.value!.id}:{})},!!props.admin);description.value='';createOpen.value=false;selected.value=created.order.id;note.value='';action.value='note';stage.value='review';visible.value=false;success.value=props.admin?copy.value.success:copy.value.customerSuccess;reference.value='';if(props.admin)customerFilter.value=created.order.customer_id;await load()}catch(e){fail(e)}finally{busy.value=false}}
async function save(row:TrackedOrder){if(busy.value||note.value.trim().length<3)return;busy.value=true;error.value='';try{const result=await updateTrackedOrder(row,{action:action.value,...(['stage','stage_correction'].includes(action.value)?{stage:stage.value}:{}),note:note.value.trim(),visible_to_customer:visible.value});page.value.orders=page.value.orders.map(o=>o.id===row.id?result.order:o);note.value='';action.value='note';visible.value=false;stage.value=orderStages[Math.min(orderStages.indexOf(result.order.stage)+1,8)]!}catch(e){fail(e)}finally{busy.value=false}}
onMounted(()=>load())
</script>
<template>
<section class="space-y-3" :aria-busy="busy">
  <header class="flex flex-wrap items-center gap-2"><h2 class="text-lg font-black">{{admin?words.title:copy.myOrders}}</h2><button class="mini-action ms-auto" :disabled="busy" @click="createOpen=!createOpen">{{words.create}}</button><button class="mini-action" :disabled="busy" @click="load(page.page)">{{locale.t('adminReload')}}</button></header>
  <form v-if="admin" class="flex flex-wrap items-end gap-2" @submit.prevent="load()"><label class="form-field min-w-0 flex-1">{{copy.reference}}<input v-model="reference" dir="ltr" maxlength="40" placeholder="AT-…" :disabled="busy"></label><button class="mini-action" :disabled="busy">{{locale.t('searchLabel')}}</button><button v-if="customerFilter||reference" type="button" class="mini-action" :disabled="busy" @click="allOrders">{{copy.allOrders}}</button></form>
  <p v-if="admin&&customerFilter" class="text-sm font-bold">{{copy.customerOrders}}: {{customer?.id===customerFilter?(customer.company_name||customer.name||customer.phone||customer.whatsapp):'#'+customerFilter}} · #{{customerFilter}}</p>
  <div class="admin-surface space-y-2 rounded-2xl p-4 text-sm leading-7">
    <p>{{admin?copy.adminHelp:copy.customerHelp}}</p>
    <details v-if="admin"><summary class="cursor-pointer font-bold">{{copy.guide}}</summary><ol class="list-inside list-decimal space-y-1"><li v-for="step in copy.steps" :key="step">{{step}}</li></ol><p class="mt-2">{{copy.visibility}}</p><p class="mt-2">{{copy.payment}}</p><p class="text-xs text-[var(--c-muted)]">{{words.manual}}</p></details>
    <p v-else class="text-xs text-[var(--c-muted)]">{{copy.submitHelp}}</p>
  </div>
  <p v-if="success" role="status" class="rounded-xl bg-[var(--c-surface-2)] p-3 text-sm font-bold">{{success}}</p>
  <form v-if="createOpen" class="admin-surface space-y-3 rounded-2xl p-4" @submit.prevent="create">
    <OrderCustomerPicker v-if="admin" v-model="customer" :disabled="busy"/>
    <label class="form-field">{{locale.t('whatsappPath')}}<select v-model="path" :disabled="busy"><option v-for="item in paths" :key="item" :value="item">{{requestPathTitle(item,locale.locale)}}</option></select></label>
    <label class="form-field">{{locale.locale==='fa'?'شرح درخواست (قابل مشاهده برای مشتری)':locale.locale==='ar'?'وصف الطلب (ظاهر للعميل)':locale.locale==='ku'?'وەسفی داواکاری (بۆ کڕیار)':'Request description (visible to customer)'}}<textarea v-model="description" required minlength="3" maxlength="4000" rows="3" :disabled="busy"/></label><button class="mini-action" :disabled="busy||(admin&&!customer?.active)||description.trim().length<3">{{words.registered}}</button>
  </form>
  <p v-if="error" class="auth-error" role="alert">{{error}}</p><p v-if="busy" role="status">{{locale.t('adminLoading')}}</p>
  <p v-if="!busy&&!error&&!page.orders.length" class="admin-surface rounded-2xl p-4 text-sm">{{words.empty}}</p>
  <article v-for="order in page.orders" :key="order.id" class="admin-surface rounded-2xl p-4">
    <button class="flex min-h-11 w-full items-center justify-between gap-2 text-start" :disabled="busy" :aria-expanded="selected===order.id" @click="open(order)"><b dir="ltr">{{order.reference}}</b><b>{{label(order.stage)}}</b></button>
    <button v-if="admin" type="button" class="mini-action my-2 order-customer-preview-action" :disabled="busy" @click="preview(order)"><Eye :size="18" aria-hidden="true"/>{{copy.preview}}</button>
    <p v-if="admin" class="mb-2 text-sm font-bold">{{locale.t('customerLabel')}}: {{order.customer_name||'—'}} · #{{order.customer_id}}</p><p class="text-sm">{{order.description}}</p><p v-if="admin" class="mt-2 text-xs">{{words.invoice}}: {{confirmation(order.invoice_confirmed_at)}} · {{words.deposit}}: {{confirmation(order.deposit_confirmed_at)}}</p>
    <div v-if="selected===order.id" class="mt-3 space-y-3">
      <CustomerOrderDetails v-if="!admin" :order="order"/>
      <OrderProgress v-else :order="order" admin/>
      <form v-if="admin" class="space-y-2 border-t border-[var(--c-border)] pt-3" @submit.prevent="save(order)">
        <p class="text-xs leading-6 text-[var(--c-muted)]">{{copy.visibility}}</p>
        <p v-if="['stage','stage_correction'].includes(action)" class="text-xs leading-6">{{copy.payment}}</p>
        <label class="form-field">{{locale.t('actions')}}<select :aria-label="locale.t('actions')" v-model="action" :disabled="busy"><option value="note">{{words.note}}</option><option v-if="!['cancelled','delivered'].includes(order.stage)||adminIdentity.identity?.is_owner" value="stage_correction">{{locale.locale==='fa'?'اصلاح دستی مرحله با ذکر دلیل':locale.locale==='ar'?'تصحيح المرحلة مع ذكر السبب':locale.locale==='ku'?'ڕاستکردنەوەی قۆناغ بە هۆکار':'Correct stage with a reason'}}</option><option v-if="!['cancelled','delivered'].includes(order.stage)" value="stage">{{words.stage}}</option><option v-if="!order.invoice_confirmed_at&&!['cancelled','delivered'].includes(order.stage)" value="invoice_confirmed">{{words.invoice}}</option><option v-if="order.invoice_confirmed_at&&!order.deposit_confirmed_at&&!['cancelled','delivered'].includes(order.stage)" value="deposit_confirmed">{{words.deposit}}</option></select></label>
        <label v-if="['stage','stage_correction'].includes(action)" class="form-field">{{words.stage}}<select :aria-label="words.stage" v-model="stage" :disabled="busy"><option v-for="item in orderStages.filter((s,i)=>action==='stage_correction'?s!==order.stage:s==='cancelled'||Math.abs(i-orderStages.indexOf(order.stage))===1)" :key="item" :value="item">{{label(item)}}</option></select></label>
        <textarea v-model="note" class="w-full rounded-xl border border-[var(--c-border)] p-3" :aria-label="words.note" required :minlength="action==='stage_correction'?10:3" maxlength="4000" rows="3" :disabled="busy"/><label class="flex items-center gap-2 text-sm"><input v-model="visible" type="checkbox" :disabled="busy">{{words.visible}}</label><button class="mini-action" :disabled="busy">{{locale.t('save')}}</button>
      </form>
      <OrderCommercialPanel v-if="admin" :order="order" admin @updated="row=>page.orders=page.orders.map(item=>item.id===row.id?row:item)"/>
    </div>
  </article>
  <div v-if="page.last_page>1" class="flex justify-between"><button class="mini-action" :disabled="busy||page.page<=1" @click="load(page.page-1)">{{locale.t('back')}}</button><span>{{page.page}} / {{page.last_page}}</span><button class="mini-action" :disabled="busy||page.page>=page.last_page" @click="load(page.page+1)">{{locale.t('adminNext')}}</button></div>
  <AdaptivePanel :open="previewOpen" :title="copy.preview" wide @close="closePreview"><div class="space-y-4" data-customer-order-preview><p class="rounded-xl bg-[var(--c-surface-2)] p-3 text-sm leading-7">{{copy.previewHelp}}</p><p v-if="previewBusy" role="status">{{locale.t('adminLoading')}}</p><p v-if="previewError" role="alert" class="auth-error">{{previewError}}</p><template v-if="previewOrder"><b dir="ltr">{{previewOrder.reference}}</b><h3 class="font-bold">{{label(previewOrder.stage)}}</h3><p class="text-sm">{{previewOrder.description}}</p><CustomerOrderDetails :order="previewOrder"/></template><button type="button" class="mini-action" @click="closePreview">{{locale.t('close')}}</button></div></AdaptivePanel>
</section>
</template>

<style scoped>
.order-customer-preview-action{min-height:2.75rem}
</style>
