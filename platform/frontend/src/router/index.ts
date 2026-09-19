import { createRouter, createWebHashHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'
import ProductsView from '@/views/ProductsView.vue'
import ProductionView from '@/views/ProductionView.vue'
import FavoritesView from '@/views/FavoritesView.vue'
import TrackingView from '@/views/TrackingView.vue'

export const router=createRouter({
  history:createWebHashHistory(),
  routes:[
    {path:'/',name:'home',component:HomeView},
    {path:'/products',name:'products',component:ProductsView},
    {path:'/production',name:'production',component:ProductionView},
    {path:'/favorites',name:'favorites',component:FavoritesView},
    {path:'/tracking',name:'tracking',component:TrackingView},
  ],
  scrollBehavior(){return{top:0}},
})
