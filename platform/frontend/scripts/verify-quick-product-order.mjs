import {chromium} from '@playwright/test'
import {spawn} from 'node:child_process'
import {mkdir} from 'node:fs/promises'
import assert from 'node:assert/strict'
const server=spawn(process.execPath,['node_modules/vite/bin/vite.js','--host','127.0.0.1','--port','4197'],{stdio:'ignore'});let browser
try{
 for(let i=0;i<60;i++){try{if((await fetch('http://127.0.0.1:4197/quick-order-acceptance/index.html')).ok)break}catch{}await new Promise(r=>setTimeout(r,200))}
 browser=await chromium.launch({headless:true,args:['--no-sandbox']});await mkdir('../../.build/quick-order-acceptance',{recursive:true})
 for(const width of [375,768,1280]){
  const page=await browser.newPage({viewport:{width,height:900}});page.setDefaultTimeout(15000)
  const errors=[];page.on('pageerror',e=>errors.push(e.message))
  const rows=[{id:10,code:'11001',availability:'available',sort_order:50},...Array.from({length:8},(_,i)=>({id:i+1,code:'1200'+(i+1),availability:'made_to_order',sort_order:i}))];let version=0,writes=0,fail=false
  const sorted=()=>[rows[0],...rows.slice(1).sort((a,b)=>a.sort_order-b.sort_order)]
  const snapshot=()=>({groups:Object.fromEntries(['available','producible'].map(group=>[group,{revision:String(version).padStart(64,'0'),products:sorted().filter(p=>(p.availability==='available')===(group==='available')).map(p=>({...p,subcategory_code:p.code.slice(0,2),label:'پتوی نوزادی',thumb_url:null}))}]))})
  await page.route('**/*',r=>r.request().url().startsWith('http://127.0.0.1:4197/')?r.continue():r.abort())
  await page.route('**/backend/**',async route=>{
   const req=route.request(),url=new URL(req.url()),path=url.pathname
   if(path.endsWith('/api/csrf-token'))return route.fulfill({json:{token:'synthetic-qa-token'}})
   if(path.endsWith('/api/catalog/products'))return route.fulfill({json:{data:rows.map(p=>({...p,names:{fa:'محصول آزمایشی'},category:{code:'1',names:{fa:'نوزادی'}},subcategory:{code:p.code.slice(0,2),names:{fa:'پتوی نوزادی'}},media:[]}))}})
   if(path.endsWith('/api/admin/product-taxonomy'))return route.fulfill({json:{subcategories:[{id:1,code:'12',category_code:'1',name:'پتو',category_name:'نوزادی',active:true,schema_revision:'a'.repeat(64),specifications:[]}]}})
   if(path.endsWith('/api/admin/products'))return route.fulfill({json:{products:sorted().slice(0,4).map(p=>({...p,subcategory_id:1,name_fa:'محصول',active:true,subcategory_code:p.code.slice(0,2),category_code:'1',revision:'a'.repeat(64),tags:[],media:[],specifications:[]})),page:1,last_page:3,total:rows.length}})
   if(path.endsWith('/api/admin/product-order')){
    if(req.method()==='GET')return route.fulfill({json:snapshot()})
    const body=req.postDataJSON();if(fail||body.revision!==snapshot().groups[body.group].revision)return route.fulfill({status:409,json:{message:'changed'}})
    assert.equal(body.group,'producible');assert.deepEqual([...body.ids].sort(),[1,2,3,4,5,6,7,8])
    for(const [index,id] of body.ids.entries())rows.find(p=>p.id===id).sort_order=index
    version++;writes++;return route.fulfill({json:snapshot()})
   }
   // Home media has independent loading state; no external service is required here.
   return route.fulfill({status:503,json:{message:'synthetic optional home media unavailable'}})
  })
  await page.goto('http://127.0.0.1:4197/quick-order-acceptance/index.html')
  const row=code=>page.locator('tbody tr').filter({has:page.locator('code',{hasText:code})})
  await row('12003').locator('[data-move=first]:enabled').waitFor()
  assert.equal(await row('12003').getByRole('button',{name:'ویرایش محصول',exact:true}).innerText(),'')
  assert.equal(await row('12003').locator('[data-move]').count(),4)
  for(const [action,expected] of [['first',[3,1,2,4,5,6,7,8]],['down',[1,3,2,4,5,6,7,8]],['up',[3,1,2,4,5,6,7,8]]]){
   const before=writes;await row('12003').locator('[data-move='+action+']').click();await page.waitForFunction(()=>document.querySelector('[role=status]')?.textContent?.includes('ذخیره شد'))
   await page.waitForTimeout(100);assert.equal(writes,before+1);assert.deepEqual(sorted().slice(1).map(p=>p.id),expected)
   await row('12003').locator('[data-move=last]:enabled').waitFor()
  }
  if(width===375)assert.ok(await page.locator('.data-table-shell').evaluate(e=>e.scrollWidth<=e.clientWidth+1))
  await page.screenshot({path:'../../.build/quick-order-acceptance/table-'+width+'.png',fullPage:true})
  await row('12003').locator('[data-move=last]').click();await page.waitForFunction(()=>!Array.from(document.querySelectorAll('tbody code')).some(e=>e.textContent==='12003'));assert.equal(sorted().at(-1).id,3)
  await page.evaluate(()=>window.qa.mode('grid'))
  const card=code=>page.locator('article.product-card[data-product-code="'+code+'"]')
  await card('12008').locator('[data-move=first]:enabled').waitFor();await card('12008').locator('[data-move=first]').click()
  await page.waitForFunction(()=>window.qa.order()[1]==='12008');assert.equal(await page.locator('.pswp').count(),0);assert.equal(await page.getByRole('dialog').count(),0)
  const toolbar=card('12008').locator('[data-card-order]');const box=await toolbar.boundingBox(),media=await card('12008').locator('.product-card-media').boundingBox();assert.ok(box.y>=media.y&&box.y+box.height<=media.y+media.height+1)
  assert.equal(await toolbar.locator('button').allTextContents().then(t=>t.join('')),'')
  await page.screenshot({path:'../../.build/quick-order-acceptance/cards-'+width+'.png',fullPage:true})
  await page.evaluate(()=>window.qa.filter('12002'));fail=true;const previous=sorted().map(p=>p.id)
  await card('12002').locator('[data-move=first]').click();await page.getByRole('alert').waitFor();assert.deepEqual(sorted().map(p=>p.id),previous);assert.ok(await card('12002').locator('[data-move=first]').isDisabled())
  fail=false;await page.getByRole('button',{name:'دریافت مجدد',exact:true}).click();await card('12002').locator('[data-move=first]:enabled').waitFor();await card('12002').locator('[data-move=first]').click();await page.waitForFunction(()=>window.qa.order()[0]==='12002')
  // Actual Home component, with its existing optional recommended-products section enabled.
  await page.evaluate(()=>{window.qa.filter('');window.qa.mode('home')});await card('12002').locator('[data-move=down]:enabled').waitFor();await card('12002').locator('[data-move=down]').click();await page.waitForFunction(()=>window.qa.order()[1]==='12008')
  assert.equal(await page.locator('.product-card').count(),6);await page.screenshot({path:'../../.build/quick-order-acceptance/home-'+width+'.png',fullPage:true})
  await page.evaluate(()=>window.qa.logout());await page.waitForFunction(()=>document.querySelectorAll('[data-move]').length===0)
  assert.deepEqual(errors,[]);await page.close();console.log(width+'px: four icon table actions, global full-group persistence, card overlay, filtered move, conflict/reload, actual Home order and guest/logout boundary PASS')
 }
}finally{if(browser)await browser.close();server.kill('SIGTERM')}
