<template>
  <div class="content-wrap content-wrap-auth" >
    <Header :activeObj="obj"></Header>
    <el-container>
      <el-main class="margin-top-15">
        <el-row :gutter="0" justify="left">
          <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="login-title">
            <el-breadcrumb separator-class="el-icon-arrow-right">
              <el-breadcrumb-item :to="{ path : '/auth/tag' }">标签管理</el-breadcrumb-item>
              <el-breadcrumb-item>编辑标签</el-breadcrumb-item>
            </el-breadcrumb>
            <router-link tag="a" to="/auth/tag" class="color-white post-button">
              <el-button type="warning" icon="el-icon-back" size="mini" >返回</el-button>
            </router-link>
          </el-col>
        </el-row>
        <Form :titleName="titleName" :formData="formData" :formloading="formloading"></Form>
      </el-main>
    </el-container>
    <Footer></Footer>
  </div>
</template>
<script>
import Header from './layout/header.vue'
import Footer from '../layout/footer.vue'
import Form from './layout/tagform.vue'
import { Message } from 'element-ui'
export default {
  components: { Header, Footer, Form },
  data () {
    return {
      formloading: true,
      titleName: '编辑',
      obj: 'activeTag',
      uname: '',
      title: '标签管理',
      formData: {
        id: 0,
        tag: '',
        title: '',
        subtitle: '',
        page_image: '',
        meta_description: '',
        reverse_direction: '0'
      }
    }
  },
  metaInfo () {
    return {
      title: this.title,
      meta: [
        { vmid: 'description', name: 'description', content: '个人博客' },
        { vmid: 'scale', name: 'viewport', content: 'width=device-width,initial-scale=1,maximum-scale=1' }
      ]
    }
  },
  methods: {
  },
  mounted () {
    // Code that will run only after the
    // entire view has been rendered
    this.$nextTick(function () {
      // 获取url传过来的 id
      if (this.$route.query.id) {
        this.formloading = true
        this.$store.dispatch('tagDetail', {id: this.$route.query.id}).then((data) => {
          this.formloading = false
          this.formData = data.data
        }).catch((e) => {
          let msg = e.toString().split(':')[1] || '未知错误206'
          Message({
            showClose: true,
            message: msg,
            type: 'error'
          })
          this.formloading = false
        })
      }
    })
  }
}
</script>
