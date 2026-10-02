import { describe, expect, it } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import {
  sanitizeVisualStyleProfile,
  useVisualStyleStore,
  targetIsHidden,
  visualProfilesEqual,
} from './store'

describe('visual style profile sanitization',()=>{
  it('keeps only approved style tokens and supported locales',()=>{
    const profile=sanitizeVisualStyleProfile({
      schema:999,
      styles:{
        'hero.title':{
          textColor:'blue',
          backgroundColor:'white',
          borderColor:'gold',
          hidden:true,
          arbitrary:'nope',
        },
        'bad selector"]':{
          textColor:'green',
        },
        'footer.shell':{
          textColor:'magenta',
        },
        'home.page':{
          textColor:'green',
          backgroundColor:'white',
          borderColor:'gold',
          hidden:true,
        },
      },
      texts:{
        'hero.title':{
          fa:'عنوان',
          en:'Title',
          xx:'ignored',
        },
      },
    })

    expect(profile).toEqual({
      schema:1,
      styles:{
        'hero.title':{
          textColor:'blue',
          backgroundColor:'white',
          borderColor:'gold',
          hidden:true,
        },
        'home.page':{
          backgroundColor:'white',
        },
      },
      texts:{
        'hero.title':{
          fa:'عنوان',
          en:'Title',
        },
      },
    })
  })

  it('protects non-hideable targets and per-target style capabilities',()=>{
    const profile=sanitizeVisualStyleProfile({
      styles:{
        'home.page':{
          textColor:'green',
          backgroundColor:'mint',
          borderColor:'gold',
          hidden:true,
        },
      },
    })

    expect(profile.styles['home.page']).toEqual({backgroundColor:'mint'})
  })

  it('compares normalized profiles deterministically',()=>{
    const a=sanitizeVisualStyleProfile({
      styles:{'hero.title':{textColor:'blue'}},
      texts:{'hero.title':{fa:'سلام'}},
    })
    const b=sanitizeVisualStyleProfile({
      styles:{'hero.title':{textColor:'blue'}},
      texts:{'hero.title':{fa:'سلام'}},
    })
    const c=sanitizeVisualStyleProfile({
      styles:{'hero.title':{textColor:'green'}},
      texts:{'hero.title':{fa:'سلام'}},
    })

    expect(visualProfilesEqual(a,b)).toBe(true)
    expect(visualProfilesEqual(a,c)).toBe(false)
  })
})

 describe('default-hidden heading recovery',()=>{
  it('allows an explicit editor show setting to override the hidden default',()=>{
    setActivePinia(createPinia())
    const visual=useVisualStyleStore()
    visual.patchStyle('home.about.eyebrow',{hidden:false})
    expect(visual.compiledCss).toContain('display:revert!important')
    visual.patchStyle('home.about.eyebrow',{hidden:true})
    expect(visual.compiledCss).toContain('display:none!important')
    expect(visual.compiledCss).not.toContain('display:revert!important')
  })
})

 describe('effective heading visibility',()=>{
  it('shows default-hidden targets with one explicit show and restores the default on reset',()=>{
    setActivePinia(createPinia())
    const visual=useVisualStyleStore()
    expect(targetIsHidden(visual.profile,'home.about.eyebrow')).toBe(true)
    visual.patchStyle('home.about.eyebrow',{hidden:false})
    expect(targetIsHidden(visual.profile,'home.about.eyebrow')).toBe(false)
    visual.resetElement('home.about.eyebrow')
    expect(targetIsHidden(visual.profile,'home.about.eyebrow')).toBe(true)
    expect(targetIsHidden(visual.profile,'home.about.title')).toBe(false)
  })
})
