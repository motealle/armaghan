import { reactive } from 'vue'
import { describe, expect, it } from 'vitest'
import { categories, products, subMeta } from '@/data/catalog'
import { mergeCatalogSnapshot, type CatalogSnapshot } from './catalogApi'

describe('catalog API staged merge',()=>{
  it('overlays managed backend products, hides managed inactive rows, and keeps unmanaged fallback rows',()=>{
    const snapshot:CatalogSnapshot={
      categories:[{
        code:'1',
        names:{fa:'نوزادی جدید',ar:null,en:'Baby',ku:null},
        subcategories:[{
          code:'11',
          names:{fa:'لباس نوزادی جدید',ar:null,en:null,ku:null},
        }],
      }],
      products:[{
        id:77,
        code:'11001',
        names:{fa:'نام داخلی سرور',ar:null,en:null,ku:null},
        availability:'made_to_order',
        sort_order:10,
        category:{code:'1',names:{fa:'نوزادی جدید',ar:null,en:null,ku:null}},
        subcategory:{code:'11',names:{fa:'لباس نوزادی جدید',ar:null,en:null,ku:null}},
      }],
      managedCategoryCodes:['1'],
      managedSubcategoryCodes:['11','12'],
      managedProductCodes:['11001','11002'],
    }

    const merged=mergeCatalogSnapshot(snapshot,products,categories)
    const updated=merged.products.find(product=>product.code==='11001')

    expect(updated?.id).toBe(1)
    expect(updated?.backendId).toBe(77)
    expect(updated?.availability).toBe('made_to_order')
    expect(updated?.subcategoryName).toBe('لباس نوزادی جدید')
    expect(merged.products.some(product=>product.code==='11002')).toBe(false)
    expect(merged.products.some(product=>product.code==='12001')).toBe(true)

    const baby=merged.categories.find(category=>category.code==='1')
    expect(baby?.name).toBe('نوزادی جدید')
    expect(baby?.subcategories.map(subcategory=>subcategory.code)).toEqual(['11'])
    expect(merged.categories.some(category=>category.code==='2')).toBe(true)
  })

  it('uses canonical specification definitions and values instead of prototype defaults, restoring subcategory defaults for empty definitions',()=>{
    const base:CatalogSnapshot={categories:[],products:[{
      id:1,code:'11001',names:{fa:'محصول',ar:null,en:null,ku:null},availability:'available',sort_order:0,
      category:null,subcategory:null,specifications:[{key:'fabric',locked:true,labels:{fa:'جنس',ar:null,en:'Fabric',ku:null},value_text:'پنبه'}],
    }],managedCategoryCodes:[],managedSubcategoryCodes:[],managedProductCodes:['11001']}
    const merged=mergeCatalogSnapshot(base)
    const product=merged.products.find(p=>p.code==='11001')!
    expect(product.specs).toEqual({locked:['جنس'],negotiable:[]})
    expect(product.specificationValues?.[0]?.value_text).toBe('پنبه')
    expect(product.specificationValues?.[0]?.labels.en).toBe('Fabric')
    base.products[0]!.specifications=[]
    expect(mergeCatalogSnapshot(base).products.find(p=>p.code==='11001')?.specs).toEqual(subMeta['11'].specs)
  })

  it('adds a new backend product with a collision-safe frontend id and existing subcategory media defaults',()=>{
    const snapshot:CatalogSnapshot={
      categories:[],
      products:[{
        id:2,
        code:'11099',
        names:{fa:'محصول جدید',ar:null,en:null,ku:null},
        availability:'available',
        sort_order:99,
        category:{code:'1',names:{fa:'نوزادی',ar:null,en:null,ku:null}},
        subcategory:{code:'11',names:{fa:'لباس نوزادی',ar:null,en:null,ku:null}},
      }],
      managedCategoryCodes:[],
      managedSubcategoryCodes:[],
      managedProductCodes:['11099'],
    }

    const merged=mergeCatalogSnapshot(snapshot,products,categories)
    const added=merged.products.find(product=>product.code==='11099')

    expect(added?.id).toBe(1_000_002)
    expect(added?.backendId).toBe(2)
    expect(added?.gallery?.length).toBeGreaterThan(0)
    expect(added?.specs.negotiable.length).toBeGreaterThan(0)
  })
  it('prefers backend product media while retaining local fallback when backend media is absent',()=>{
    const snapshot:CatalogSnapshot={
      categories:[],
      products:[{
        id:1,
        code:'11001',
        names:{fa:'نام داخلی سرور',ar:null,en:null,ku:null},
        availability:'available',
        sort_order:10,
        media:[{
          id:'media-1',
          url:'/backend/api/catalog/media/1/card',
          thumb_url:'/backend/api/catalog/media/1/thumb',
          detail_url:'/backend/api/catalog/media/1/detail',
        }],
        category:{code:'1',names:{fa:'نوزادی',ar:null,en:null,ku:null}},
        subcategory:{code:'11',names:{fa:'لباس نوزادی',ar:null,en:null,ku:null}},
      }],
      managedCategoryCodes:[],
      managedSubcategoryCodes:[],
      managedProductCodes:['11001'],
    }

    const merged=mergeCatalogSnapshot(snapshot,products,categories)
    const product=merged.products.find(item=>item.code==='11001')

    expect(product?.image).toBe('/backend/api/catalog/media/1/card?v=20261005-card-gallery-3')
    expect(product?.gallery).toEqual(['/backend/api/catalog/media/1/detail?v=20261005-card-gallery-3'])

    const reactiveMerged=mergeCatalogSnapshot(
      snapshot,
      reactive(structuredClone(products)) as typeof products,
      reactive(structuredClone(categories)) as typeof categories,
    )
    expect(reactiveMerged.products.find(item=>item.code==='11001')?.image)
      .toBe('/backend/api/catalog/media/1/card?v=20261005-card-gallery-3')
  })

})


