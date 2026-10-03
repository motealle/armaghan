<script setup lang="ts">
import {computed,onMounted,ref} from 'vue'
import {useLocaleStore} from '@/stores/locale'
import {CustomerSessionApiError} from '@/features/auth/services/customerSessionApi'
import {fetchAdminCustomers,type AdminCustomer} from '@/features/admin/services/adminApi'
import {createTrackedOrder,fetchOrders,updateTrackedOrder,orderStages,stageLabels,type TrackedOrder,type OrderPage} from '../services/trackingApi'
import {requestPathTitle} from '@/services/whatsapp'
import type {RequestPath} from '@/types/domain'
const props=defineProps<{admin?:boolean}>(),locale=useLocaleStore()
const page=ref<OrderPage>({orders:[],page:1,last_page:1}),busy=ref(false),error=ref(''),selected=ref<number|null>(null),createOpen=ref(false)
const customers=ref<AdminCustomer[]>([]),customerSearch=ref(''),customerId=ref<number|null>(null),description=ref(''),path=ref<RequestPath>('simple')
const note=ref(''),stage=ref('review'),visible=ref(false),action=ref('note')
const words=computed(()=>locale.locale==='fa'?{title:'سفارش‌ها و خط زمانی',empty:'هنوز سفارشی برای پیگیری ثبت نشده است.',create:'ثبت درخواست جدید',note:'توضیح مرحله',visible:'نمایش این توضیح به مشتری',invoice:'تأیید دستی فاکتور',deposit:'تأیید دستی پیش‌پرداخت',stage:'تغییر مرحله',manual:'وضعیت و تأییدها دستی ثبت می‌شوند؛ تأیید پیش‌پرداخت به معنی بررسی بانکی خودکار نیست.',private:'یادداشت داخلی',registered:'ثبت درخواست برای پیگیری'}:locale.locale==='ar'?{title:'الطلبات والخط الزمني',empty:'لا توجد طلبات مسجلة.',create:'طلب جديد',note:'ملاحظة المرحلة',visible:'إظهار الملاحظة للعميل',invoice:'تأكيد الفاتورة يدوياً',deposit:'تأكيد الدفعة يدوياً',stage:'تغيير المرحلة',manual:'التأكيدات يدوية ولا تعني تحققاً مصرفياً آلياً.',private:'ملاحظة داخلية',registered:'تسجيل الطلب'}:locale.locale==='ku'?{title:'داواکاری و هێڵی کات',empty:'هێشتا داواکاری تۆمار نەکراوە.',create:'داواکاری نوێ',note:'تێبینی قۆناغ',visible:'پیشاندانی تێبینی بۆ کڕیار',invoice:'پشتڕاستکردنەوەی فاکتۆر بە دەست',deposit:'پشتڕاستکردنەوەی پێشەکی بە دەست',stage:'گۆڕینی قۆناغ',manual:'پشتڕاستکردنەوەکان بە دەستە و پشکنینی بانکی نییە.',private:'تێبینی ناوخۆیی',registered:'تۆمارکردنی داواکاری'}:{title:'Orders & timeline',empty:'No orders registered yet.',create:'New request',note:'Stage note',visible:'Show this note to the customer',invoice:'Manually confirm invoice',deposit:'Manually confirm deposit',stage:'Change stage',manual:'Confirmations are manual; deposit confirmation is not an automated bank check.',private:'Internal note',registered:'Register request'})
const paths:RequestPath[]=['simple','available','unavailable','custom','brand','packaging']
function confirmation(value:string|null){return value?'✓ '+new Date(value).toLocaleDateString(locale.locale==='ku'?'ar':locale.locale):'—'}
function label(value:string){return stageLabels[locale.locale][orderStages.indexOf(value as typeof orderStages[number])]??value}
function fail(e:unknown){error.value=locale.t(e instanceof CustomerSessionApiError&&e.status===409?'adminConflict':e instanceof CustomerSessionApiError&&e.status===403?'adminPermissionDenied':'adminRequestFailed')}
async function load(number=1){busy.value=true;error.value='';try{page.value=await fetchOrders(!!props.admin,number)}catch(e){fail(e)}finally{busy.value=false}}
async function searchCustomers(){try{customers.value=(await fetchAdminCustomers(1,customerSearch.value)).customers}catch(e){fail(e)}}
function open(row:TrackedOrder){selected.value=selected.value===row.id?null:row.id;note.value='';action.value='note';stage.value=orderStages[Math.min(orderStages.indexOf(row.stage)+1,8)]!;visible.value=false;error.value=''}
async function create(){if(busy.value||description.value.trim().length<3||props.admin&&!customerId.value)return;busy.value=true;error.value='';try{await createTrackedOrder({request_path:path.value,description:description.value.trim(),...(props.admin?{customer_id:customerId.value!}:{})},!!props.admin);description.value='';createOpen.value=false;page.value=await fetchOrders(!!props.admin)}catch(e){fail(e)}finally{busy.value=false}}
async function save(row:TrackedOrder){if(busy.value||note.value.trim().length<3)return;busy.value=true;error.value='';try{const result=await updateTrackedOrder(row,{action:action.value,...(action.value==='stage'?{stage:stage.value}:{}),note:note.value.trim(),visible_to_customer:visible.value});page.value.orders=page.value.orders.map(o=>o.id===row.id?result.order:o);note.value=''}catch(e){fail(e)}finally{busy.value=false}}
onMounted(()=>load())
</script>
<template>
<section class="space-y-3" :aria-busy="busy">
  <header class="flex flex-wrap items-center gap-2"><h2 class="text-lg font-black">{{words.title}}</h2><button class="mini-action ms-auto" :disabled="busy" @click="createOpen=!createOpen;admin&&searchCustomers()">{{words.create}}</button><button class="mini-action" :disabled="busy" @click="load(page.page)">{{locale.t('adminReload')}}</button></header>
  <p v-if="admin" class="text-xs text-[var(--c-muted)]">{{words.manual}}</p>
  <form v-if="createOpen" class="admin-surface space-y-3 rounded-2xl p-4" @submit.prevent="create">
    <template v-if="admin"><div class="flex gap-2"><input v-model="customerSearch" class="min-w-0 flex-1" :placeholder="locale.t('searchLabel')" maxlength="100"><button type="button" class="mini-action" :disabled="busy" @click="searchCustomers">{{locale.t('searchLabel')}}</button></div><label class="form-field">{{locale.t('customerLabel')}}<select v-model.number="customerId" required :disabled="busy"><option :value="null">—</option><option v-for="customer in customers" :key="customer.id" :value="customer.id">#{{customer.id}} · {{customer.company_name||customer.name||'—'}} {{customer.email||''}}</option></select></label></template>
    <label class="form-field">{{locale.t('whatsappPath')}}<select v-model="path" :disabled="busy"><option v-for="item in paths" :key="item" :value="item">{{requestPathTitle(item,locale.locale)}}</option></select></label>
    <label class="form-field">{{locale.locale==='fa'?'شرح درخواست (قابل مشاهده برای مشتری)':locale.locale==='ar'?'وصف الطلب (ظاهر للعميل)':locale.locale==='ku'?'وەسفی داواکاری (بۆ کڕیار)':'Request description (visible to customer)'}}<textarea v-model="description" required minlength="3" maxlength="4000" rows="3" :disabled="busy"/></label><button class="mini-action" :disabled="busy">{{words.registered}}</button>
  </form>
  <p v-if="error" class="auth-error" role="alert">{{error}}</p><p v-if="busy" role="status">{{locale.t('adminLoading')}}</p>
  <p v-if="!busy&&!error&&!page.orders.length" class="admin-surface rounded-2xl p-4 text-sm">{{words.empty}}</p>
  <article v-for="order in page.orders" :key="order.id" class="admin-surface rounded-2xl p-4">
    <button class="flex min-h-11 w-full items-center justify-between gap-2 text-start" :disabled="busy" :aria-expanded="selected===order.id" @click="open(order)"><b dir="ltr">{{order.reference}}</b><b>{{label(order.stage)}}</b></button>
    <p v-if="admin" class="mb-2 text-sm font-bold">{{locale.t('customerLabel')}}: {{order.customer_name||'—'}} · #{{order.customer_id}}</p><p class="text-sm">{{order.description}}</p><p class="mt-2 text-xs">{{words.invoice}}: {{confirmation(order.invoice_confirmed_at)}} · {{words.deposit}}: {{confirmation(order.deposit_confirmed_at)}}</p>
    <div v-if="selected===order.id" class="mt-3 space-y-3">
      <ol class="grid grid-cols-2 gap-2 sm:grid-cols-3"><li v-for="(item,index) in orderStages.slice(0,9)" :key="item" class="rounded-xl border border-[var(--c-border)] p-2 text-xs" :class="{'font-bold text-[var(--c-secondary)]':index<=orderStages.indexOf(order.stage)&&order.stage!=='cancelled'}">{{index+1}} · {{label(item)}} <span v-if="item===order.stage">●</span></li></ol>
      <ol class="space-y-2 border-s-2 border-[var(--c-secondary)] ps-3"><li v-for="event in order.events" :key="event.id" class="rounded-xl bg-[var(--c-surface-2)] p-3 text-sm"><b>{{label(event.stage)}}</b><time class="mx-2 text-xs">{{new Date(event.created_at).toLocaleString(locale.locale==='ku'?'ar':locale.locale)}}</time><small v-if="admin&&!event.visible_to_customer">{{words.private}}</small><p class="whitespace-pre-wrap">{{event.note}}</p></li></ol>
      <form v-if="admin" class="space-y-2 border-t border-[var(--c-border)] pt-3" @submit.prevent="save(order)">
        <label class="form-field">{{locale.t('actions')}}<select v-model="action" :disabled="busy"><option value="note">{{words.note}}</option><option v-if="!['cancelled','delivered'].includes(order.stage)" value="stage">{{words.stage}}</option><option v-if="!order.invoice_confirmed_at&&!['cancelled','delivered'].includes(order.stage)" value="invoice_confirmed">{{words.invoice}}</option><option v-if="order.invoice_confirmed_at&&!order.deposit_confirmed_at&&!['cancelled','delivered'].includes(order.stage)" value="deposit_confirmed">{{words.deposit}}</option></select></label>
        <label v-if="action==='stage'" class="form-field">{{words.stage}}<select v-model="stage" :disabled="busy"><option v-for="item in orderStages.filter((s,i)=>s==='cancelled'||Math.abs(i-orderStages.indexOf(order.stage))===1)" :key="item" :value="item">{{label(item)}}</option></select></label>
        <textarea v-model="note" class="w-full rounded-xl border border-[var(--c-border)] p-3" :aria-label="words.note" required minlength="3" maxlength="4000" rows="3" :disabled="busy"/><label class="flex items-center gap-2 text-sm"><input v-model="visible" type="checkbox" :disabled="busy">{{words.visible}}</label><button class="mini-action" :disabled="busy">{{locale.t('save')}}</button>
      </form>
    </div>
  </article>
  <div v-if="page.last_page>1" class="flex justify-between"><button class="mini-action" :disabled="busy||page.page<=1" @click="load(page.page-1)">{{locale.t('back')}}</button><span>{{page.page}} / {{page.last_page}}</span><button class="mini-action" :disabled="busy||page.page>=page.last_page" @click="load(page.page+1)">{{locale.t('adminNext')}}</button></div>
</section>
</template>
