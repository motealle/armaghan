import {chromium} from '@playwright/test'
import {spawn} from 'node:child_process'
import {mkdir} from 'node:fs/promises'
import assert from 'node:assert/strict'
const server=spawn(process.execPath,['node_modules/vite/bin/vite.js','--host','127.0.0.1','--port','4199'],{stdio:'ignore'});let browser
try{
 for(let i=0;i<60;i++){try{if((await fetch('http://127.0.0.1:4199/order-acceptance/index.html')).ok)break}catch{}await new Promise(r=>setTimeout(r,200))}
 browser=await chromium.launch({headless:true,args:['--no-sandbox']});await mkdir('../../.build/order-acceptance',{recursive:true})
 for(const width of [375,768,1280]){
  const page=await browser.newPage({viewport:{width,height:900}});page.setDefaultTimeout(15000);page.on('pageerror',e=>console.log('BROWSER ERROR',e.message))
  const rows=[{id:1,code:'11001',availability:'available',sort_order:20},{id:2,code:'11002',availability:'available',sort_order:10},{id:3,code:'12001',availability:'made_to_order',sort_order:0},{id:4,code:'12002',availability:'made_to_order',sort_order:1},{id:5,code:'12003',availability:'unavailable',sort_order:2}]
  let versions={available:0,producible:0},fail=false,writes=0
  const groupRows=key=>rows.filter(p=>(p.availability==='available')===(key==='available')).sort((a,b)=>a.sort_order-b.sort_order||a.code.localeCompare(b.code))
  const snapshot=()=>({groups:Object.fromEntries(['available','producible'].map(key=>[key,{revision:String(versions[key]).padStart(64,'0'),products:groupRows(key).map(p=>({...p,subcategory_code:p.code.slice(0,2),label:'پتو',thumb_url:null}))}]))})
  await page.route('**/*',r=>r.request().url().startsWith('http://127.0.0.1:4199/')?r.continue():r.abort())
  await page.route('**/backend/**',async route=>{
   const req=route.request(),url=req.url()
   if(url.endsWith('/api/csrf-token'))return route.fulfill({json:{token:'synthetic-qa-token'}})
   if(url.endsWith('/api/catalog/products'))return route.fulfill({json:{data:rows.map(p=>({...p,names:{fa:'QA'},subcategory:{code:p.code.slice(0,2),names:{fa:'پتو'}},category:{code:'1',names:{fa:'نوزادی'}},media:[]}))}})
   if(url.endsWith('/api/admin/product-order')){
    if(req.method()==='GET')return route.fulfill({json:snapshot()})
    const body=req.postDataJSON();if(fail||body.revision!==snapshot().groups[body.group].revision)return route.fulfill({status:409,json:{message:'changed'}})
    assert.deepEqual([...body.ids].sort(),groupRows(body.group).map(p=>p.id).sort())
    for(const [index,id] of body.ids.entries())rows.find(p=>p.id===id).sort_order=index
    versions[body.group]++;writes++;return route.fulfill({json:snapshot()})
   }
   return route.abort()
  })
  await page.goto('http://127.0.0.1:4199/order-acceptance/index.html');await page.locator('[data-order-id="5"]').waitFor()
  await page.getByLabel('کد محصول را بنویسید',{exact:true}).fill('۱۲۰۰۳');await page.getByRole('button',{name:'پیدا کن',exact:true}).click()
  await page.locator('[data-order-id="5"]').getByRole('button',{name:'ابتدا',exact:true}).click();assert.equal(writes,0)
  assert.equal(await page.locator('[data-order-id]').first().getAttribute('data-order-id'),'5')
  await page.screenshot({path:'../../.build/order-acceptance/edit-'+width+'.png',fullPage:true})
  assert.ok(await page.getByRole('dialog').evaluate(e=>e.scrollWidth<=e.clientWidth+1))
  await page.getByRole('button',{name:'ذخیره ترتیب',exact:true}).click();await page.getByRole('status').filter({hasText:'ترتیب نمایش در سایت ذخیره شد.'}).waitFor();assert.equal(writes,1)
  const order=await page.evaluate(()=>window.qa.publicOrder());assert.deepEqual(order.map(id=>id-1000000),[2,1,5,3,4])
  await page.getByRole('button',{name:'بستن',exact:true}).last().click();await page.evaluate(()=>window.qa.open());await page.locator('[data-order-id="5"]').waitFor();assert.equal(await page.locator('[data-order-id]').first().getAttribute('data-order-id'),'5')
  await page.locator('[data-order-id="4"] button').first().click();await page.locator('[data-order-id="4"]').getByRole('button',{name:'ابتدا',exact:true}).click();fail=true
  await page.getByRole('button',{name:'ذخیره ترتیب',exact:true}).click();await page.getByRole('alert').waitFor();assert.ok(await page.getByRole('button',{name:'ذخیره ترتیب',exact:true}).isDisabled());assert.equal(await page.locator('[data-order-id]').first().getAttribute('data-order-id'),'4')
  page.once('dialog',d=>d.accept());await page.getByRole('button',{name:'دریافت مجدد',exact:true}).click();await page.waitForFunction(()=>document.querySelector('[data-order-id]')?.getAttribute('data-order-id')==='5')
  await page.evaluate(()=>window.qa.setLocale('en'));await page.screenshot({path:'../../.build/order-acceptance/read-en-'+width+'.png',fullPage:true})
  await page.evaluate(()=>window.qa.logout());await page.getByRole('dialog').waitFor({state:'hidden'});await page.close()
  console.log(width+'px: Persian-code jump, first move, explicit save, reopen persistence, public adapter/group order, conflict retention/reload and logout PASS')
 }
}finally{if(browser)await browser.close();server.kill('SIGTERM')}
