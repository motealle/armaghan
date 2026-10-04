import { describe, expect, it } from 'vitest'
import { createSSRApp } from 'vue'
import { renderToString } from 'vue/server-renderer'
import { createPinia, setActivePinia } from 'pinia'
import { useLocaleStore } from '@/stores/locale'
import CustomerContactCard from './CustomerContactCard.vue'

describe('customer direct contact card',()=>{
  it.each(['fa','ar','en','ku'] as const)('renders an accessible direct link in %s without an account or product',async(lang)=>{
    const pinia=createPinia();setActivePinia(pinia)
    const locale=useLocaleStore();locale.locale=lang
    const app=createSSRApp(CustomerContactCard);app.use(pinia)
    const html=await renderToString(app)
    expect(html).toContain('href="https://wa.me/989933509793"')
    expect(html).toContain('rel="noopener noreferrer"')
    expect(html).toContain(locale.t('customerContactAction'))
    expect(html).toContain(locale.t('customerContactHelp'))
    expect(html).not.toContain('?text=')
  })
})
