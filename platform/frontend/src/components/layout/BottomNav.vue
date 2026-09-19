<script setup lang="ts">
import { ClipboardList, Grid2X2, Heart, House, WandSparkles } from '@lucide/vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const items = [
  { to: '/', label: 'خانه', icon: House },
  { to: '/products', label: 'محصولات', icon: Grid2X2 },
  { to: '/production', label: 'سفارش تولید', icon: WandSparkles },
  { to: '/favorites', label: 'مطلوب‌ها', icon: Heart },
  { to: '/tracking', label: 'پیگیری', icon: ClipboardList },
]
function active(path: string) {
  return path === '/' ? route.path === '/' : route.path.startsWith(path)
}
</script>

<template>
  <nav class="bottom-nav" aria-label="ناوبری اصلی">
    <div class="mx-auto grid max-w-[680px] grid-cols-5">
      <RouterLink
        v-for="item in items"
        :key="item.to"
        :to="item.to"
        class="nav-item"
        :class="{ active: active(item.to) }"
      >
        <component :is="item.icon" :size="21" />
        <span>{{ item.label }}</span>
      </RouterLink>
    </div>
  </nav>
</template>
