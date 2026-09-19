import { beforeEach, describe, expect, it } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { useOrderWizardStore } from './orderWizard'

describe('production order wizard',()=>{
  beforeEach(()=>setActivePinia(createPinia()))

  it('moves through path, category, subcategory and produces preview',()=>{
    const wizard=useOrderWizardStore()
    wizard.choosePath('custom')
    expect(wizard.step).toBe(1)
    wizard.chooseCategory('1')
    expect(wizard.step).toBe(2)
    wizard.chooseSubcategory('11')
    expect(wizard.step).toBe(3)
    expect(wizard.preview).toContain('تولید سفارشی')
    expect(wizard.preview).toContain('11 · لباس نوزادی')
    expect(wizard.whatsapp).toContain('wa.me/989381009231')
  })

  it('backs out without corrupting state',()=>{
    const wizard=useOrderWizardStore()
    wizard.choosePath('packaging')
    wizard.chooseCategory('3')
    wizard.chooseSubcategory('32')
    wizard.back()
    expect(wizard.step).toBe(2)
    expect(wizard.subcategoryCode).toBeNull()
    wizard.reset()
    expect(wizard.step).toBe(0)
    expect(wizard.path).toBeNull()
  })
})
