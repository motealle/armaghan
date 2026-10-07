import {expect,it} from 'vitest'
import {helpSections,searchHelp} from './guide'

it('retains all source topics and questions with unique identities and clean answers',()=>{
  const items=helpSections.flatMap(section=>section.items)
  expect(helpSections).toHaveLength(21)
  expect(items).toHaveLength(86)
  expect(new Set(items.map(item=>item.id)).size).toBe(items.length)
  expect(items.every(item=>item.question.endsWith('؟')&&item.answer.trim().length>0)).toBe(true)
  expect(JSON.stringify(helpSections)).not.toMatch(/Top of Form|Bottom of Form/)
})

it('searches questions, answers and topic names with Persian normalization while keeping topic selection',()=>{
  expect(searchHelp('کد ۱۱۲۲')[0]?.id).toBe('3')
  expect(searchHelp('كد ١١٢٢')[0]?.id).toBe('3')
  expect(searchHelp('پیش فاکتور').length).toBeGreaterThan(0)
  expect(searchHelp('','15')[0]?.title).toBe('پرداخت')
  expect(searchHelp('پیراهنی که وجود ندارد')).toEqual([])
  expect(searchHelp('۱۱۲۲','15')).toEqual([])
})
