import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import { router } from './router'
import './styles/main.css'

document.documentElement.dataset.uiTest='29'
document.documentElement.dataset.uiRevision='2026-10-06-customer-browsing-home-batch2'

// Remove the old cache-busting URL without forcing a second page load.
const freshUrl=new URL(window.location.href)
if(freshUrl.searchParams.has('_armaghan_fresh')){
  freshUrl.searchParams.delete('_armaghan_fresh')
  history.replaceState(history.state,'',freshUrl.pathname+freshUrl.search+freshUrl.hash)
}
void navigator.serviceWorker?.getRegistrations().then(registrations=>registrations.forEach(registration=>void registration.update())).catch(()=>{})
createApp(App).use(createPinia()).use(router).mount('#app')
