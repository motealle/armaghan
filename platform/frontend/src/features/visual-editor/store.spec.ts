import { describe, expect, it } from 'vitest'
import {
  sanitizeVisualStyleProfile,
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
