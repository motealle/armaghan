import { chromium } from '@playwright/test'
import { spawn } from 'node:child_process'
import { mkdir } from 'node:fs/promises'
import assert from 'node:assert/strict'
const server=spawn(process.execPath,['node_modules/vite/bin/vite.js','--host','127.0.0.1','--port','4198'],{stdio:'ignore'})
let browser
try{
 for(let i=0;i<60;i++){try{if((await fetch('http://127.0.0.1:4198/spec-acceptance/index.html')).ok)break}catch{}await new Promise(r=>setTimeout(r,200))}
 browser=await chromium.launch({headless:true,args:['--no-sandbox']})
 await mkdir('../../.build/spec-acceptance',{recursive:true})
 for(const width of [375,768,1280]){
  const page=await browser.newPage({viewport:{width,height:900}})
  page.setDefaultTimeout(15000);page.setDefaultNavigationTimeout(20000)
  page.on('pageerror',error=>console.log('BROWSER ERROR:',error.message))
  let row={id:77,subcategory_id:1,revision:'rev1',code:'12011',specifications:['ابعاد','طرح ظاهری','فرم محصول','مشخصات مدل','ترکیب رنگ','جنس','تعداد در پک'].map((label,i)=>({id:i+1,key:'spec'+i,labels:{fa:label,en:'Specification '+i,ar:null,ku:null},locked:i===0,value_text:i===5?'پنبه':null,values:{fa:i===5?'پنبه':null,en:null,ar:null,ku:null}}))}
  const writes=[];let fail=false;let initialized=width!==375;let schemaWrites=0
  await page.route('**/*',route=>route.request().url().startsWith('http://127.0.0.1:4198/')?route.continue():route.abort())
  await page.route('**/backend/**',async route=>{
   const request=route.request(),url=request.url()
   if(url.endsWith('/api/csrf-token'))return route.fulfill({json:{token:'synthetic-qa-token'}})
   if(request.method()==='GET'&&url.endsWith('/products/77'))return route.fulfill({json:{product:initialized?row:{...row,specifications:[]}}})
   if(request.method()==='GET'&&url.endsWith('/product-taxonomy'))return route.fulfill({json:{subcategories:[{id:1,schema_revision:'empty',specifications:[]}]}})
   if(request.method()==='PUT'&&url.endsWith('/product-taxonomy/1/specifications')){
    const body=request.postDataJSON();assert.equal(body.acknowledged,true);assert.equal(body.definitions.length,7);assert.ok(body.definitions.every(d=>d.id===null));assert.equal(body.definitions[0].labels.fa,'ابعاد')
    row.specifications=body.definitions.map((d,i)=>({...d,id:i+1,value_text:null,values:{fa:null,ar:null,en:null,ku:null}}));row.revision+='-schema';initialized=true;schemaWrites++
    return route.fulfill({json:{subcategories:[]}})
   }
   if(request.method()==='PATCH'&&url.endsWith('/products/77/specifications')){
    const body=request.postDataJSON();if(fail||body.revision!==row.revision)return route.fulfill({status:409,json:{message:'conflict'}})
    assert.ok(['fa','ar','en','ku'].includes(body.locale));assert.deepEqual(Object.keys(body).sort(),['locale','revision','specifications'])
    row=structuredClone(row);for(const change of body.specifications){const spec=row.specifications.find(s=>s.id===change.definition_id);spec.values[body.locale]=change.value_text;if(body.locale==='fa')spec.value_text=change.value_text}
    row.revision+='-next';writes.push(body);return route.fulfill({json:{product:row}})
   }
   return route.abort()
  })
  await page.goto('http://127.0.0.1:4198/spec-acceptance/index.html')
  await page.waitForFunction(()=>Boolean(window.qa))
  console.log(width+'px: component mounted')
  const edit=page.getByRole('button',{name:'ویرایش مشخصات',exact:true});await edit.click()
  await page.getByRole('textbox').first().fill('۴۰ × ۶۰')
  await page.getByLabel('جنس · فارسی',{exact:true}).fill('کتان')
  const language=page.getByRole('combobox',{name:'زبان متن',exact:true});await language.selectOption('en')
  await page.getByLabel('جنس · English',{exact:true}).fill('Linen')
  await language.selectOption('fa');assert.equal(await page.getByLabel('جنس · فارسی',{exact:true}).inputValue(),'کتان')
  await page.screenshot({path:'../../.build/spec-acceptance/edit-'+width+'.png',fullPage:true})
  assert.ok(await page.getByRole('textbox').evaluateAll(inputs=>inputs.every(input=>input.getBoundingClientRect().width>50)))
  await page.getByRole('button',{name:'ذخیره',exact:true}).click()
  await page.getByRole('status').filter({hasText:'تغییرات در سایت ذخیره شد.'}).waitFor()
  assert.equal(writes.length,2);assert.equal(writes[1].revision,writes[0].revision+'-next');assert.equal(schemaWrites,width===375?1:0);assert.equal(row.specifications[5].values.fa,'کتان');assert.equal(row.specifications[5].values.en,'Linen')
  await page.evaluate(()=>window.qa.setLocale('en'));await page.getByText('Linen',{exact:true}).waitFor()
  await page.screenshot({path:'../../.build/spec-acceptance/read-en-'+width+'.png',fullPage:true})
  await page.getByRole('button',{name:'Edit specifications',exact:true}).click();await page.getByRole('textbox').first().fill('UNSAVED')
  fail=true;await page.getByRole('button',{name:'Save',exact:true}).click();await page.getByRole('alert').waitFor()
  assert.equal(await page.getByRole('textbox').first().inputValue(),'UNSAVED');assert.ok(await page.getByRole('button',{name:'Save',exact:true}).isDisabled())
  await page.evaluate(()=>window.qa.logout());assert.equal(await page.getByRole('button',{name:'Edit specifications',exact:true}).count(),0)
  await page.close();console.log(width+'px: real Vue edit, empty-category initialization, locked value, language drafts, sequential save, public display, conflict retention and logout PASS')
 }
}finally{if(browser)await browser.close();server.kill('SIGTERM')}
