import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import { router } from './router'
import './styles/main.css'

document.documentElement.dataset.uiTest='28'

createApp(App).use(createPinia()).use(router).mount('#app')
