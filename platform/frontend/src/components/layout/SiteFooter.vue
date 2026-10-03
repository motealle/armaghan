<script setup lang="ts">
import { CircleHelp, Globe2, Mail, MessageCircleMore, PackageSearch, Send, UsersRound } from '@lucide/vue'
import { categories } from '@/data/catalog'
import { useAdminStore } from '@/features/admin/store'
import { useSessionStore } from '@/stores/session'
import { useLocaleStore } from '@/stores/locale'

const emit=defineEmits<{help:[]}>()
const locale=useLocaleStore()
const admin=useAdminStore(),session=useSessionStore()
</script>

<template>
  <footer data-style-id="footer.shell" data-style-label="فوتر سایت" class="site-footer test26-site-footer">

    <div data-style-id="footer.columns" data-style-label="ستون‌های فوتر" class="test26-footer-grid">
      <section>
        <h2>{{locale.t('products')}}</h2>
        <RouterLink v-for="category in categories" :key="category.code" :to="{path:'/products',query:{category:category.code}}">
          {{locale.categoryName(category.code,category.name)}}
        </RouterLink>
      </section>
      <section>
        <h2>{{locale.t('aboutArmaghanTitle')}}</h2>
        <RouterLink to="/">{{locale.t('home')}}</RouterLink>
        <RouterLink to="/production">{{locale.t('production')}}</RouterLink>
        <RouterLink to="/favorites">{{locale.t('favorites')}}</RouterLink>
      </section>
      <section class="test26-footer-help">
        <h2>{{locale.t('helpGuide')}}</h2>
        <button type="button" @click="emit('help')"><CircleHelp :size="16"/>{{locale.t('helpTitle')}}</button>
        <RouterLink to="/tracking"><PackageSearch :size="16"/>{{locale.t(admin.identity||session.isAuthenticated?'panel':'tracking')}}</RouterLink>
    <div data-style-id="footer.brand" data-style-label="لوگوی فوتر" class="test26-footer-brand">
      <img :src="'../../logo.png'" alt="Armaghan">
    </div>
      </section>
      <section>
        <h2>{{locale.t('footerSalesTitle')}}</h2>
        <p>{{locale.t('footerContact')}}</p>
        <span class="test26-footer-contact"><Mail :size="16"/>{{locale.t('footerContactLabel')}}</span>
        <div class="test26-footer-socials" :aria-label="locale.t('footerSocialTitle')">
          <span class="test26-footer-social-icon" :title="locale.t('socialInstagram')"><MessageCircleMore :size="16"/></span>
          <span class="test26-footer-social-icon" :title="locale.t('socialLinkedIn')"><UsersRound :size="16"/></span>
          <span class="test26-footer-social-icon" :title="locale.t('socialTelegram')"><Send :size="16"/></span>
          <span class="test26-footer-social-icon" :title="locale.t('socialYouTube')"><Globe2 :size="16"/></span>
        </div>

      </section>
    </div>

  </footer>
</template>
