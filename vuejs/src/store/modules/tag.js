import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import api from '../../api/index.js'

Vue.use(Vuex)

const tag = {
  state: {
  },
  mutations: {
  },
  actions: {
    // 列表
    tagList ({commit}, params) {
      return new Promise(function (resolve, reject) {
        // respond是响应的返回值
        api.get('tag', params).then(respond => {
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
    tagCreate ({dispatch}, params) {
      return new Promise(function (resolve, reject) {
        // 有id存在的话, 编辑操作
        if (params.id) {
          dispatch('tagUpdate', params).then(msg => {
            resolve(msg)
          }).catch((e) => {
            // 抛出错误
            reject(new Error('未知错误203'))
          })
        } else {
          // 新增操作
          // respond是响应的返回值
          axios.defaults.headers.common['Authorization'] = window.localStorage.token
          api.post('tag', params).then(respond => {
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
            reject(new Error('未知错误204'))
          })
        }
      })
    },
    // 打开编辑
    tagDetail ({commit}, params) {
      return new Promise(function (resolve, reject) {
        // respond是响应的返回值
        api.get('tag', params).then(respond => {
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
          reject(new Error('未知错误205'))
        })
      })
    },
    // 编辑
    tagUpdate ({commit}, params) {
      return new Promise(function (resolve, reject) {
        // respond是响应的返回值
        axios.defaults.headers.common['Authorization'] = window.localStorage.token
        api.put('tag', params).then(respond => {
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
          reject(new Error('未知错误206'))
        })
      })
    },
    // 删除
    tagDelete ({commit}, params) {
      return new Promise(function (resolve, reject) {
        // respond是响应的返回值
        axios.defaults.headers.common['Authorization'] = window.localStorage.token
        api.delete('tag', params).then(respond => {
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
          reject(new Error('未知错误207'))
        })
      })
    }
  }
}

export default tag
