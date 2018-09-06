<template>
  <div class="content-wrap content-wrap-auth" ref="contentWrap">
    <Header></Header>
    <el-container>
      <el-main class="index-main" v-loading="loading">
        <el-row type="flex" justify="center" class="index-main-titlewrapper">
          <el-col :xs="11" :sm="10" :md="12" :lg="10" :xl="10" class="bg-info text-center article-main-title">
              <h1>{{ author }}</h1>
          </el-col>
        </el-row>
        <el-row type="flex" justify="center" class="index-main-top">
          <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="text-center">
            <img src="../../../static/image/about-bg.jpg" alt="" class="index-main-top-img">
          </el-col>
        </el-row>
        <el-row type="flex" justify="center">
          <el-col :xs="22" :sm="22" :md="22" :lg="21" :xl="20" class="login-title">
            <div class="post-preview">
              <h2 class="post-title text-center">{{ detail.title }}</h2>
              <h3 class="post-subtitle text-center">{{ detail.subtitle }}</h3>
              <p class="post-meta">
                {{ detail.published_at }}
                in
                <span v-for="(val, key) in detail.tags" :key="key">
                  <a :href = "['#/?tag='+ val + '&title=' + val + '&subtitle=' + detail.tags_title[key]]" class="tags">{{ val }}</a>
                </span>
              </p>
              <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="article-main-content" v-html="detail.content_html">
              </el-col>
              <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                <router-link v-if="detail.prev" tag="a" :to="{path: '/article', query: listQuery.tag ? {slug: detail.prev.slug, tag: listQuery.tag} : {slug: detail.prev.slug}}" class="article-main-prev"><i class="el-icon-d-arrow-left"></i>{{ detail.prev.title }}</router-link>

                <router-link v-if="detail.next" tag="a" :to="{path: '/article', query: listQuery.tag ? {slug: detail.next.slug, tag: listQuery.tag} : {slug: detail.next.slug}}" class="article-main-next">{{ detail.next.title }}<i class="el-icon-d-arrow-right"></i></router-link>

              </el-col>
              <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                <h3>留下您的评论</h3>
                <Comment :aid="detail.id" @commentList="commentList"></Comment>
              </el-col>
              <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                <h3>| 最新评论</h3>
                <Flow :aid="detail.id" ref="flow"></Flow>
              </el-col>
            </div>
          </el-col>
        </el-row>
      </el-main>
    </el-container>
    <Footer></Footer>
  </div>
</template>
<script>
import Header from '@/components/layout/frontheader'
import Footer from '@/components/layout/footer'
import Comment from '@/components/layout/comment'
import Flow from '@/components/layui/flowcomment'
import { Message } from 'element-ui'
export default {
  components: {Header, Footer, Comment, Flow},
  data () {
    return {
      contentWrapHeight: '',
      loading: false,
      author: '',
      listQuery: {
        sid: 1,
        page: 1,
        limit: 10,
        slug: '',
        tag: ''
      },
      detail: {
        'id': '',
        'title': '',
        'subtitle': '',
        'content_html': '',
        'page_image': '',
        'meta_description': '',
        'tags': '',
        'tags_title': '',
        'author': '',
        'published_at': ''
      }
    }
  },
  // 监听,当路由发生变化的时候执行
  watch: {
    $route: {
      handler: function (val, oldVal) {
        this.getDetail()
      }
    }
  },
  created () {
    this.getDetail()
  },
  methods: {
    getDetail () {
      const _this = this
      _this.loading = true
      _this.listQuery.slug = _this.$route.query.slug || ''
      _this.listQuery.tag = _this.$route.query.tag || ''
      _this.$store.dispatch('postList', _this.listQuery).then((data) => {
        _this.loading = false
        _this.detail = data.data
        _this.author = _this.detail.author
        _this.$refs['flow'].flowlist()
      }).catch((e) => {
        let msg = e.toString().split(':')[1] || '未知错误206'
        Message({
          showClose: true,
          message: msg,
          type: 'error'
        })
        _this.loading = false
      })
    },
    commentList () {
      this.$refs['flow'].newFlow()
    }
  },
  mounted () {
    this.$nextTick(function () {

    })
  }
}
</script>