it('survives repeated hydration with nested gallery proxies from an earlier merge',()=>{
  const snapshot:CatalogSnapshot={categories:[],products:[{id:1,code:'11001',names:{fa:'Test',ar:null,en:null,ku:null},availability:'available',sort_order:0,category:null,subcategory:null}],managedProductCodes:['11001'],managedCategoryCodes:[],managedSubcategoryCodes:[]}
  let current={products:reactive(structuredClone(products)),categories:reactive(structuredClone(categories))}
  for(let i=0;i<3;i++){
    const next=mergeCatalogSnapshot(snapshot,current.products,current.categories)
    current={products:reactive(next.products),categories:reactive(next.categories)}
    expect(current.products.find(p=>p.code==='11001')?.backendId).toBe(1)
  }
})


it('restores all six Test28 subcategory groups even from previously emptied cached cards',()=>{
  const codes=Object.keys(subMeta) as Array<keyof typeof subMeta>
  const snapshot:CatalogSnapshot={categories:[],products:codes.map((code,i)=>({id:i+1,code:code+'001',names:{fa:'Test',ar:null,en:null,ku:null},availability:'available',sort_order:i,category:null,subcategory:null,specifications:[]})),managedProductCodes:[],managedCategoryCodes:[],managedSubcategoryCodes:[]}
  const cached=products.map(p=>({...p,specs:{locked:[],negotiable:[]},specificationValues:[]}))
  const merged=mergeCatalogSnapshot(snapshot,cached)
  for(const code of codes){
    const product=merged.products.find(p=>p.code===code+'001')!
    expect(product.specs).toEqual(subMeta[code].specs)
    expect(product.specificationValues).toBeUndefined()
  }
})


it('reconciles a renamed server product by immutable backend identity and removes stale duplicate cards',()=>{
  const row={id:77,code:'11199',names:{fa:'Edited',ar:null,en:null,ku:null},availability:'made_to_order' as const,sort_order:0,category:null,subcategory:null}
  const snapshot:CatalogSnapshot={categories:[],products:[row],managedProductCodes:['11199'],managedCategoryCodes:[],managedSubcategoryCodes:[]}
  const original={...structuredClone(products[0]!),backendId:77}
  const duplicate={...original,id:1_000_077,code:'11199'}
  const merged=mergeCatalogSnapshot(snapshot,[original,duplicate]).products
  expect(merged).toHaveLength(1)
  expect(merged[0]).toMatchObject({id:original.id,backendId:77,code:'11199',name:'Edited',availability:'made_to_order'})
  expect(mergeCatalogSnapshot(snapshot,merged).products).toEqual(merged)
})

it('does not resurrect absent server-owned products as local fallback after rename/archive',()=>{
  const snapshot:CatalogSnapshot={categories:[],products:[],managedProductCodes:[],managedCategoryCodes:[],managedSubcategoryCodes:[]}
  const local=structuredClone(products[1]!)
  const stale={...structuredClone(products[0]!),backendId:77}
  expect(mergeCatalogSnapshot(snapshot,[stale,local]).products).toEqual([local])
})
