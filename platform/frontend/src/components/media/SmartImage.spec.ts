import { expect, it } from 'vitest'
import { createSSRApp, h } from 'vue'
import { renderToString } from 'vue/server-renderer'
import SmartImage from './SmartImage.vue'

it('shows the selected no-photo image while the real image loads, without the SVG or eager blur',async()=>{
  const html=await renderToString(createSSRApp({render:()=>h(SmartImage,{src:'/photo.webp',fallbackSrc:'/default.webp',alt:'Product',eager:true,fit:'contain-blur'})}))
  expect(html).toContain('src="/default.webp"')
  expect(html).toContain('src="/photo.webp"')
  expect(html).not.toContain('smart-placeholder-svg')
  expect(html).not.toContain('background-image')
  expect(html).toContain('opacity:0')
})

it('creates no offscreen image requests before controlled preload activates',async()=>{
  const html=await renderToString(createSSRApp({render:()=>h(SmartImage,{src:'/photo.webp',fallbackSrc:'/default.webp',alt:'Product',preloadNear:true})}))
  expect(html).not.toContain('src="')
})

it('shows a spinner only for an actual active photo request',async()=>{
 const render=(src:string,preloadNear=false)=>renderToString(createSSRApp({render:()=>h(SmartImage,{src,fallbackSrc:'/default.webp',alt:'Product',interactiveLoading:true,preloadNear})}))
 expect(await render('/photo.webp')).toContain('photo-loading-ring')
 expect(await render('/default.webp')).not.toContain('photo-loading-ring')
 expect(await render('/photo.webp',true)).not.toContain('photo-loading-ring')
})
