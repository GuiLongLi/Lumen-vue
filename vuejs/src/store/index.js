import Vue from 'vue'
import Vuex from 'vuex'
import user from './modules/user'
import tag from './modules/tag'
import post from './modules/post'
import comment from './modules/comment'

Vue.use(Vuex)

const store = new Vuex.Store({
  modules: {
    user,
    post,
    comment,
    tag
  }
})

export default store
