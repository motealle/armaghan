#!/usr/bin/env node
import { mkdir, rm, writeFile } from 'node:fs/promises'
import path from 'node:path'
import { fileURLToPath } from 'node:url'

const HERE=path.dirname(fileURLToPath(import.meta.url))
const FRONTEND=path.resolve(HERE,'..')
const PUBLIC_DIR=path.join(FRONTEND,'public','images','digikala')
const GENERATED=path.join(FRONTEND,'src','data','digikalaMedia.generated.ts')
const requiredPerProduct = 2

const groups=[
  {sub:'11',query:'ست لباس نوزادی',codes:['11001','11002','11003']},
  {sub:'12',query:'پتو نوزادی',codes:['12001','12002','12003']},
  {sub:'21',query:'ست لباس دخترانه بچگانه',codes:['21001','21002','21003']},
  {sub:'22',query:'ست لباس پسرانه بچگانه',codes:['22001','22002','22003']},
  // Prefer modest/product-led search terms for women. Final visual review remains required.
  {sub:'31',query:'تونیک زنانه بلند حجاب',codes:['31001','31002','31003']},
  {sub:'32',query:'ست ورزشی زنانه پوشیده',codes:['32001','32002','32003']},
]

const headers={
  'Accept':'application/json',
  'User-Agent':'Mozilla/5.0 (compatible; ArmaghanPrototype/1.0; +https://armaghantrading.com)',
}
const sleep=(ms)=>new Promise(resolve=>setTimeout(resolve,ms))

async function getJson(url,tries=3){
  let last
  for(let attempt=1;attempt<=tries;attempt++){
    try{
      const response=await fetch(url,{headers,redirect:'follow'})
      if(!response.ok)throw new Error(`${response.status} ${response.statusText}`)
      return await response.json()
    }catch(error){
      last=error
      await sleep(350*attempt)
    }
  }
  throw last
}

function productUrl(product,id){
  const uri=product?.url?.uri
  if(typeof uri==='string'&&uri)return uri.startsWith('http')?uri:`https://www.digikala.com${uri.startsWith('/')?'':'/'}${uri}`
  return `https://www.digikala.com/product/dkp-${id}/`
}

function imageUrls(product){
  const urls=[]
  const push=(value)=>{
    if(typeof value==='string'&&/^https?:\/\//.test(value)&&!urls.includes(value))urls.push(value)
  }
  const main=product?.images?.main?.url
  if(Array.isArray(main))main.forEach(push)
  const list=product?.images?.list
  if(Array.isArray(list)){
    for(const item of list){
      if(Array.isArray(item?.url))item.url.forEach(push)
    }
  }
  return urls
}

function extension(contentType,url){
  const type=(contentType??'').toLowerCase()
  if(type.includes('webp'))return 'webp'
  if(type.includes('png'))return 'png'
  if(type.includes('avif'))return 'avif'
  if(type.includes('jpeg')||type.includes('jpg'))return 'jpg'
  const match=new URL(url).pathname.toLowerCase().match(/\.(webp|png|avif|jpe?g)$/)
  return match ? (match[1]==='jpeg'?'jpg':match[1]) : 'jpg'
}

async function download(url,basename){
  const response=await fetch(url,{headers:{'User-Agent':headers['User-Agent'],'Accept':'image/avif,image/webp,image/*,*/*;q=0.8'},redirect:'follow'})
  if(!response.ok)throw new Error(`image ${response.status}: ${url}`)
  const bytes=Buffer.from(await response.arrayBuffer())
  if(bytes.length<3000)throw new Error(`image too small (${bytes.length} bytes): ${url}`)
  const ext=extension(response.headers.get('content-type'),response.url||url)
  const filename=`${basename}.${ext}`
  await writeFile(path.join(PUBLIC_DIR,filename),bytes)
  return {filename,path:`./images/digikala/${filename}`,bytes:bytes.length,contentType:response.headers.get('content-type')||''}
}

async function search(query){
  const url=new URL('https://api.digikala.com/v1/search/')
  url.searchParams.set('q',query)
  url.searchParams.set('page','1')
  const payload=await getJson(url)
  return payload?.data?.products ?? []
}

async function details(id){
  const payload=await getJson(`https://api.digikala.com/v2/product/${id}/`)
  return payload?.data?.product ?? payload?.data ?? null
}

async function main(){
  await rm(PUBLIC_DIR,{recursive:true,force:true})
  await mkdir(PUBLIC_DIR,{recursive:true})
  const mapping={}
  const manifest={source:'Digikala',authorization_basis:'project-owner-reported permission/contract; prototype use',fetched_at:new Date().toISOString(),products:[]}

  for(const group of groups){
    const candidates=await search(group.query)
    const selected=[]
    for(const item of candidates){
      if(selected.length>=group.codes.length)break
      const id=Number(item?.id)
      if(!Number.isFinite(id)||selected.some(x=>x.id===id))continue
      try{
        const detail=await details(id)
        const urls=imageUrls(detail)
        if(urls.length>=requiredPerProduct)selected.push({id,detail,urls})
      }catch(error){
        console.warn(`Skipping Digikala product ${id}: ${error}`)
      }
      await sleep(120)
    }
    if(selected.length<group.codes.length)throw new Error(`Digikala query "${group.query}" returned only ${selected.length} products with 2+ images`)

    for(let i=0;i<group.codes.length;i++){
      const code=group.codes[i]
      const picked=selected[i]
      const files=[]
      const originals=[]
      for(let n=0;n<requiredPerProduct;n++){
        const original=picked.urls[n]
        const saved=await download(original,`${code}-0${n+1}`)
        files.push(saved.path)
        originals.push({url:original,file:saved.filename,bytes:saved.bytes,content_type:saved.contentType})
        await sleep(80)
      }
      mapping[code]=files
      manifest.products.push({
        prototype_code:code,
        subcategory:group.sub,
        query:group.query,
        digikala_product_id:picked.id,
        source_product_url:productUrl(picked.detail,picked.id),
        title_fa:picked.detail?.title_fa ?? '',
        images:originals,
      })
    }
  }

  const category={
    '1':mapping['11001'][0],
    '2':mapping['21001'][0],
    '3':mapping['31001'][0],
  }
  const hero=[mapping['11001'][1],mapping['22002'][0],mapping['32001'][0]]
  const ts=`// AUTO-GENERATED by scripts/fetch-digikala-assets.mjs. Do not hand edit.\nexport const digikalaMediaByProductCode: Record<string,string[]> = ${JSON.stringify(mapping,null,2)}\nexport const digikalaCategoryMedia: Record<string,string> = ${JSON.stringify(category,null,2)}\nexport const digikalaHeroMedia: string[] = ${JSON.stringify(hero,null,2)}\n`
  await writeFile(GENERATED,ts,'utf8')
  await writeFile(path.join(PUBLIC_DIR,'manifest.json'),JSON.stringify(manifest,null,2),'utf8')
  console.log(`Digikala media ready: ${manifest.products.length} products / ${manifest.products.length*requiredPerProduct} images`)
}

main().catch((error)=>{
  console.error('Digikala media fetch failed:',error)
  if(process.env.DIGIKALA_FETCH_STRICT==='1')process.exit(1)
  console.warn('Keeping repository fallback media map for local development.')
})
