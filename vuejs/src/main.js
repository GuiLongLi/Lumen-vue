// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import api from './api/index.js'
import utils from './utils/index.js'
import './utils/request.js'
import store from './store'
// 引入ueditor
import '../static/ue/ueditor.config.js'
import '../static/ue/ueditor.all.min.js'
import '../static/ue/lang/zh-cn/zh-cn.js'
import '../static/ue/ueditor.parse.min.js'

Vue.config.productionTip = false
// 将api方法绑定到全局
Vue.prototype.$api = api
// 将工具方法绑定到全局
Vue.prototype.$utils = utils
// 将store绑定到全局
Vue.prototype.$store = store

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  components: { App },
  template: '<App/>'
})
