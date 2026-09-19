import { describe, expect, it } from 'vitest'
import { geoLocale } from './localeDetection'

describe('locale geolocation guess',()=>{
  it('prioritizes Iraqi Kurdistan before Arabic-country mapping',()=>{
    expect(geoLocale({countryCode:'IQ',region:'Erbil'})).toBe('ku')
    expect(geoLocale({countryCode:'IQ',region:'Sulaymaniyah'})).toBe('ku')
  })
  it('maps Kurdish-majority Iranian provinces to Sorani and Tehran to Persian',()=>{
    expect(geoLocale({countryCode:'IR',region:'Kurdistan'})).toBe('ku')
    expect(geoLocale({countryCode:'IR',region:'Kermanshah'})).toBe('ku')
    expect(geoLocale({countryCode:'IR',region:'Tehran'})).toBe('fa')
  })
  it('maps Arabic countries to Arabic and other known countries to English',()=>{
    expect(geoLocale({countryCode:'SA',region:'Riyadh'})).toBe('ar')
    expect(geoLocale({countryCode:'DE',region:'Saxony'})).toBe('en')
  })
})
