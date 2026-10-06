<script setup lang="ts">
import { useLocaleStore } from '@/stores/locale'
import type { CustomerFields } from '../services/adminApi'
const draft=defineModel<CustomerFields>({required:true})
defineProps<{disabled:boolean}>()
const locale=useLocaleStore()
const addressFields=[['city','customerCity'],['district','customerDistrict'],['shop_number','customerShopNumber']] as const
const businessFields=[['sales_product_group','customerSalesProductGroup'],['purchase_volume','customerPurchaseVolume'],['cooperation_type','customerCooperationType'],['sales_type','customerSalesType']] as const
</script>
<template>
  <div class="space-y-4">
    <fieldset class="admin-surface grid gap-3 rounded-2xl p-4 md:grid-cols-2" :disabled="disabled">
      <legend class="px-2 font-bold">{{locale.t('customerIdentityGroup')}}</legend>
      <label class="form-field">{{locale.t('customerContactName')}}<input v-model="draft.contact_name" maxlength="255" autocomplete="name"></label>
      <label class="form-field">{{locale.t('customerStoreName')}}<input v-model="draft.company_name" required maxlength="255" autocomplete="organization"></label>
      <label class="form-field">{{locale.t('customerPhone')}}<input v-model="draft.phone" dir="ltr" maxlength="64" inputmode="tel" autocomplete="tel"></label>
      <label class="form-field">WhatsApp<input v-model="draft.whatsapp" dir="ltr" maxlength="64" inputmode="tel"></label>
      <label class="form-field">{{locale.t('customerContactEmail')}}<input v-model="draft.contact_email" type="email" dir="ltr" maxlength="255" autocomplete="email"></label>
      <label class="form-field">{{locale.t('customerPreferredLanguage')}}<select v-model="draft.preferred_language"><option :value="null">—</option><option value="fa">فارسی</option><option value="en">English</option><option value="ar">العربية</option><option value="ku">کوردی</option></select></label>
      <p class="text-xs text-[var(--c-muted)] md:col-span-2">{{locale.t('customerLoginEmailHelp')}}</p>
    </fieldset>
    <fieldset class="admin-surface grid gap-3 rounded-2xl p-4 md:grid-cols-2" :disabled="disabled">
      <legend class="px-2 font-bold">{{locale.t('customerAddressGroup')}}</legend>
      <label class="form-field">{{locale.t('country')}}<input v-model="draft.country_name" maxlength="255" autocomplete="country-name"></label>
      <label class="form-field">{{locale.t('adminCountryCode')}}<input v-model="draft.country_code" dir="ltr" maxlength="2" pattern="[A-Za-z]{2}"></label>
      <label v-for="[field,label] in addressFields" :key="field" class="form-field">{{locale.t(label)}}<input v-model="draft[field]" maxlength="255"></label>
    </fieldset>
    <fieldset class="admin-surface grid gap-3 rounded-2xl p-4 md:grid-cols-2" :disabled="disabled">
      <legend class="px-2 font-bold">{{locale.t('customerBusinessGroup')}}</legend>
      <label v-for="[field,label] in businessFields" :key="field" class="form-field">{{locale.t(label)}}<input v-model="draft[field]" maxlength="255"></label>
      <label class="flex items-center gap-2 md:col-span-2"><input v-model="draft.pinned" type="checkbox">{{locale.t('customerPin')}}</label>
    </fieldset>
  </div>
</template>
