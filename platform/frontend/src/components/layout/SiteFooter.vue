<script setup lang="ts">
import { CircleHelp, Mail, PackageSearch } from '@lucide/vue'
import { categories } from '@/data/catalog'
import { useLocaleStore } from '@/stores/locale'

const emit=defineEmits<{help:[]}>()
const locale=useLocaleStore()
</script>

<template>
  <footer class="site-footer test26-site-footer">
    <div class="test26-footer-brand">
      <img src="../../logo.png" alt="Armaghan">
      <div>
        <b>{{locale.t('brandName')}}</b>
        <span>{{locale.t('footerText')}}</span>
      </div>
    </div>

    <div class="test26-footer-grid">
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
      <section>
        <h2>{{locale.t('helpGuide')}}</h2>
        <button type="button" @click="emit('help')"><CircleHelp :size="16"/>{{locale.t('helpTitle')}}</button>
        <RouterLink to="/tracking"><PackageSearch :size="16"/>{{locale.t('tracking')}}</RouterLink>
      </section>
      <section>
        <h2>{{locale.t('footerSalesTitle')}}</h2>
        <p>{{locale.t('footerContact')}}</p>
        <span class="test26-footer-contact"><Mail :size="16"/>{{locale.t('footerContactLabel')}}</span>
      </section>
    </div>
  </footer>
</template>
