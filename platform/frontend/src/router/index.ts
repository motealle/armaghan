import { createRouter, createWebHashHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'
import ProductsView from '@/views/ProductsView.vue'
import ProductionView from '@/views/ProductionView.vue'
import FavoritesView from '@/views/FavoritesView.vue'
import TrackingView from '@/views/TrackingView.vue'
import MagicLinkView from '@/views/MagicLinkView.vue'

const pagePath=window.location.pathname+window.location.search
const history=createWebHashHistory(pagePath)
// Asset <base> must not send native/new-tab RouterLinks into the numbered lane.
history.createHref=(location)=>pagePath+'#'+location

export const router=createRouter({
  history,
  routes:[
    {path:'/',name:'home',component:HomeView},
    {path:'/admin',name:'admin',component:()=>import('@/views/AdminView.vue')},
    {path:'/products',name:'products',component:ProductsView},
    {path:'/production',name:'production',component:ProductionView},
    {path:'/favorites',name:'favorites',component:FavoritesView},
    {path:'/favorites/share/:token',name:'favorite-share',component:FavoritesView},
    {path:'/tracking',name:'tracking',component:TrackingView},
    {path:'/magic/:token',name:'magic-link',component:MagicLinkView},
  ],
  scrollBehavior(){return{top:0}},
})
