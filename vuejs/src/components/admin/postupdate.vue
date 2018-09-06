<template>
  <div class="content-wrap content-wrap-auth" >
    <Header :activeObj="obj"></Header>
    <el-container>
      <el-main class="margin-top-15">
        <el-row :gutter="0" justify="left">
          <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="login-title">
            <el-breadcrumb separator-class="el-icon-arrow-right">
              <el-breadcrumb-item :to="{ path : '/auth/post' }">文章管理</el-breadcrumb-item>
              <el-breadcrumb-item>编辑文章</el-breadcrumb-item>
            </el-breadcrumb>
            <router-link tag="a" to="/auth/post" class="color-white post-button">
              <el-button type="warning" icon="el-icon-back" size="mini" >返回</el-button>
            </router-link>
          </el-col>
        </el-row>
        <Form :titleName="titleName" :formData="formData" :formloading="formloading" :tagoptions="tagoptions" :ueditor="ueditor"></Form>
      </el-main>
    </el-container>
    <Footer></Footer>
  </div>
</template>
<script>
import Header from './layout/header.vue'
import Footer from '../layout/footer.vue'
import Form from './layout/postform.vue'
import { Message } from 'element-ui'
export default {
  components: { Header, Footer, Form },
  data () {
    return {
      formloading: false,
      titleName: '编辑',
      obj: 'activePost',
      uname: '',
      title: '文章管理',
      tagoptions: '',
      ueditor: {
        value: '',
        config: {
          // 这里可以选择自己需要的工具按钮名称,此处仅选择如下五个
          toolbars: [
            ['fullscreen', 'source', 'undo', 'redo',
              'print', // 打印
              'preview', // 预览
              'insertimage' // 多图上传
            ],
            ['fontfamily', // 字体
              'fontsize', // 字号
              'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'removeformat', 'formatmatch'
            ],
            ['forecolor', // 字体颜色
              'backcolor', // 背景色
              'justifyleft', // 居左对齐
              'justifyright', // 居右对齐
              'justifycenter', // 居中对齐
              'justifyjustify', // 两端对齐
              'insertorderedlist', 'insertunorderedlist'],
            ['paragraph', // 段落和标题
              'lineheight', // 行间距
              'simpleupload', // 单图上传
              'selectall', 'cleardoc']
          ],
          // focus时自动清空初始化时的内容
          autoClearinitialContent: true,
          // 关闭elementPath
          elementPathEnabled: false,
          // 默认的编辑区域高度
          initialFrameHeight: 300,
          UEDITOR_HOME_URL: '/static/ue/'
        }
      },
      formData: {
        id: 0,
        title: '',
        subtitle: '',
        content_html: '',
        page_image: '',
        published_at: new Date(),
        meta_description: '',
        old_tags: ''
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
  mounted: function () {
    // Code that will run only after the
    // entire view has been rendered
    this.$nextTick(function () {
      // 获取url传过来的 id
      if (this.$route.query.id) {
        this.$store.dispatch('postDetail', {id: this.$route.query.id}).then((data) => {
          this.formData = data.data
        }).catch((e) => {
          let msg = e.toString().split(':')[1] || '未知错误206'
          Message({
            showClose: true,
            message: msg,
            type: 'error'
          })
        })
      }
      this.$store.dispatch('tagList', {page: 1, limit: 99999999}).then((data) => {
        this.formloading = false
        let dat = []
        let tmp = ''
        for (let i in data.data) {
          tmp = {
            value: data.data[i].id,
            label: data.data[i].tag
          }
          dat.push(tmp)
        }
        this.tagoptions = dat
      }).catch((e) => {
        this.formloading = false
      })
    })
  }
}
</script>
