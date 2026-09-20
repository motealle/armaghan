#!/usr/bin/env node
import { mkdir, rm, stat, writeFile } from 'node:fs/promises'
import path from 'node:path'
import { fileURLToPath } from 'node:url'
import { execFileSync } from 'node:child_process'

const HERE=path.dirname(fileURLToPath(import.meta.url))
const FRONTEND=path.resolve(HERE,'..')
const PUBLIC_DIR=path.join(FRONTEND,'public','images','digikala')
const GENERATED=path.join(FRONTEND,'src','data','digikalaMedia.generated.ts')
const requiredPerProduct = 2
const browserUa='Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36'

const sourceSets={
  '11':[
    {
      url:'https://dkstatics-public.digikala.com/digikala-faq/536c0e0730a48c5558dcb4b76e782d8ba1182932_1633781452.jpg',
      source:'Digikala FAQ category artwork — ست لباس راحتی نوزاد',
      page:'https://www.digikala.com/faq/question/1026/',
    },
    {
      url:'https://dkstatics-public.digikala.com/digikala-faq/536c0e0730a48c5558dcb4b76e782d8ba1182932_1633167498.jpg',
      source:'Digikala FAQ category artwork — تاپ و تی شرت راحتی نوزاد',
      page:'https://www.digikala.com/faq/question/960/',
    },
  ],
  '12':[
    {
      url:'https://dkstatics-public.digikala.com/digikala-products/36f4be4ec8f050340502a934387e55cbfacbb6d9_1670225649.jpg?x-oss-process=image/resize,m_lfit,h_800,w_800/format,webp/quality,q_90',
      source:'Digikala catalog image — baby/blanket prototype set',
      page:'https://www.digikala.com/',
    },
    {
      url:'https://dkstatics-public.digikala.com/digikala-products/86b8fe2346c1116620bbb1af06ed3e288519aa30_1670225650.jpg?x-oss-process=image/resize,m_lfit,h_800,w_800/format,webp/quality,q_90',
      source:'Digikala catalog image — baby/blanket prototype set',
      page:'https://www.digikala.com/',
    },
  ],
  '21':[
    {
      url:'https://dkstatics-public.digikala.com/digikala-faq/536c0e0730a48c5558dcb4b76e782d8ba1182932_1635590308.jpg',
      source:'Digikala FAQ category artwork — ست لباس دخترانه',
      page:'https://www.digikala.com/faq/question/1300/',
    },
    {
      url:'https://dkstatics-public.digikala.com/digikala-faq/536c0e0730a48c5558dcb4b76e782d8ba1182932_1635590339.jpg',
      source:'Digikala FAQ category artwork — ست لباس راحتی دخترانه',
      page:'https://www.digikala.com/faq/question/1306/',
    },
  ],
  '22':[
    {
      url:'https://dkstatics-public.digikala.com/digikala-faq/536c0e0730a48c5558dcb4b76e782d8ba1182932_1637998012.jpg',
      source:'Digikala FAQ category artwork — ست لباس پسرانه',
      page:'https://www.digikala.com/faq/question/1296/',
    },
    {
      url:'https://dkstatics-public.digikala.com/digikala-faq/536c0e0730a48c5558dcb4b76e782d8ba1182932_1638126610.jpg',
      source:'Digikala FAQ category artwork — ست لباس راحتی پسرانه',
      page:'https://www.digikala.com/faq/question/1303/',
    },
  ],
  '31':[
    {
      url:'https://dkstatics-public.digikala.com/digikala-faq/536c0e0730a48c5558dcb4b76e782d8ba1182932_1635595665.jpg',
      source:'Digikala FAQ category artwork — لباس زنانه',
      page:'https://www.digikala.com/faq/question/1377/',
    },
    {
      url:'https://dkstatics-public.digikala.com/digikala-faq/536c0e0730a48c5558dcb4b76e782d8ba1182932_1633170455.jpg',
      source:'Digikala FAQ category artwork — تاپ و تی‌شرت راحتی زنانه',
      page:'https://www.digikala.com/faq/question/965/',
    },
  ],
  '32':[
    {
      url:'https://dkstatics-public.digikala.com/digikala-faq/536c0e0730a48c5558dcb4b76e782d8ba1182932_1633851658.jpg',
      source:'Digikala FAQ category artwork — سویشرت و هودی ورزشی زنانه',
      page:'https://www.digikala.com/faq/question/1043/',
    },
    {
      url:'https://dkstatics-public.digikala.com/digikala-faq/536c0e0730a48c5558dcb4b76e782d8ba1182932_1633844973.jpg',
      source:'Digikala FAQ category artwork — سرهمی و شلوار ورزشی زنانه',
      page:'https://www.digikala.com/faq/question/1039/',
    },
  ],
}

