<script setup lang="ts">
import { MessageCircle, ArrowUpRight } from '@lucide/vue'
import { useLocaleStore } from '@/stores/locale'
import { directContactWhatsappUrl } from '@/services/whatsapp'
const locale=useLocaleStore()
const contactUrl=directContactWhatsappUrl()
</script>
<template>
  <section class="admin-surface mb-4 rounded-2xl p-4" aria-labelledby="customer-contact-heading">
    <h2 id="customer-contact-heading" class="text-base font-black text-[var(--c-text)]">{{locale.t('customerContactTitle')}}</h2>
    <p class="mt-2 text-sm leading-6 text-[var(--c-muted)]">{{locale.t('customerContactHelp')}}</p>
    <a :href="contactUrl" target="_blank" rel="noopener noreferrer" class="mini-action mt-3 min-h-12 gap-2">
      <MessageCircle :size="20" aria-hidden="true"/><span>{{locale.t('customerContactAction')}}</span><ArrowUpRight :size="17" aria-hidden="true"/>
    </a>
  </section>
</template>
