<script setup lang="ts">
import { computed } from 'vue'
import { ArrowRight, Box, RotateCcw, Tag, WandSparkles } from '@lucide/vue'
import { categories } from '@/data/catalog'
import { requestPathTitle } from '@/services/whatsapp'
import { useOrderWizardStore } from '@/stores/orderWizard'
import WhatsAppIcon from '@/components/icons/WhatsAppIcon.vue'

const wizard = useOrderWizardStore()
const paths = [
  { id: 'custom' as const, label: 'تولید سفارشی', desc: 'تولید جدید با مشخصات قابل مذاکره', icon: WandSparkles },
  { id: 'brand' as const, label: 'تولید با برند', desc: 'محصول با برند سفارش‌دهنده', icon: Tag },
  { id: 'packaging' as const, label: 'تولید با بسته‌بندی', desc: 'بسته‌بندی متناسب با نیاز خریدار', icon: Box },
]
const title = computed(() => wizard.path ? requestPathTitle(wizard.path) : 'سفارش تولید')
</script>

<template>
  <section class="rounded-3xl border border-slate-200 bg-[var(--c-paper)] p-4 shadow-sm md:p-5">
    <div class="mb-4 flex items-center gap-2">
      <div>
        <h2 class="text-lg font-extrabold">{{ title }}</h2>
        <p class="mt-1 text-xs text-slate-500">مسیر مرحله‌ای برای جلوگیری از فرم‌های بلند و گیج‌کننده.</p>
      </div>
      <button v-if="wizard.step > 0" class="ms-auto inline-flex min-h-10 items-center gap-1 rounded-xl border border-slate-200 px-3 text-xs font-bold" @click="wizard.reset">
        <RotateCcw :size="16" /> شروع مجدد
      </button>
    </div>

    <div class="mb-5 grid grid-cols-4 gap-1.5">
      <i v-for="step in 4" :key="step" class="h-1.5 rounded-full" :class="step - 1 <= wizard.step ? 'bg-[var(--c-primary)]' : 'bg-slate-200'" />
    </div>

    <div v-if="wizard.step === 0" class="grid gap-3 md:grid-cols-3">
      <button v-for="item in paths" :key="item.id" class="flex items-center gap-3 rounded-2xl border border-slate-200 p-4 text-start hover:border-[var(--c-primary)]" @click="wizard.choosePath(item.id)">
        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-emerald-50 text-emerald-700"><component :is="item.icon" :size="22" /></span>
        <span><b class="block text-sm">{{ item.label }}</b><small class="mt-1 block text-[11px] leading-5 text-slate-500">{{ item.desc }}</small></span>
      </button>
    </div>

    <div v-else-if="wizard.step === 1">
      <h3 class="mb-3 text-sm font-extrabold">۱. دسته اصلی را انتخاب کنید</h3>
      <div class="grid grid-cols-3 gap-2">
        <button v-for="category in categories" :key="category.code" class="rounded-2xl border border-slate-200 p-4 text-start hover:border-[var(--c-primary)]" @click="wizard.chooseCategory(category.code)">
          <span class="text-[10px] font-extrabold text-[var(--c-primary)]">0{{ category.code }}</span>
          <b class="mt-2 block text-sm">{{ category.name }}</b>
          <small class="mt-1 block text-[10px] text-slate-500">{{ category.subtitle }}</small>
        </button>
      </div>
    </div>

    <div v-else-if="wizard.step === 2 && wizard.category">
      <h3 class="mb-3 text-sm font-extrabold">۲. زیردسته را انتخاب کنید</h3>
      <div class="grid gap-2 sm:grid-cols-2">
        <button v-for="subcategory in wizard.category.subcategories" :key="subcategory.code" class="rounded-2xl border border-slate-200 p-4 text-start hover:border-[var(--c-primary)]" @click="wizard.chooseSubcategory(subcategory.code)">
          <b class="text-sm">{{ subcategory.code }} · {{ subcategory.name }}</b>
          <small class="mt-1 block text-slate-500">{{ wizard.category.name }}</small>
        </button>
      </div>
    </div>

    <div v-else-if="wizard.step === 3 && wizard.specSource" class="space-y-4">
      <div>
        <h3 class="mb-3 text-sm font-extrabold">۳. مشخصات این زیردسته</h3>
        <div class="grid grid-cols-2 gap-2">
          <div v-for="item in wizard.specSource.specs.locked" :key="'l-'+item" class="rounded-xl border border-slate-200 bg-slate-100 p-3">
            <b class="text-xs">{{ item }}</b><small class="mt-1 block text-[10px] text-slate-500">🔒 غیرقابل تغییر</small>
          </div>
          <div v-for="item in wizard.specSource.specs.negotiable" :key="'n-'+item" class="rounded-xl border border-emerald-100 bg-emerald-50 p-3">
            <b class="text-xs">{{ item }}</b><small class="mt-1 block text-[10px] text-emerald-700">قابل مذاکره</small>
          </div>
        </div>
      </div>
      <label class="block text-xs font-bold">
        توضیح تکمیلی برای فروشنده
        <textarea v-model="wizard.note" rows="3" class="mt-1 w-full rounded-xl border border-slate-200 bg-white p-3 text-sm outline-none focus:border-[var(--c-primary)]" />
      </label>
      <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-3">
        <div class="mb-2 text-xs font-extrabold text-emerald-800">پیش‌نمایش پیام</div>
        <pre class="whitespace-pre-wrap font-sans text-xs leading-6 text-slate-700">{{ wizard.preview }}</pre>
      </div>
      <a :href="wizard.whatsapp" target="_blank" rel="noopener" class="wa-primary flex min-h-14 items-center justify-center gap-2 rounded-2xl px-4 text-sm font-extrabold">
        <span class="wa-logo-chip"><WhatsAppIcon :size="23" /></span> ادامه در واتساپ
      </a>
    </div>

    <button v-if="wizard.step > 0" class="mt-4 inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-200 px-3 text-xs font-bold" @click="wizard.back">
      <ArrowRight :size="16" /> بازگشت
    </button>
  </section>
</template>
