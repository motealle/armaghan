import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { baseMessages } from '@/i18n/messages'
import { useLocaleStore } from './locale'

vi.mock('@/services/localeDetection',()=>({detectInitialLocale:vi.fn(async()=>'fa')}))

class MemoryStorage{
  private data=new Map<string,string>()
  getItem(key:string){return this.data.get(key)??null}
  setItem(key:string,value:string){this.data.set(key,String(value))}
  removeItem(key:string){this.data.delete(key)}
}

describe('Test29 locale isolation',()=>{
  beforeEach(()=>{
    vi.stubGlobal('localStorage',new MemoryStorage())
    vi.stubGlobal('document',{documentElement:{lang:'',dir:'',dataset:{}}})
    setActivePinia(createPinia())
  })
  afterEach(()=>vi.unstubAllGlobals())

  it('keeps previous-version language preferences and overrides untouched',()=>{
    localStorage.setItem('armaghan:locale:manual','ar')
    localStorage.setItem('armaghan:test28:translations',JSON.stringify({fa:{home:'نسخه قبل'}}))
    const store=useLocaleStore()
    store.setManual('en')
    store.setOverride('fa','home','نسخه جدید')
    expect(localStorage.getItem('armaghan:locale:manual')).toBe('ar')
    expect(JSON.parse(localStorage.getItem('armaghan:test28:translations')!)).toEqual({fa:{home:'نسخه قبل'}})
    expect(localStorage.getItem('armaghan:test29:locale:manual')).toBe('en')
    expect(store.t('home','fa')).toBe('نسخه جدید')
    expect(document.documentElement.dir).toBe('ltr')
  })

  it('restores the new-version preference independently',async()=>{
    localStorage.setItem('armaghan:locale:manual','ar')
    localStorage.setItem('armaghan:test29:locale:manual','ku')
    const store=useLocaleStore()
    await store.initialize()
    expect(store.locale).toBe('ku')
    expect(document.documentElement.lang).toBe('ckb')
    expect(document.documentElement.dir).toBe('rtl')
    expect(localStorage.getItem('armaghan:locale:manual')).toBe('ar')
  })

  it('uses localized Why Armaghan defaults in all supported locales',()=>{
    expect(baseMessages.fa.whyArmaghanEyebrow).toBe('چرا ارمغان؟')
    expect(baseMessages.ar.whyArmaghanEyebrow).toBe('لماذا أرمغان؟')
    expect(baseMessages.ku.whyArmaghanEyebrow).toBe('بۆچی ئارماغان؟')
    expect(baseMessages.en.whyArmaghanEyebrow).toBe('Why Armaghan?')
  })
})
