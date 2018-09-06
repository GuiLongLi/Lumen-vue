import axios from 'axios'
import { Message } from 'element-ui'
import store from '../store'

// 创建axios实例
const service = axios.create({
})

// 自定义的axios响应拦截器
service.interceptors.response.use((response) => {
  // 判断一下响应中是否有token , 如果有，就直接使用此token 替换掉本地的token,你也可以根据你的业务需要，自己编写更新token逻辑
  let token = response.headers.authorization
  if (token) {
    // 如果header中存在token ,那么触发 refreshtoken 方法,替换本地的token
    store.dispatch('refreshToken', token)
  }
  return response
}, (error) => {
  switch (error.response.status) {
    // 如果响应中的http code 为401 ,那么则此用户可能token失效了，触发logout 方法，清除本地数据，并将用户重定向到登录页面
    case 401:
      return store.dispatch('logout')
    // 如果响应中的http code 为400，那么就弹出一条错误提示给用户
    case 400:
      // message: error.response.data.error,
      return Message({
        message: '未知错误: 101',
        type: 'error'
      })
  }
  return Promise.reject(error)
})

export default service
