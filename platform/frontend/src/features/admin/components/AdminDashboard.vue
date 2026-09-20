<script setup lang="ts">
import { ref } from 'vue'
import { ImagePlus, KeyRound, LogIn, Plus, RotateCcw, Trash2, UserRoundCog } from '@lucide/vue'
import { demoCustomers } from '@/data/catalog'
import { compressImage } from '@/features/admin/services/imageCompression'
import { useCatalogStore } from '@/stores/catalog'
import { useSessionStore } from '@/stores/session'
import { useDesignStore } from '@/stores/design'
import type { Product } from '@/types/domain'

const catalog=useCatalogStore()
const session=useSessionStore()
const design=useDesignStore()
const note=ref('')
const name=ref('')
const code=ref('')

function addProduct(){
  if(!name.value.trim()||!code.value.trim())return
  const nextId=Math.max(0,...catalog.items.map(p=>p.id))+1
  const product:Product={
    id:nextId,code:code.value.trim(),name:name.value.trim(),categoryCode:'1',subcategoryCode:'11',
    categoryName:'نوزادی',subcategoryName:'لباس نوزادی',availability:'available',
    specs:{locked:[],negotiable:['مشخصات قابل مذاکره']},
  }
  catalog.add(product);name.value='';code.value=''
}
async function upload(id:number,event:Event){
  const input=event.target as HTMLInputElement
  const file=input.files?.[0]
  if(!file)return
  try{
    const compressed=await compressImage(file)
    catalog.updateImage(id,compressed)
    note.value='تصویر در نسخه آزمایشی به WebP فشرده شد. در پروداکشن این کار سمت سرور/صف انجام می‌شود.'
  }catch{note.value='پردازش تصویر ناموفق بود.'}
}
function impersonate(id:number){session.impersonate(id);note.value='حالت ورود به‌نیابت فعال شد؛ برای خروج از آن یا کل حساب، دکمه خروج بالای صفحه همیشه فعال است.'}
</script>

<template>
  <div class="space-y-6">
    <section class="rounded-2xl border border-[var(--c-border)] bg-[var(--c-surface)] p-4 shadow-sm">
      <div class="mb-3">
        <h2 class="text-lg font-black text-[var(--c-text)]">نمای اکشن کارت محصول</h2>
        <p class="mt-1 text-xs text-[var(--c-muted)]">مدیر تعیین می‌کند اکشن‌های کارت برای همه کاربران فشرده یا دارای برچسب باشند.</p>
      </div>
      <div class="grid gap-2 sm:grid-cols-2">
        <button
          type="button"
          class="admin-display-mode"
          :class="{active:design.cardActionMode==='compact'}"
          :aria-pressed="design.cardActionMode==='compact'"
          @click="design.cardActionMode='compact'"
        >
          <b>آیکون فقط</b>
          <span>پیش‌فرض؛ آیکون‌های ۸۰٪ و بدون متن</span>
        </button>
        <button
          type="button"
          class="admin-display-mode"
          :class="{active:design.cardActionMode==='labeled'}"
          :aria-pressed="design.cardActionMode==='labeled'"
          @click="design.cardActionMode='labeled'"
        >
          <b>آیکون + متن</b>
          <span>سفارش · مطلوب · مشخصات</span>
        </button>
      </div>
    </section>
    <section>
      <div class="mb-3 flex flex-wrap items-end gap-3">
        <div><h2 class="text-lg font-black">مشتری‌ها</h2><p class="mt-1 text-xs text-slate-500">نمای Customer 360 برای اعتبارسنجی تجربه مدیر.</p></div>
      </div>
      <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white p-2 shadow-sm">
        <table class="w-full min-w-[760px] text-xs">
          <thead><tr class="text-slate-500"><th class="p-2 text-start">مشتری</th><th class="p-2 text-start">واتساپ</th><th class="p-2 text-start">ایمیل</th><th class="p-2 text-start">وضعیت</th><th class="p-2 text-start">سفارش</th><th class="p-2 text-start">عملیات</th></tr></thead>
          <tbody>
            <tr v-for="customer in demoCustomers" :key="customer.id" class="border-t border-slate-100">
              <td class="p-2 font-bold">{{customer.flag}} {{customer.name}}</td><td class="p-2">{{customer.whatsapp}}</td><td class="p-2">{{customer.email}}</td><td class="p-2">{{customer.activeOrder}}</td><td class="p-2">{{customer.orderCount}}</td>
              <td class="p-2"><div class="flex gap-1"><button class="mini-action"><KeyRound :size="15"/> لینک/رمز</button><button class="mini-action" @click="impersonate(customer.id)"><UserRoundCog :size="15"/> ورود به‌نیابت</button></div></td>
            </tr>
          </tbody>
        </table>
      </div>
      <p v-if="note" class="mt-2 text-xs text-emerald-700">{{note}}</p>
    </section>

    <section>
      <div class="mb-3 flex items-center gap-3">
        <div><h2 class="text-lg font-black">محصولات</h2><p class="text-xs text-slate-500">CRUD آزمایشی؛ منبع نهایی Filament خواهد بود.</p></div>
        <button class="ms-auto mini-action" @click="catalog.reset"><RotateCcw :size="15"/> بازنشانی دمو</button>
      </div>
      <form class="mb-3 grid gap-2 rounded-2xl border border-slate-200 bg-white p-3 sm:grid-cols-[1fr_180px_auto]" @submit.prevent="addProduct">
        <input v-model="name" class="rounded-xl border border-slate-200 p-2.5 text-sm" placeholder="نام محصول">
        <input v-model="code" class="rounded-xl border border-slate-200 p-2.5 text-sm" placeholder="کد محصول">
        <button class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[var(--c-primary)] px-4 text-sm font-bold text-white"><Plus :size="17"/> افزودن</button>
      </form>
      <div class="grid gap-3 md:grid-cols-2">
        <article v-for="product in catalog.items" :key="product.id" class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
          <div class="flex items-center gap-3"><div class="min-w-0 flex-1"><b class="block truncate text-sm">{{product.name}}</b><small class="text-slate-500">{{product.code}}</small></div>
            <label class="mini-action cursor-pointer"><ImagePlus :size="15"/> تصویر<input class="hidden" type="file" accept="image/*" @change="upload(product.id,$event)"></label>
            <button class="mini-action text-rose-700" @click="catalog.remove(product.id)"><Trash2 :size="15"/> حذف</button>
          </div>
        </article>
      </div>
    </section>
  </div>
</template>
