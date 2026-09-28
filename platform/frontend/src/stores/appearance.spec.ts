import { beforeEach, describe, expect, it } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import {
  APPEARANCE_KEY,
  APPEARANCE_SCHEMA_KEY,
  customerAppearanceDefaults,
  sanitizeAppearanceProfiles,
  useAppearanceStore,
} from './appearance'
import { profileForWidth } from '@/services/viewportProfile'

class MemoryStorage{
  private data=new Map<string,string>()
  getItem(key:string){return this.data.get(key)??null}
  setItem(key:string,value:string){this.data.set(key,String(value))}
  removeItem(key:string){this.data.delete(key)}
  clear(){this.data.clear()}
}

describe('appearance store',()=>{
  beforeEach(()=>{
    Object.defineProperty(globalThis,'localStorage',{value:new MemoryStorage(),configurable:true})
    setActivePinia(createPinia())
  })

  it('uses customer defaults for each viewport',()=>{
    const store=useAppearanceStore()
    expect(store.profiles.mobile.headerMode).toBe('compact-drawer')
    expect(store.profiles.mobile.showHamburger).toBe(false)
    expect(store.profiles.tablet.headerMode).toBe('expanded')
    expect(store.profiles.tablet.showHamburger).toBe(false)
    expect(store.profiles.desktop.headerMode).toBe('expanded')
    expect(store.profiles.desktop.homeProductGrid).toBe('hidden')
    expect(store.profiles.desktop.heroMode).toBe('single')
    expect(store.profiles.desktop.showCategoryNumbers).toBe(false)
  })

  it('migrates schema 3 to the customer defaults for navigation while preserving unrelated choices',()=>{
    localStorage.setItem(APPEARANCE_SCHEMA_KEY,'3')
    localStorage.setItem(APPEARANCE_KEY,JSON.stringify({
      mobile:{headerMode:'expanded',showHamburger:true,showWhy:false},
      tablet:{headerMode:'compact-drawer',showHamburger:true,showAbout:false},
      desktop:{headerMode:'compact-drawer',showHamburger:true},
    }))
    setActivePinia(createPinia())
    const store=useAppearanceStore()
    expect(store.profiles.mobile.headerMode).toBe('compact-drawer')
    expect(store.profiles.mobile.showHamburger).toBe(false)
    expect(store.profiles.mobile.showWhy).toBe(false)
    expect(store.profiles.tablet.headerMode).toBe('expanded')
    expect(store.profiles.tablet.showHamburger).toBe(false)
    expect(store.profiles.tablet.showAbout).toBe(false)
    expect(store.profiles.desktop.headerMode).toBe('expanded')
    expect(store.profiles.desktop.showHamburger).toBe(false)
    expect(localStorage.getItem(APPEARANCE_SCHEMA_KEY)).toBe('4')
  })

  it('allows reversible per-device header and hamburger changes after migration',()=>{
    const store=useAppearanceStore()
    store.updateProfile('mobile',{headerMode:'expanded',showHamburger:true})
    store.updateProfile('tablet',{headerMode:'compact-drawer',showHamburger:true})
    expect(store.profiles.mobile.headerMode).toBe('expanded')
    expect(store.profiles.mobile.showHamburger).toBe(false)
    expect(store.profiles.tablet.headerMode).toBe('compact-drawer')
    expect(store.profiles.tablet.showHamburger).toBe(true)
  })

  it('sanitizes invalid persisted values and protects compact navigation',()=>{
    localStorage.setItem(APPEARANCE_KEY,JSON.stringify({
      mobile:{headerMode:'compact-drawer',showHamburger:false,heroMode:'invalid',homeProductGrid:'broken'},
      desktop:{headerMode:'broken',showCategoryNumbers:'yes'},
    }))
    setActivePinia(createPinia())
    const store=useAppearanceStore()
    expect(store.profiles.mobile.showHamburger).toBe(true)
    expect(store.profiles.mobile.heroMode).toBe(customerAppearanceDefaults.mobile.heroMode)
    expect(store.profiles.desktop.headerMode).toBe(customerAppearanceDefaults.desktop.headerMode)
    expect(store.profiles.desktop.showCategoryNumbers).toBe(false)
  })

  it('resets one viewport without touching the others',()=>{
    const store=useAppearanceStore()
    store.updateProfile('desktop',{showCategoryNumbers:true})
    store.updateProfile('mobile',{homeProductGrid:'recommended-6'})
    store.resetProfile('desktop')
    expect(store.profiles.desktop.showCategoryNumbers).toBe(false)
    expect(store.profiles.mobile.homeProductGrid).toBe('recommended-6')
  })

  it('maps widths to the same mobile tablet desktop bands as md and lg',()=>{
    expect(profileForWidth(767)).toBe('mobile')
    expect(profileForWidth(768)).toBe('tablet')
    expect(profileForWidth(1023)).toBe('tablet')
    expect(profileForWidth(1024)).toBe('desktop')
  })

  it('sanitizes a partial object deterministically',()=>{
    const result=sanitizeAppearanceProfiles({desktop:{heroMode:'carousel'}})
    expect(result.desktop.heroMode).toBe('carousel')
    expect(result.mobile).toEqual(customerAppearanceDefaults.mobile)
  })
})
