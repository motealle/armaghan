import { chromium } from '@playwright/test'
import { spawn } from 'node:child_process'
import { readFile, mkdir, writeFile } from 'node:fs/promises'
import { createHash } from 'node:crypto'
import assert from 'node:assert/strict'
const server=spawn(process.execPath,['node_modules/vite/bin/vite.js','--host','127.0.0.1','--port','4197'],{stdio:'ignore'})
let browser
try{
 for(let i=0;i<60;i++){try{if((await fetch('http://127.0.0.1:4197/format-acceptance/index.html')).ok)break}catch{}await new Promise(r=>setTimeout(r,200))}
 browser=await chromium.launch({headless:true,args:['--no-sandbox']})
 const page=await browser.newPage()
 await page.route('**/*',route=>route.request().url().startsWith('http://127.0.0.1:4197/')?route.continue():route.abort())
 await page.goto('http://127.0.0.1:4197/format-acceptance/index.html')
 await page.waitForFunction(()=>typeof window.prepare==='function')
 const hashes=JSON.parse(await readFile('format-acceptance/fixtures/SHA256SUMS.json','utf8'))
 const formats={jpg:'image/jpeg',png:'image/x-png',webp:'image/webp',gif:'image/gif',bmp:'image/bmp',avif:'image/avif',heic:'image/heic',heif:'image/heif'}
 const output='../../.build/image-format-uploads';await mkdir(output,{recursive:true});const manifest=[]
 for(const [ext,type] of Object.entries(formats)){
  const name='sample.'+ext;const bytes=await readFile('format-acceptance/fixtures/'+name)
  assert.equal(createHash('sha256').update(bytes).digest('hex'),hashes[name])
  await page.locator('#picked').setInputFiles({name:'۲۰۲۶۱۰۱۷_'+name,mimeType:type,buffer:bytes})
  const result=await page.evaluate(()=>window.prepareSelected())
  assert.ok(['image/jpeg','image/png','image/webp'].includes(result.type));assert.deepEqual(result.dimensions,[64,48]);assert.ok(result.bytes.length>0);assert.ok(result.pixels[0]>120&&result.pixels[1]>80&&result.pixels[2]<120&&result.pixels[3]===255,ext+': retained gold shape pixel')
  const target=ext+'-'+result.name;await writeFile(output+'/'+target,Buffer.from(result.bytes))
  manifest.push({source:ext,path:target,type:result.type});console.log(ext.toUpperCase()+': real file picker, retained bytes after reset, browser decode and canonical output PASS')
 }
 const large=Buffer.from(await page.evaluate(()=>window.largePng()),'base64')
 assert.ok(large.length>1_400_000&&large.length<20*1024*1024)
 await page.locator('#picked').setInputFiles({name:'۲۰۲۶۱۰۱۷_۱۲۳۴۵۶.png',mimeType:'image/png',buffer:large})
 const preparedLarge=await page.evaluate(()=>window.prepareSelected())
 assert.deepEqual(preparedLarge.dimensions,[1920,1440]);assert.ok(preparedLarge.bytes.length<8*1024*1024)
 assert.ok(preparedLarge.pixels[0]>120&&preparedLarge.pixels[1]>80&&preparedLarge.pixels[2]<120&&preparedLarge.pixels[3]===255)
 console.log('Phone-size PNG '+large.length+' bytes: real picker, reset, resize, retained pixels PASS')
 for(const data of [{name:'fake.png',bytes:[60,115,118,103,47,62],type:'image/png'},{name:'broken.heic',bytes:[0,0,0,24,102,116,121,112,104,101,105,99,0,0,0,0],type:'image/heic'}]){
  const rejected=await page.evaluate(async d=>{try{await window.prepare(d.name,d.bytes,d.type);return false}catch{return true}},data)
  assert.ok(rejected);console.log(data.name+': invalid content rejected PASS')
 }
 await writeFile(output+'/manifest.json',JSON.stringify(manifest,null,2))
}finally{if(browser)await browser.close();server.kill('SIGTERM')}
