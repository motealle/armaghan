import { createRouter, createWebHashHistory } from 'vue-router'
import { installPositionPersistence, scrollForNavigation } from '@/services/browsingPosition'
const HomeView=()=>import('@/views/HomeView.vue')
const ProductsView=()=>import('@/views/ProductsView.vue')
const ProductionView=()=>import('@/views/ProductionView.vue')
const FavoritesView=()=>import('@/views/FavoritesView.vue')
const TrackingView=()=>import('@/views/TrackingView.vue')
const MagicLinkView=()=>import('@/views/MagicLinkView.vue')

// Normalize historical entry timestamps before hash history captures its base.
const entryUrl=new URL(window.location.href)
if(entryUrl.searchParams.has('_armaghan_fresh')){
  entryUrl.searchParams.delete('_armaghan_fresh')
  window.history.replaceState(window.history.state,'',entryUrl.pathname+entryUrl.search+entryUrl.hash)
}
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
    {path:'/s/:token',name:'favorite-share-short',component:FavoritesView},
    {path:'/favorites/share/:token',name:'favorite-share',component:FavoritesView},
    {path:'/tracking',name:'tracking',component:TrackingView},
    {path:'/magic/:token',name:'magic-link',component:MagicLinkView},
  ],
  scrollBehavior(to,from,saved){return scrollForNavigation(to,!from.matched.length,saved)},
})

installPositionPersistence(router)
