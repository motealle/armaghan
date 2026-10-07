import {expect,it,vi} from 'vitest'
const request=vi.hoisted(()=>vi.fn())
vi.mock('@/features/auth/services/customerSessionApi',()=>({requestJson:request}))
import {resolveCatalogProduct} from './adminApi'
it('resolves server cards by identity even if the cached code is stale',async()=>{
 request.mockResolvedValue({product:{id:77,code:'11199'}})
 expect(await resolveCatalogProduct({backendId:77,code:'11001'})).toEqual({id:77,code:'11199'})
 expect(request).toHaveBeenCalledExactlyOnceWith('/api/admin/products/77')
})
it('propagates missing server identity without retrying as a create/sample',async()=>{
 request.mockRejectedValue(new Error('404'))
 await expect(resolveCatalogProduct({backendId:77,code:'11001'})).rejects.toThrow('404')
 expect(request).toHaveBeenCalledTimes(1)
})
it('uses exact code lookup only for a sample without server identity',async()=>{
 request.mockResolvedValue({products:[{id:77,code:'11001'}]})
 expect(await resolveCatalogProduct({code:'11001'},true)).toEqual({id:77,code:'11001'})
 expect(request).toHaveBeenCalledExactlyOnceWith('/api/admin/products?code=11001')
})

it('rejects an unregistered card in ordinary editing without any create/read request',async()=>{
 await expect(resolveCatalogProduct({code:'old-sample'})).rejects.toThrow('persisted server identity')
 expect(request).not.toHaveBeenCalled()
})
