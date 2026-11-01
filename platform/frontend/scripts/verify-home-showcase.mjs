import {chromium} from '@playwright/test'
import {spawn,execFileSync} from 'node:child_process'
import {mkdir,readFile} from 'node:fs/promises'
import assert from 'node:assert/strict'
await mkdir('../../.build/showcase-acceptance',{recursive:true})
execFileSync('ffmpeg',['-hide_banner','-loglevel','error','-y','-f','lavfi','-i','color=c=blue:s=320x180:d=2','-c:v','libx264','-pix_fmt','yuv420p','-movflags','+faststart','../../.build/showcase-acceptance/test.mp4'])
const movie=await readFile('../../.build/showcase-acceptance/test.mp4')
const server=spawn(process.execPath,['node_modules/vite/bin/vite.js','--host','127.0.0.1','--port','4196'],{stdio:'ignore'});let browser
try{
 for(let i=0;i<60;i++){try{if((await fetch('http://127.0.0.1:4196/showcase-acceptance/index.html')).ok)break}catch{}await new Promise(r=>setTimeout(r,200))}
 browser=await chromium.launch({headless:true,args:['--no-sandbox']})
 for(const width of [375,768,1280]){
  const page=await browser.newPage({viewport:{width,height:900}});page.setDefaultTimeout(15000);const errors=[];page.on('pageerror',e=>errors.push(e.message))
  let draft={styles:{},texts:{}},published={styles:{},texts:{}},revision=0,publishes=0,conflict=false,videoReads=0;const auth=[]
  const snapshot=()=>({profile:{id:1,slug:'default',name:'QA',schema:1},draft:{...structuredClone(draft),css:'',checksum:String(revision).padStart(64,'0'),updated_at:null},versions:[],publications:{}})
  await page.route('**/*',r=>r.request().url().startsWith('http://127.0.0.1:4196/')?r.continue():r.abort())
  await page.route('https://www.aparat.com/**',r=>r.fulfill({contentType:'text/html',body:'<!doctype html><title>QA Aparat provider</title>'}))
  await page.route('**/vids/*.mp4',r=>{videoReads++;const range=r.request().headers()['range']?.match(/bytes=(\d+)-(\d*)/);if(!range)return r.fulfill({contentType:'video/mp4',body:movie});const start=Number(range[1]),end=Math.min(Number(range[2]||movie.length-1),movie.length-1);return r.fulfill({status:206,headers:{'Content-Type':'video/mp4','Content-Range':`bytes ${start}-${end}/${movie.length}`,'Accept-Ranges':'bytes'},body:movie.subarray(start,end+1)})})
  await page.route('**/backend/**',async r=>{
   const req=r.request(),path=new URL(req.url()).pathname
   if(path.endsWith('/api/csrf-token'))return r.fulfill({json:{token:'synthetic-qa-token'}})
   if(path.endsWith('/api/auth/login')||path.endsWith('/api/auth/register')){auth.push({path,...req.postDataJSON()});return r.fulfill({status:path.endsWith('register')?201:200,json:{ok:true}})}
   if(path.endsWith('/api/customer/session'))return r.fulfill({json:{customer:{id:7,name:'QA',company_name:'QA',email:null,phone:'+9647701234567'}}})
   if(path.endsWith('/api/admin/style-profile/draft')){const data=req.postDataJSON();if(conflict||data.expected_checksum!==snapshot().draft.checksum)return r.fulfill({status:409,json:{message:'changed'}});draft={styles:data.styles,texts:data.texts};revision++;return r.fulfill({json:{profile:snapshot().profile,draft:snapshot().draft}})}
   if(path.endsWith('/api/admin/style-profile/publish/staging')){published=structuredClone(draft);publishes++;return r.fulfill({status:201,json:{version:{id:publishes,number:publishes},channel:'staging'}})}
   if(path.endsWith('/api/admin/style-profile'))return r.fulfill({json:snapshot()})
   if(path.endsWith('/api/style-profile/staging'))return r.fulfill({json:{...published,schema:1,channel:'staging',checksum:String(revision).padStart(64,'0')}})
   if(path.includes('/api/home-media/'))return r.fulfill({json:{images:{hero:'/images/final/categories/category-baby.webp','capability.production':'/images/final/categories/category-baby.webp','capability.export':'/images/final/categories/category-kids.webp','capability.trade':'/images/final/categories/category-baby.webp','document.official':'/images/final/categories/category-baby.webp'},srcsets:{}}})
   return r.fulfill({status:401,json:{message:'unauthenticated'}})
  })
  const base='http://127.0.0.1:4196/showcase-acceptance/index.html'
  await page.goto(base);await page.getByLabel('شماره موبایل',{exact:true}).fill('٠٧٧٠١٢٣٤٥٦٧');await page.getByLabel('رمز عبور',{exact:true}).fill('123456')
  await page.getByRole('button',{name:'نمایش رمز',exact:true}).click();assert.equal(await page.getByLabel('رمز عبور',{exact:true}).getAttribute('type'),'text')
  await page.screenshot({path:`../../.build/showcase-acceptance/login-${width}.png`,fullPage:true})
  await page.getByRole('button',{name:'ورود',exact:true}).click();await page.getByRole('dialog').waitFor({state:'hidden'});assert.equal(auth[0].identifier,'+9647701234567');assert.equal(auth[0].password,'123456')
  await page.reload();await page.getByRole('tab',{name:'ساخت حساب',exact:true}).click();await page.getByLabel('شماره موبایل',{exact:true}).fill('07701234567');await page.getByLabel('رمز عبور',{exact:true}).fill('123456');await page.getByRole('button',{name:'ایجاد حساب',exact:true}).click();await page.getByRole('dialog').waitFor({state:'hidden'});assert.equal(auth[1].username,'');assert.equal(auth[1].phone,'+9647701234567')
  await page.goto(base+'?mode=home');await page.locator('video').waitFor();assert.equal(await page.locator('video').getAttribute('preload'),'none');assert.equal(videoReads,0)
  for(const lang of ['fa','ar','en','ku']){await page.evaluate(l=>window.qa.locale(l),lang);for(const label of ['فارسی','العربية','کوردی'])assert.equal(await page.getByRole('button',{name:label,exact:true}).count(),1)}
  await page.evaluate(()=>window.qa.locale('fa'));await page.locator('video').evaluate(v=>{window.previousVideo=v;return v.play()});await page.waitForFunction(()=>document.querySelector('video')?.currentTime>0);assert.ok(videoReads>0)
  await page.getByRole('button',{name:'العربية',exact:true}).click();await page.waitForFunction(()=>window.previousVideo.paused);assert.ok((await page.locator('video').getAttribute('src')).includes('arabic.mp4'))
  await page.getByRole('button',{name:'کوردی',exact:true}).click();assert.ok((await page.locator('video').getAttribute('src')).includes('kordi.mp4'))
  await page.screenshot({path:`../../.build/showcase-acceptance/videos-${width}.png`,fullPage:true})
  await page.locator('[data-style-id="home.capability.production.body"] button').click();await page.getByRole('dialog').waitFor();assert.equal(await page.locator('[data-style-id="home.capability.production.detail.1"]').count(),0);assert.equal(await page.locator('[data-style-id="home.capability.production.detail.4"]').count(),0);assert.equal(await page.locator('[data-style-id="home.capability.production.detail.2"]').count(),1)
  await page.getByRole('button',{name:'بستن',exact:true}).first().click();await page.locator('[data-style-id="home.capability.export.body"] button').click();for(const i of [3,4,5,6])assert.equal(await page.locator(`[data-style-id="home.capability.export.detail.${i}"]`).count(),0)
  await page.getByRole('button',{name:'بستن',exact:true}).first().click();await page.locator('[data-style-id="home.capability.trade.body"] button').click();assert.equal(await page.getByRole('dialog').locator('li').count(),1);assert.equal(await page.locator('[data-style-id="home.capability.trade.official.media"] img').count(),1)
  await page.goto(base+'?mode=editor');await page.getByRole('button',{name:'ذخیره و انتشار تغییرات',exact:true}).waitFor();await page.waitForFunction(()=>!document.querySelector('fieldset')?.disabled)
  await page.locator('[data-field-key="capabilityProductionDetail2"]').fill('ظرفیت تولید: ۵۰۰۰ عدد در ماه');await page.locator('[data-showcase-toggle="home.capability.production.detail.1"]').check()
  await page.getByLabel('لینک آپارات · العربية',{exact:true}).fill('https://www.aparat.com/v/qa123');await page.locator('[data-video-provider="ar"]').check();await page.getByRole('button',{name:'ذخیره و انتشار تغییرات',exact:true}).click();await page.waitForFunction(()=>document.body.textContent.includes('تغییرات ذخیره و منتشر شد.'));assert.equal(publishes,1)
  assert.equal(published.styles['home.capability.production.detail.1'].hidden,false);assert.equal(published.texts['home.capability.production.detail.2'].fa,'ظرفیت تولید: ۵۰۰۰ عدد در ماه')
  await page.screenshot({path:`../../.build/showcase-acceptance/editor-${width}.png`,fullPage:true})
  await page.goto(base+'?mode=home');await page.getByRole('button',{name:'العربية',exact:true}).click();await page.getByRole('button',{name:'پخش ویدئو · العربية',exact:true}).click();await page.locator('iframe').waitFor();assert.ok((await page.locator('iframe').getAttribute('src')).endsWith('/videohash/qa123/vt/frame'))
  await page.getByRole('button',{name:'پخش از سایت',exact:true}).click();await page.locator('video').waitFor();await page.locator('[data-style-id="home.capability.production.body"] button').click();await page.getByText('ظرفیت تولید: ۵۰۰۰ عدد در ماه',{exact:true}).waitFor();assert.equal(await page.locator('[data-style-id="home.capability.production.detail.1"]').count(),1)
  assert.deepEqual(errors,[]);await page.close();console.log(width+'px: simple phone sign-in/signup, real MP4 decode, no preload, slide pause, all UI languages, requested flags, published text reload, real document image and safe Aparat/local fallback PASS')
 }
}finally{if(browser)await browser.close();server.kill('SIGTERM')}
