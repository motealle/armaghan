<script setup lang="ts">
import { ClipboardList, Grid2X2, Heart, House, WandSparkles } from '@lucide/vue'
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useLocaleStore } from '@/stores/locale'

const route=useRoute()
const locale=useLocaleStore()
const items=computed(()=>[
  {to:'/',label:locale.t('home'),icon:House},
  {to:'/products',label:locale.t('products'),icon:Grid2X2},
  {to:'/production',label:locale.t('production'),icon:WandSparkles},
  {to:'/favorites',label:locale.t('favorites'),icon:Heart},
  {to:'/tracking',label:locale.t('tracking'),icon:ClipboardList},
])
function active(path:string){return path==='/'?route.path==='/':route.path.startsWith(path)}
</script>

<template>
  <nav class="bottom-nav lg:hidden" :aria-label="locale.t('home')">
    <div class="mx-auto grid max-w-[680px] grid-cols-5">
      <RouterLink v-for="item in items" :key="item.to" :to="item.to" class="nav-item" :class="{active:active(item.to)}">
        <component :is="item.icon" :size="21"/>
        <span>{{item.label}}</span>
      </RouterLink>
    </div>
  </nav>
</template>
