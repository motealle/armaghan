import { describe, expect, it } from 'vitest'
import { products } from '@/data/catalog'
import { buildProductMessage, buildProductionMessage, whatsappUrl } from './whatsapp'

describe('WhatsApp builder',()=>{
  it('builds a product message with code and path',()=>{
    const message=buildProductMessage(products[0]!, 'available')
    expect(message).toContain('خرید موجود')
    expect(message).toContain(products[0]!.code)
    expect(message).toContain(products[0]!.name)
  })

  it('generates a canonical wa.me url',()=>{
    const url=whatsappUrl('سلام تست')
    expect(url.startsWith('https://wa.me/989381009231?text=')).toBe(true)
    expect(url).toContain(encodeURIComponent('سلام تست'))
  })

  it('builds production messages with fixed and negotiable specs',()=>{
    const message=buildProductionMessage({
      path:'brand',category:'نوزادی',subcategory:'11 · لباس نوزادی',
      negotiable:['سایز'],locked:['جنس'],note:'نمونه',
    })
    expect(message).toContain('سفارش تولید با برند')
    expect(message).toContain('قابل مذاکره: سایز')
    expect(message).toContain('ثابت: جنس')
  })
})
