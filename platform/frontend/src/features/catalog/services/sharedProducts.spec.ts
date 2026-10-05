import {expect,it,vi} from 'vitest'
import {fetchSharedProducts} from './catalogApi'
it('loads a server-only shared product without the recipients local catalog',async()=>{
 const fetchMock=vi.fn().mockResolvedValue({ok:true,status:200,json:async()=>({data:[{id:91,code:'11999',names:{fa:'محصول جدید',en:null,ar:null,ku:null},availability:'available',sort_order:1,category:{code:'1',names:{fa:'زنانه'}},subcategory:{code:'11',names:{fa:'مانتو'}},media:[]}],meta:{last_page:1},catalog:{managed_codes:['11999']}})})
 vi.stubGlobal('fetch',fetchMock)
 const products=await fetchSharedProducts(['11999'])
 expect(products.map(p=>p.code)).toEqual(['11999'])
 expect(products[0]?.backendId).toBe(91)
 expect(fetchMock.mock.calls[0]?.[0]).toContain('codes%5B%5D=11999')
 vi.unstubAllGlobals()
})