const groups=[
  {sub:'11',codes:['11001','11002','11003']},
  {sub:'12',codes:['12001','12002','12003']},
  {sub:'21',codes:['21001','21002','21003']},
  {sub:'22',codes:['22001','22002','22003']},
  {sub:'31',codes:['31001','31002','31003']},
  {sub:'32',codes:['32001','32002','32003']},
]

function extensionFromUrl(url){
  try{
    const pathname=new URL(url).pathname.toLowerCase()
    const match=pathname.match(/\.(webp|png|avif|jpe?g)$/)
    if(match)return match[1]==='jpeg'?'jpg':match[1]
  }catch{}
  return 'jpg'
}

async function download(asset,basename){
  const ext=extensionFromUrl(asset.url)
  const filename=`${basename}.${ext}`
  const target=path.join(PUBLIC_DIR,filename)
  try{
    execFileSync('curl',[
      '--fail','--silent','--show-error','--location','--max-redirs','8',
      '--retry','3','--retry-delay','1','--retry-all-errors',
      '--connect-timeout','15','--max-time','90',
      '--user-agent',browserUa,
      '--header','Accept: image/avif,image/webp,image/*,*/*;q=0.8',
      '--output',target,asset.url,
    ],{stdio:['ignore','inherit','pipe'],maxBuffer:8*1024*1024})
  }catch(error){
    throw new Error(`Digikala CDN download failed for ${asset.url}: ${error?.stderr?.toString?.()||error?.message||error}`)
  }
  const info=await stat(target)
  if(info.size<3000)throw new Error(`Digikala image too small (${info.size} bytes): ${asset.url}`)
  return {filename,path:`./images/digikala/${filename}`,bytes:info.size}
}

async function main(){
  await rm(PUBLIC_DIR,{recursive:true,force:true})
  await mkdir(PUBLIC_DIR,{recursive:true})
  const mapping={}
  const manifest={
    source:'Digikala',
    authorization_basis:'project-owner-reported permission/contract; prototype use',
    fetched_at:new Date().toISOString(),
    mode:'deterministic-digikala-cdn-seed',
    products:[],
  }

  for(const group of groups){
    const source=sourceSets[group.sub]
    if(!source||source.length!==requiredPerProduct)throw new Error(`Missing exactly 2 Digikala seed images for subcategory ${group.sub}`)
    // Download one stable pair per subcategory and reuse it across its 3 mock products.
    const local=[]
    for(let n=0;n<requiredPerProduct;n++){
      local.push(await download(source[n],`sub-${group.sub}-0${n+1}`))
    }
    for(const code of group.codes){
      mapping[code]=local.map(item=>item.path)
      manifest.products.push({
        prototype_code:code,
        subcategory:group.sub,
        source_page:source[0].page,
        source_product_url:source[0].page,
        title_fa:source[0].source,
        images:local.map((item,index)=>({
          file:item.filename,
          bytes:item.bytes,
          original_url:source[index].url,
          source_label:source[index].source,
          source_page:source[index].page,
        })),
      })
    }
  }

  const category={
    '1':mapping['11001'][0],
    '2':mapping['21001'][0],
    '3':mapping['31001'][0],
  }
  const hero=[mapping['11001'][1],mapping['22001'][0],mapping['32001'][0]]
  const ts=`// AUTO-GENERATED by scripts/fetch-digikala-assets.mjs. Do not hand edit.\nexport const digikalaMediaByProductCode: Record<string,string[]> = ${JSON.stringify(mapping,null,2)}\nexport const digikalaCategoryMedia: Record<string,string> = ${JSON.stringify(category,null,2)}\nexport const digikalaHeroMedia: string[] = ${JSON.stringify(hero,null,2)}\n`
  await writeFile(GENERATED,ts,'utf8')
  await writeFile(path.join(PUBLIC_DIR,'manifest.json'),JSON.stringify(manifest,null,2),'utf8')
  console.log(`Digikala media ready: ${manifest.products.length} products / ${manifest.products.length*requiredPerProduct} product-image assignments / 12 downloaded source files`)
}

main().catch((error)=>{
  console.error('Digikala media fetch failed:',error)
  if(process.env.DIGIKALA_FETCH_STRICT==='1')process.exit(1)
  console.warn('Keeping repository fallback media map for local development.')
})
