<template>
  <div>
    <el-row type="flex" justify="center">
      <el-col :xs="22" :sm="22" :md="22" :lg="21" :xl="20" class="login-title" id="LAY_flow" ref="LAY_flow">
      </el-col>
    </el-row>
  </div>
</template>
<script>
import { Message } from 'element-ui'
export default {
  data () {
    return {
      list: null,
      total: null,
      listQuery: {
        page: 1,
        limit: 1
      }
    }
  },
  // 监听,当路由发生变化的时候执行
  watch: {
    $route: {
      handler: function (val, oldVal) {
        this.$refs.LAY_flow.$el.innerHTML = ''
        this.flowlist()
      }
    }
  },
  methods: {
    getList (page, next) {
      const _this = this
      _this.listQuery.tag = _this.$route.query.tag || ''
      _this.listQuery.page = page || 1
      this.$store.dispatch('postList', _this.listQuery).then((data) => {
        let pageCount = data.total ? Math.ceil(data.total / _this.listQuery.limit) : 0
        let dat = data.data || ''
        let lis = []
        for (let i = 0, leng = dat.length; i < leng; i++) {
          let str = ''
          str += '<div class="post-preview">' +
            '<a href="#/article?slug=' + dat[i].slug + '">' +
            '<h2 class="post-title">' + dat[i].title + '</h2>' +
            '<h3 class="post-subtitle">' + dat[i].subtitle + '</h3>' +
            '</a>' +
            '<p class="post-meta"> ' +
            dat[i].published_at +
            ' in '
          for (let x in dat[i].tags) {
            str += '<a href="#/?tag=' + dat[i].tags[x] + '&title=' + dat[i].tags[x] + '&subtitle=' + dat[i].tags_title[x] + '" class="tags">' + dat[i].tags[x] + '</a> '
          }
          str += '</p> ' +
            '</div>'
          lis.push(str)
        }
        next(lis.join(''), page < pageCount)
      }).catch((e) => {
        let msg = e.toString().split(':')[1] || '未知错误206'
        Message({
          showClose: true,
          message: msg,
          type: 'error'
        })
      })
    },
    flowlist () {
      const _this = this
      window.layui.use('flow', function () {
        let flow = window.layui.flow
        flow.load({
          elem: '#LAY_flow', // 流加载容器
          scrollElem: '#LAY_flow', // 滚动条所在元素，一般不用填，此处只是演示需要。
          isAuto: false,
          isLazyimg: true, // 是否懒加载图片
          done: function (page, next) { // 加载下一页
            _this.getList(page, next)
          }
        })
      })
    }
  },
  mounted () {
    this.$nextTick(function () {
      this.flowlist()
    })
  }
}
</script>
