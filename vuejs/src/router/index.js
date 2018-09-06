import Vue from 'vue'
import Router from 'vue-router'
import Meta from 'vue-meta'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import 'element-ui/lib/theme-chalk/display.css'
// 即先加载promise , 在加载 vuex
import 'es6-promise'
import Vuex from 'vuex'
import AuthFrame from '@/frame/auths'
import auths from './auths.js'
import TagFrame from '@/frame/tag'
import tag from './tag.js'
import PostFrame from '@/frame/post'
import post from './post.js'
import Uesimpleupload from '@/components/layout/uesimpleupload'
import Index from '@/components/front/index'
import Article from '@/components/front/article'

Vue.use(Router)
Vue.use(Meta)
Vue.use(ElementUI)
Vue.use(Vuex)

const route = new Router({
  routes: [
    {
      path: '/auth',
      component: AuthFrame,
      children: auths,
      beforeEnter: (to, from, next) => {
        const tokens = window.localStorage.token
        if (tokens) next({path: '/auth/post'})
        else next()
      }
    },
    {
      path: '/auth',
      component: TagFrame,
      children: tag,
      beforeEnter: (to, from, next) => {
        const tokens = window.localStorage.token
        if (!tokens) next({path: '/auth/login'})
        else next()
      }
    },
    {
      path: '/auth',
      component: PostFrame,
      children: post,
      beforeEnter: (to, from, next) => {
        const tokens = window.localStorage.token
        if (!tokens) next({path: '/auth/login'})
        else next()
      }
    },
    {
      path: '/ueditor/simpleupload',
      component: Uesimpleupload
    },
    {
      path: '/',
      component: Index
    },
    {
      path: '/article',
      component: Article,
      beforeEnter: (to, from, next) => {
        const slug = to.query.slug
        const totag = to.query.tag
        const fromtag = from.query.tag
        if (!slug) next('/')
        if (fromtag && !totag) next({path: '/article', query: {slug: slug, tag: fromtag}})
        next()
      }
    }
  ]
})

route.beforeEach((to, from, next) => {
  const tokens = window.localStorage.token
  const preg = /auth.*/ig
  if (!tokens && preg.test(to.path) && to.path !== '/auth/login' && to.path !== '/auth/register') next({path: '/auth/login'})
  else next()
})

export default route
