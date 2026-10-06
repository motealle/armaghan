<script setup lang="ts">
import { ArrowUpRight } from '@lucide/vue'
import WhatsAppIcon from '@/components/icons/WhatsAppIcon.vue'
import { useLocaleStore } from '@/stores/locale'
import { directContactWhatsappUrl } from '@/services/whatsapp'
const locale=useLocaleStore()
const contactUrl=directContactWhatsappUrl()
</script>
<template>
  <section class="admin-surface mb-4 rounded-2xl p-4" aria-labelledby="customer-contact-heading">
    <h2 id="customer-contact-heading" class="text-base font-black text-[var(--c-text)]">{{locale.t('customerContactTitle')}}</h2>
    <p class="mt-2 text-sm leading-6 text-[var(--c-muted)]">{{locale.t('customerContactHelp')}}</p>
    <a :href="contactUrl" target="_blank" rel="noopener noreferrer" class="mini-action customer-whatsapp-action mt-3 min-h-12 gap-2">
      <WhatsAppIcon :size="20" tone="white"/><span>{{locale.t('customerContactAction')}}</span><ArrowUpRight :size="17" aria-hidden="true"/>
    </a>
  </section>
</template>
