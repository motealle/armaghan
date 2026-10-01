import { describe, expect, it } from 'vitest'
import { customerProductLabel } from './presentation'

describe('customer product presentation',()=>{
  it('uses the six-way subcategory label for available products',()=>{
    expect(customerProductLabel({availability:'available'},'لباس نوزادی','ناموجود/تولید‌پذیر')).toBe('لباس نوزادی')
  })

  it('uses the unified unavailable/producible label for unavailable and made-to-order products',()=>{
    expect(customerProductLabel({availability:'unavailable'},'پسرانه','ناموجود/تولید‌پذیر')).toBe('ناموجود/تولید‌پذیر')
    expect(customerProductLabel({availability:'made_to_order'},'دخترانه','ناموجود/تولید‌پذیر')).toBe('ناموجود/تولید‌پذیر')
  })
})
