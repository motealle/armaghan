import { describe, expect, it } from 'vitest'
import { products } from '@/data/catalog'
import { buildProductMessage, buildProductionMessage, whatsappUrl, directContactWhatsappUrl } from './whatsapp'

describe('WhatsApp builder',()=>{
  it('opens a blank direct chat to the configured team without customer/order/product data',()=>{
    const url=new URL(directContactWhatsappUrl())
    expect(url.origin).toBe('https://wa.me')
    expect(url.pathname).toBe('/989933509793')
    expect(url.search).toBe('');expect(url.hash).toBe('')
  })
  it('builds a product message with code and path',()=>{
    const message=buildProductMessage(products[0]!, 'available')
    expect(message).toContain('درخواست تغییر')
    expect(message).toContain(products[0]!.code)
    expect(message).toContain(products[0]!.subcategoryName)
  })

  it('generates a canonical wa.me url',()=>{
    const url=whatsappUrl('سلام تست')
    expect(url.startsWith('https://wa.me/989933509793?text=')).toBe(true)
    expect(url).toContain(encodeURIComponent('سلام تست'))
  })

  it('builds production messages with fixed and negotiable specs',()=>{
    const message=buildProductionMessage({
      path:'brand',category:'نوزادی',subcategory:'11 · لباس نوزادی',
      negotiable:['سایز'],locked:['جنس'],note:'نمونه',
    })
    expect(message).toContain('تولید با برند اختصاصی')
    expect(message).toContain('قابل مذاکره: سایز')
    expect(message).toContain('ثابت: جنس')
  })
})
