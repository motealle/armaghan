import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import { router } from './router'
import './styles/main.css'

document.documentElement.dataset.uiTest='29'
document.documentElement.dataset.uiRevision='2026-10-05-fast-media-owner-alias-r2'

const FORCE_FRESH_UNTIL=Date.parse('2026-10-12T23:59:59+03:30')
const freshUrl=new URL(window.location.href)
if(Date.now()<FORCE_FRESH_UNTIL&&!freshUrl.searchParams.has('_armaghan_fresh')){
  freshUrl.searchParams.set('_armaghan_fresh',String(Date.now()))
  window.location.replace(freshUrl.toString())
}else{
  if(freshUrl.searchParams.has('_armaghan_fresh')){
    freshUrl.searchParams.delete('_armaghan_fresh')
    history.replaceState(history.state,'',freshUrl.pathname+(freshUrl.search?freshUrl.search:'')+freshUrl.hash)
  }
  void navigator.serviceWorker?.getRegistrations().then(registrations=>registrations.forEach(registration=>void registration.update()))
  createApp(App).use(createPinia()).use(router).mount('#app')
}
