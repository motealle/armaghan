<script setup lang="ts">
import { ArrowUp, ArrowDown, ArrowUpToLine, ArrowDownToLine } from '@lucide/vue'
import { useLocaleStore } from '@/stores/locale'
import type { OrderMove } from '../services/productOrder'
withDefaults(defineProps<{first:boolean;last:boolean;disabled?:boolean}>(),{disabled:false})
const emit=defineEmits<{move:[direction:OrderMove]}>()
const locale=useLocaleStore()
const actions=[{key:'first',icon:ArrowUpToLine},{key:'up',icon:ArrowUp},{key:'down',icon:ArrowDown},{key:'last',icon:ArrowDownToLine}] as const
</script>
<template>
 <div class="product-move-buttons" role="group" :aria-label="locale.t('orderTitle')" @click.stop @touchstart.stop @touchend.stop>
  <button v-for="action in actions" :key="action.key" type="button" :data-move="action.key" :title="locale.t('order'+action.key)" :aria-label="locale.t('order'+action.key)" :disabled="disabled||(['first','up'].includes(action.key)?first:last)" @click.stop="emit('move',action.key)"><component :is="action.icon" :size="17" aria-hidden="true"/></button>
 </div>
</template>
<style scoped>
.product-move-buttons{display:inline-flex;direction:ltr;flex-shrink:0;overflow:hidden;isolation:isolate;border:1px solid var(--c-border);border-radius:.8rem;background:var(--c-surface);color:var(--c-primary);box-shadow:0 2px 5px #00000014}
button{display:grid;place-items:center;width:2rem;min-height:2.5rem;flex-shrink:0;background:transparent;cursor:pointer}
button+button{border-inline-start:1px solid var(--c-border)}
button:hover:not(:disabled){background:var(--c-surface-2)}
button:focus-visible{outline:2px solid var(--c-primary);outline-offset:-3px}
button:disabled{opacity:.3;cursor:default}
@media(pointer:coarse){button{min-height:2.75rem}}
</style>
