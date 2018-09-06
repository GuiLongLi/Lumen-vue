<template>
  <el-container>
    <el-header class="auth-header">
      <el-row :gutter="10">
        <el-col :xs="2" :sm="3" :md="3" :lg="3" :xl="6"><div class="grid-content"></div></el-col>
        <el-col :xs="10" :sm="9" :md="9" :lg="9" :xl="6" class="text-left">
          <router-link tag="a" to="/" class="auth-link">博客</router-link>
          <router-link tag="a" to="/auth/post" class="auth-link hidden-xs-only" :class="{ 'router-link-exact-active' : activePost}">文章</router-link>
          <router-link tag="a" to="/auth/tag" class="auth-link hidden-xs-only" :class="{ 'router-link-exact-active' : activeTag}">标签</router-link>
          <router-link tag="a" to="/auth/upload" class="auth-link hidden-xs-only" :class="{ 'router-link-exact-active' : activeUpload}">上传</router-link>
        </el-col>
        <el-col :xs="10" :sm="9" :md="9" :lg="9" :xl="6" class="text-right">
          <el-dropdown trigger="click" class="hidden-sm-and-up">
            <span class="el-dropdown-link">
              <i class="el-icon-menu hidden-sm-and-up size-15"></i>
            </span>
            <el-dropdown-menu slot="dropdown" class="hidden-sm-and-up">
              <el-dropdown-item class="clearfix">
                <router-link tag="a" to="/auth/post" class="color-gray padding-5-8">文章</router-link>
              </el-dropdown-item>
              <el-dropdown-item class="clearfix">
                <router-link tag="a" to="/auth/tag" class="color-gray padding-5-8">标签</router-link>
              </el-dropdown-item>
              <el-dropdown-item class="clearfix">
                <router-link tag="a" to="/auth/upload" class="color-gray padding-5-8">上传</router-link>
              </el-dropdown-item>
              <el-menu
                default-active="2"
                class="el-menu-vertical-logout"
                background-color="#ccc"
                text-color="#fff"
                active-text-color="#ffd04b">
                <el-submenu index="1">
                  <template slot="title">
                    <span v-text="$utils.str_substr(author, 3)"></span>
                  </template>
                  <el-menu-item index="1-1" @click="logout">退出</el-menu-item>
                </el-submenu>
              </el-menu>
            </el-dropdown-menu>
          </el-dropdown>
          <div class="hidden-xs-only">
            <el-dropdown trigger="click">
            <span class="el-dropdown-link">
              {{ $utils.str_substr(author, 3) }}
              <i class="el-icon-arrow-down"></i>
            </span>
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item class="clearfix">
                  <a href="javascript:;" @click="logout">退出</a>
                </el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
          </div>
        </el-col>
        <el-col :xs="2" :sm="3" :md="3" :lg="3" :xl="6"><div class="grid-content"></div></el-col>
      </el-row>
    </el-header>
  </el-container>
</template>
<script>
import { Message } from 'element-ui'
export default {
  props: ['activeObj'],
  data () {
    return {
      activePost: false,
      activeTag: false,
      activeUpload: false,
      activeRef: this.activeObj
    }
  },
  methods: {
    logout () {
      this.loading = true
      this.$store.dispatch('logout').then(() => {
        this.loading = false
        this.$router.push({path: '/auth/login'})
      }).catch((e) => {
        let msg = e.toString().split(':')[1] || '未知错误205'
        Message({
          showClose: true,
          message: msg,
          type: 'error'
        })
        this.loading = false
      })
    }
  },
  mounted: function () {
    // Code that will run only after the
    // entire view has been rendered
    this.$nextTick(function () {
      this[this.activeRef] = true
    })
  },
  computed: {
    author () {
      return window.localStorage.name
    }
  }
}
</script>
