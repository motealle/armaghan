import {chromium} from '@playwright/test'
import {spawn} from 'node:child_process'
import {mkdir} from 'node:fs/promises'
import assert from 'node:assert/strict'
await mkdir('../../.build/tracking-acceptance',{recursive:true})
const server=spawn(process.execPath,['node_modules/vite/bin/vite.js','--host','127.0.0.1','--port','4197'],{stdio:'pipe'})
let browser,lastPage
try{
 await new Promise((resolve,reject)=>{server.stdout.on('data',data=>{if(String(data).includes('4197'))resolve()});server.on('error',reject);server.on('exit',code=>reject(Error('Vite exited '+code)))})
 browser=await chromium.launch({headless:true,args:['--no-sandbox']})
 for(const width of [375,768,1280]){
  const page=await browser.newPage({viewport:{width,height:950}});lastPage=page
  const errors=[],writes=[],searches=[],orderQueries=[];page.on('pageerror',e=>errors.push(e.message))
  const customer={id:77,active:true,has_account:true,name:'مشتری آزمایشی',company_name:null,email:null,whatsapp:'+9647701234567',phone:'+9647701234567',tags:[],priority:0,revision:'a'.repeat(64),pinned:false}
  let order=null,previewFail=false
  await page.route('**/backend/**',async route=>{
   const req=route.request(),url=new URL(req.url()),path=url.pathname
   if(path.endsWith('/api/csrf-token'))return route.fulfill({json:{token:'synthetic-token'}})
   if(path.endsWith('/api/admin/customers')){searches.push(url.searchParams.get('search'));return route.fulfill({json:{customers:[customer],page:1,last_page:1,total:1}})}
   if((path.endsWith('/api/admin/orders')||path.endsWith('/api/customer/orders'))&&req.method()==='POST'){
    const body=req.postDataJSON();writes.push(body);order={id:10,reference:'AT-SYNTHETIC77',customer_id:body.customer_id??77,customer_name:customer.name,description:body.description,stage:'inquiry',revision:'b'.repeat(64),invoice_confirmed_at:null,deposit_confirmed_at:null,events:[],documents:[],quote:null}
    return route.fulfill({status:201,json:{order}})
   }
   if(path.endsWith('/api/admin/orders/10')&&req.method()==='PATCH'){
    const body=req.postDataJSON();writes.push(body);order={...order,stage:body.stage??order.stage,revision:'c'.repeat(64),events:[...order.events,{id:order.events.length+1,stage:body.stage??order.stage,note:body.note,visible_to_customer:body.visible_to_customer,created_at:'2026-11-01T12:00:00Z'}]}
    return route.fulfill({json:{order}})
   }
   if(path.endsWith('/api/admin/orders/10/customer-view')){if(previewFail)return route.fulfill({status:500,json:{message:'synthetic failure'}});return route.fulfill({json:{order:{...order,events:order.events.filter(e=>e.visible_to_customer)}}})}
   if(path.endsWith('/api/admin/orders')){orderQueries.push(url.searchParams.get('customer_id'));return route.fulfill({json:{orders:order?[order]:[],page:1,last_page:1}})}
   if(path.endsWith('/api/customer/orders'))return route.fulfill({json:{orders:order?[{...order,events:order.events.filter(e=>e.visible_to_customer)}]:[],page:1,last_page:1}})
   if(path.includes('/api/home-media/'))return route.fulfill({json:{images:{},srcsets:{}}})
   return route.fulfill({status:401,json:{message:'unauthenticated'}})
  })
  await page.goto('http://127.0.0.1:4197/tracking-acceptance/index.html')
  await page.locator('.admin-tabs').getByRole('button',{name:'مشتری‌ها',exact:true}).click()
  await page.getByRole('button',{name:'ثبت سفارش',exact:true}).click()
  await page.getByLabel('انتخاب مشتری',{exact:true}).waitFor()
  assert.equal(await page.getByLabel('انتخاب مشتری',{exact:true}).inputValue(),'77')
  assert.equal(writes.length,0)
  await page.getByLabel('جست‌وجوی مشتری با نام یا شماره موبایل',{exact:true}).fill('٠٧٧٠ ١٢٣-٤٥٦٧')
  await page.getByLabel('جست‌وجوی مشتری با نام یا شماره موبایل',{exact:true}).locator('..').locator('..').getByRole('button',{name:'جستجو',exact:true}).click()
  await page.waitForFunction(()=>!document.querySelector('select')?.disabled)
  assert.ok(searches.includes('7701234567'));assert.equal(await page.getByLabel('انتخاب مشتری',{exact:true}).inputValue(),'77')
  await page.getByLabel('شرح درخواست (قابل مشاهده برای مشتری)',{exact:true}).fill('سفارش آزمایشی کنترل مسیر؛ داده واقعی نیست')
  await page.getByRole('button',{name:'ثبت درخواست برای پیگیری',exact:true}).click()
  await page.getByRole('button',{name:/AT-SYNTHETIC77/}).waitFor()
  assert.equal(writes[0].customer_id,77);assert.equal(writes.length,1)
  assert.equal(await page.getByRole('button',{name:/AT-SYNTHETIC77/}).getAttribute('aria-expanded'),'true')
  const form=page.locator('article > div > form').last()
  await form.getByLabel('عملیات',{exact:true}).selectOption('stage')
  await form.getByLabel('تغییر مرحله',{exact:true}).selectOption('review')
  await form.getByLabel('توضیح مرحله',{exact:true}).fill('مشخصات سفارش در حال بررسی است')
  await form.getByLabel('نمایش این توضیح به مشتری',{exact:true}).check()
  await form.getByRole('button',{name:'ذخیره',exact:true}).click()
  await page.waitForFunction(()=>!document.querySelector('section[aria-busy="true"]'))
  assert.equal(writes[1].stage,'review');assert.equal(writes[1].visible_to_customer,true)
  assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth>innerWidth),false)
  await page.screenshot({path:`../../.build/tracking-acceptance/admin-${width}.png`,fullPage:true})
  order.events.push({id:90,stage:'review',note:'PRIVATE STAFF ONLY',visible_to_customer:false,created_at:'2026-11-01T12:00:00Z'})
  const before=writes.length
  await page.getByRole('button',{name:'نمایش از دید مشتری',exact:true}).click()
  const preview=page.locator('[data-customer-order-preview]')
  await preview.getByText('مشخصات سفارش در حال بررسی است',{exact:true}).waitFor()
  assert.equal(await preview.getByText('PRIVATE STAFF ONLY',{exact:true}).count(),0)
  assert.equal(await preview.locator('form,textarea,input,select').count(),0);assert.equal(writes.length,before)
  await page.screenshot({path:`../../.build/tracking-acceptance/preview-${width}.png`,fullPage:true})
  await preview.getByRole('button',{name:'بستن',exact:true}).click()
  previewFail=true;await page.getByRole('button',{name:'نمایش از دید مشتری',exact:true}).click();await preview.getByRole('alert').waitFor();assert.equal(await preview.getByText('مشخصات سفارش در حال بررسی است',{exact:true}).count(),0);await preview.getByRole('button',{name:'بستن',exact:true}).click();previewFail=false
  await page.locator('.admin-tabs').getByRole('button',{name:'مشتری‌ها',exact:true}).click()
  await page.getByRole('button',{name:'سفارش‌های مشتری',exact:true}).click()
  await page.getByRole('button',{name:/AT-SYNTHETIC77/}).waitFor();assert.equal(await page.getByLabel('شرح درخواست (قابل مشاهده برای مشتری)',{exact:true}).count(),0);assert.ok(orderQueries.includes('77'));assert.equal(writes.length,before)
  await page.evaluate(()=>window.qa.mode('customer'))
  await page.getByRole('button',{name:/AT-SYNTHETIC77/}).click()
  await page.getByText('مشخصات سفارش در حال بررسی است',{exact:true}).waitFor()
  assert.equal(await page.getByLabel('عملیات',{exact:true}).count(),0)
  await page.screenshot({path:`../../.build/tracking-acceptance/customer-${width}.png`,fullPage:true})
  await page.evaluate(()=>window.qa.wizard())
  await page.getByRole('button',{name:'ثبت درخواست برای پیگیری',exact:true}).click()
  const progressLink=page.getByRole('link',{name:'مشاهده مراحل سفارش من',exact:true})
  await progressLink.waitFor();await page.screenshot({path:`../../.build/tracking-acceptance/wizard-${width}.png`,fullPage:true});assert.ok((await progressLink.getAttribute('href')).includes('order=AT-SYNTHETIC77'));const count=writes.length
  await page.evaluate(()=>window.qa.locale('ar'));await page.getByRole('link',{name:'عرض مراحل طلبي',exact:true}).waitFor();assert.equal(await page.getByRole('button',{name:'تسجيل الطلب للمتابعة',exact:true}).count(),0)
  await page.evaluate(()=>window.qa.locale('fa'));await progressLink.click();await page.evaluate(()=>window.qa.mode('customer'))
  await page.getByRole('button',{name:/AT-SYNTHETIC77/}).waitFor();assert.equal(await page.getByRole('button',{name:/AT-SYNTHETIC77/}).getAttribute('aria-expanded'),'true');assert.equal(writes.length,count)
  await page.evaluate(()=>window.qa.wizard());await page.getByRole('link',{name:'مشاهده مراحل سفارش من',exact:true}).waitFor();assert.equal(writes.length,count)
  await page.evaluate(()=>window.qa.mode('home'))
  await page.locator('[data-style-id="home.why"]').waitFor()
  for(const lang of ['fa','ar','ku','en']){
   await page.evaluate(value=>window.qa.locale(value),lang)
   assert.equal(await page.evaluate(()=>{const root=document.querySelector('.home-page'),sections=[...root.children].filter(e=>e.tagName==='SECTION');return sections.at(-1)?.getAttribute('data-style-id')}),'home.why')
   assert.equal(await page.locator('.test26-product-banner').count(),3)
   assert.ok(await page.evaluate(()=>!!(document.querySelector('[data-style-id="home.product-banners"]').compareDocumentPosition(document.querySelector('[data-style-id="home.why"]'))&Node.DOCUMENT_POSITION_FOLLOWING)))
  }
  assert.deepEqual(errors,[]);await page.close();console.log(width+'px: customer shortcut, selected phone account, localized search, create/expand, shared stage, customer read-only view and final Home section in four languages PASS')
 }
}catch(e){if(lastPage&&!lastPage.isClosed())await lastPage.screenshot({path:'../../.build/tracking-acceptance/failure.png',fullPage:true});throw e}finally{if(browser)await browser.close();server.kill('SIGTERM')}
