<template>
  <div>
    <el-row type="flex" justify="center">
      <el-col :xs="22" :sm="22" :md="22" :lg="21" :xl="20" class="login-title" ref="articlelist">
        <loadmore
          :bottom-method="loadBottom"
          :top-method="loadTop"
          :bottom-all-loaded="allLoaded"
          :bottomPullText='bottomPullTextVal'
          :bottomDropText='bottomDropTextVal'
          :bottomLoadingText='bottomLoadingTextVal'
          :topPullText='topPullTextVal'
          :topDropText='topDropTextVal'
          :topLoadingText='topLoadingTextVal'
          :autoFill="false"
          @bottom-status-change="handleBottomChange"
          @top-status-change="handleTopChange"
          ref="loadmore">
          <div class="post-preview" v-for="(item, key) in list" :key="key">
            <router-link tag="a" :to="{path: '/article', query: {slug: item.slug}}">
              <h2 class="post-title">{{ item.title }}</h2>
              <h3 class="post-subtitle">{{ item.subtitle }}</h3>
            </router-link>
            <p class="post-meta">
              <router-link v-for="(ti, tk) in item.tags" :key="tk" tag="a" :to="{path: '/', query: {tag: ti, title: item.title, subtitle: item.subtitle}}" class="tags">{{ ti }}</router-link>
            </p>
          </div>
        </loadmore>
      </el-col>
    </el-row>
  </div>
</template>
<script>
import { Loadmore, Toast } from 'mint-ui'
export default {
  components: { Loadmore },
  data () {
    return {
      list: [],
      allLoaded: false, // 是否可以上拉属性，false可以上拉，true为禁止上拉
      status: '',
      bottomPullTextVal: '上划加载更多数据',
      bottomDropTextVal: '释放更新',
      bottomLoadingTextVal: '加载中...',
      topPullTextVal: '下拉加载更多数据',
      topDropTextVal: '释放更新',
      topLoadingTextVal: '加载中...',
      topStatus: '',
      bottomStatus: '',
      listQuery: {
        page: 1,
        limit: 5
      }
    }
  },
  methods: {
    getlist () {
      // 这里是ajax请求回来的数据
      const _this = this
      _this.listQuery.tag = _this.$route.query.tag || ''
      this.$store.dispatch('postList', _this.listQuery).then((data) => {
        let dat = data.data || {}
        for (let i in dat) {
          this.list.push(dat[i])
        }
        this.allLoaded = true
      }).catch((e) => {
        let msg = e.toString().split(':')[1] || '未知错误206'
        Toast({
          message: msg,
          iconClass: 'icon icon-failure'
        })
        this.allLoaded = true
      })
    },
    // 上拉加载
    loadBottom () {
      this.listQuery.page++
      this.$refs.loadmore.onBottomLoaded()
      this.getlist()
    },
    // 下拉刷新
    loadTop () {
      this.allLoaded = false
      this.listQuery.page = 1
      this.getlist()
    },
    handleTopChange (status) {
      this.topStatus = status
    },
    handleBottomChange (status) {
      this.bottomStatus = status
    }
  },
  mounted () {
    this.$nextTick(() => {
      this.getlist()
    })
  }
}
</script>
