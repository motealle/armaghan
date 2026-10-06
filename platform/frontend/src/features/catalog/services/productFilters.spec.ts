import {expect,it} from 'vitest'
import {readProductFilters,productFilterQuery} from './productFilters'

it('round-trips the selected subcategory, category, search and availability through reloadable query values',()=>{
  const selection={category:'1',subcategory:'11',availability:'made_to_order',query:'11001'}
  const query=productFilterQuery(selection)
  expect(readProductFilters(query)).toEqual(selection)
  expect(productFilterQuery({category:'all',subcategory:'all',availability:'all',query:''})).toEqual({})
  expect(readProductFilters({category:['1'],subcategory:'bad',availability:'bad',q:'x'.repeat(150)})).toEqual({category:'all',subcategory:'all',availability:'all',query:'x'.repeat(100)})
})
