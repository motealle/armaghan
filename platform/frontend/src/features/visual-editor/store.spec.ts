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
        'footer.shell':{},
      },
      texts:{
        'hero.title':{
          fa:'عنوان',
          en:'Title',
        },
      },
    })
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
