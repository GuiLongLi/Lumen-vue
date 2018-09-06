import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import router from '../../router'
import api from '../../api/index.js'

Vue.use(Vuex)

const user = {
  state: {
    name: null,
    token: null,
    auth: false
  },
  mutations: {
    // 用户登录成功，存储token并设置header头
    logined (state, token) {
      state.auth = true
      state.token = token
      localStorage.token = token
    },
    // 用户刷新token成功，使用新的token替换本地token
    refreshToken (state, token) {
      state.token = token
      localStorage.token = token
      axios.defaults.headers.common['Authorization'] = state.token
    },
    // 登录成功后拉去用户信息，存储到本地
    profile (state, data) {
      state.name = data.name
      localStorage.name = data.name
    },
    // 用户登出，清除本地数据
    logout (state) {
      state.name = null
      state.auth = false
      state.token = null
      localStorage.removeItem('name')
      localStorage.removeItem('token')
    }
  },
  actions: {
    // 注册成功后保存用户信息  dispatch、commit是promise自带方法， params是调用register时传入的参数
    register ({dispatch, commit}, params) {
      return new Promise(function (resolve, reject) {
        // respond是响应的返回值
        api.post('auth/register', params).then(respond => {
          // 响应返回200
          if (respond.status === 200) {
            // 响应的code 是200
            if (respond.data.code === 200) {
              // 提交登录state
              commit('logined', 'Bearer ' + respond.data.access_token)
              // 设置header头
              axios.defaults.headers.common['Authorization'] = 'Bearer ' + respond.data.access_token
              // 获取个人信息
              dispatch('profile').then(() => {
                router.push({path: '/auth/post'})
                resolve()
              }).catch(() => {
                // 抛出错误
                reject(new Error('注册失败'))
              })
            } else {
              // 抛出错误
              reject(new Error(respond.data.msg))
            }
            // 返回提示
            resolve(respond.data.msg)
          }
        }).catch((e) => {
          // 抛出错误
          reject(new Error('未知错误201'))
        })
      })
    },
    // 登录成功后保存用户信息  dispatch、commit是promise自带方法， params是调用register时传入的参数
    login ({dispatch, commit}, params) {
      return new Promise(function (resolve, reject) {
        // respond是响应的返回值
        api.post('auth/login', params).then(respond => {
          // 响应返回200
          if (respond.status === 200) {
            // 响应的code 是200
            if (respond.data.code === 200) {
              // 提交登录state
              commit('logined', 'Bearer ' + respond.data.access_token)
              // 设置header头
              axios.defaults.headers.common['Authorization'] = 'Bearer ' + respond.data.access_token
              // 获取个人信息
              dispatch('profile').then(() => {
                const callback = window.location.href.split('?callback=')[1]
                if (callback) {
                  router.push({path: window.decodeURIComponent(callback)})
                } else {
                  router.push({path: '/auth/post'})
                }
                resolve()
              }).catch(() => {
                // 抛出错误
                reject(new Error('登录失败'))
              })
            } else {
              // 抛出错误
              reject(new Error(respond.data.msg))
            }
            // 返回提示
            resolve(respond.data.msg)
          }
        }).catch((e) => {
          // 抛出错误
          reject(new Error('未知错误202'))
        })
      })
    },
    // 登录后获取用户信息
    logined ({dispatch, commit}, token) {
      return new Promise(function (resolve, reject) {
        // 设置header头
        axios.defaults.headers.common['Authorization'] = token
        // 获取个人信息
        dispatch('profile').then(() => {
          resolve()
        }).catch(() => {
          // 抛出错误
          reject(new Error('登录失败'))
        })
      })
    },
    // 登录成功后使用token拉取用户信息
    profile ({commit}) {
      return new Promise(function (resolve, reject) {
        api.get('profile', {}).then(respond => {
          if (typeof (respond.status) !== 'undefined' && respond.status !== null && respond.status === 200) {
            commit('profile', respond.data.data)
            resolve()
          } else {
            reject(new Error('登录失败'))
          }
        })
      })
    },
    // 用户登出，清除本地数据并重定向至登录页面
    logout ({commit}) {
      return new Promise(function (resolve, reject) {
        commit('logout')
        api.post('auth/logout', {}).then(respond => {
          resolve()
        })
      })
    },
    // 将刷新的token 保存到本地
    refreshToken ({commit}, token) {
      return new Promise(function (resolve, reject) {
        commit('refreshToken', token)
      })
    }
  }
}

export default user
