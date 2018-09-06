<template>
  <div class="content-wrap content-wrap-login">
    <Header></Header>
    <el-container>
      <el-main class="index-main">
        <el-row type="flex" justify="center" class="index-main-titlewrapper">
          <el-col :xs="11" :sm="10" :md="12" :lg="10" :xl="10" class="bg-info text-center index-main-title">
            <h1>{{ title }}</h1>
            <hr>
            <h2>{{ subtitle }}</h2>
          </el-col>
        </el-row>
        <el-row type="flex" justify="center" class="index-main-top">
          <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="text-center">
            <img src="../../../static/image/home-bg.jpg" alt="" class="index-main-top-img">
          </el-col>
        </el-row>
        <Flow></Flow>
      </el-main>
    </el-container>
    <Footer></Footer>
  </div>
</template>
<script>
import Header from '@/components/layout/frontheader'
import Footer from '@/components/layout/footer'
import Flow from '@/components/mintui/articlelist'
export default {
  components: {Header, Footer, Flow},
  data () {
    return {
      defaults: {
        title: '博客园',
        subtitle: 'http://www.lv.net'
      },
      title: '',
      subtitle: ''
    }
  },
  // 监听,当路由发生变化的时候执行
  watch: {
    $route: {
      handler: function (val, oldVal) {
        if (val && val.query.title && val.query.subtitle && val.query.tag) {
          window.localStorage.articleTitle = val.query.title
          window.localStorage.articleSubtitle = val.query.subtitle
        } else {
          window.localStorage.articleTitle = this.defaults.title
          window.localStorage.articleSubtitle = this.defaults.subtitle
        }
        this.title = window.localStorage.articleTitle || this.defaults.title
        this.subtitle = window.localStorage.articleSubtitle || this.defaults.subtitle
      }
    }
  },
  mounted: function () {
    // Code that will run only after the
    // entire view has been rendered
    this.$nextTick(function () {
      if (this.$route.query.title && this.$route.query.subtitle && this.$route.query.tag) {
        window.localStorage.articleTitle = this.$route.query.title
        window.localStorage.articleSubtitle = this.$route.query.subtitle
      } else {
        window.localStorage.articleTitle = this.defaults.title
        window.localStorage.articleSubtitle = this.defaults.subtitle
      }
      this.title = window.localStorage.articleTitle || this.defaults.title
      this.subtitle = window.localStorage.articleSubtitle || this.defaults.subtitle
    })
  }
}
</script>
