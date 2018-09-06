import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import api from '../../api/index.js'

Vue.use(Vuex)

const post = {
  state: {
  },
  mutations: {
  },
  actions: {
    // 列表
    commentList ({commit}, params) {
      return new Promise(function (resolve, reject) {
        // respond是响应的返回值
        api.get('comment', params).then(respond => {
          // 响应返回200
          if (respond.status === 200) {
            // 响应的code 是200
            if (respond.data.code === 200) {
              resolve(respond.data)
            } else {
              // 抛出错误
              reject(new Error(respond.data.msg))
            }
          }
        }).catch((e) => {
          // 抛出错误
          reject(new Error('未知错误202'))
        })
      })
    },
    // 创建
    articleComment ({dispatch}, params) {
      return new Promise(function (resolve, reject) {
        // 新增操作
        // respond是响应的返回值
        axios.defaults.headers.common['Authorization'] = window.localStorage.token
        api.post('comment', params).then(respond => {
          // 响应返回200
          if (respond.status === 200) {
            // 响应的code 是200
            if (respond.data.code === 200) {
              resolve(respond.data.msg)
            } else {
              // 抛出错误
              reject(new Error(respond.data.msg))
            }
          }
        }).catch((e) => {
          // 抛出错误
          reject(new Error('未知错误201'))
        })
      })
    }
  }
}

export default post
