import {expect,it} from 'vitest'
import {moveOrderedProduct,sameProductOrder} from './productOrder'
it('moves exactly one item first/up/down/last without mutating source or losing others',()=>{
 const rows=[{id:1},{id:2},{id:3},{id:4}]
 expect(moveOrderedProduct(rows,3,'first').map(p=>p.id)).toEqual([3,1,2,4])
 expect(moveOrderedProduct(rows,3,'up').map(p=>p.id)).toEqual([1,3,2,4])
 expect(moveOrderedProduct(rows,2,'down').map(p=>p.id)).toEqual([1,3,2,4])
 expect(moveOrderedProduct(rows,2,'last').map(p=>p.id)).toEqual([1,3,4,2])
 expect(rows.map(p=>p.id)).toEqual([1,2,3,4]);expect(sameProductOrder(rows,moveOrderedProduct(rows,1,'up'))).toBe(true)
 expect(sameProductOrder(rows,moveOrderedProduct(rows,2,'last'))).toBe(false)
})
