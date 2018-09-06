<template>
  <div>
    <el-row type="flex" justify="center">
      <el-col :xs="22" :sm="22" :md="22" :lg="22" :xl="22" id="LAY_flow" ref="LAY_flow">
      </el-col>
    </el-row>
  </div>
</template>
<script>
import { Message } from 'element-ui'
export default {
  props: {
    aid: ''
  },
  data () {
    return {
      list: null,
      total: null,
      listQuery: {
        page: 1,
        limit: 5
      }
    }
  },
  methods: {
    getList (page, next) {
      const _this = this
      _this.listQuery.page = page || 1
      _this.listQuery.aid = this.aid || 0
      this.$store.dispatch('commentList', _this.listQuery).then((data) => {
        let pageCount = data.total ? Math.ceil(data.total / _this.listQuery.limit) : 0
        let dat = data.data || ''
        let lis = []
        for (let i = 0, leng = dat.length; i < leng; i++) {
          let str = ''
          str += '<div class="comment-preview">'
          str += '<div class="comment-preview-left el-col-xs-1 el-col-sm-1 el-col-md-1 el-col-lg-1 el-col-xl-1">' +
            '<img src="../../../static/favicon.ico" alt="">' +
            '</div>'
          str += '<div class="comment-preview-right el-col-xs-22 el-col-sm-22 el-col-md-22 el-col-lg-22 el-col-xl-22" style="display: inline-block;">' +
            '<h3 class="comment-subtitle"><a href="javascript:;">' + dat[i].author + '</a> : <span class="comment-content">' + dat[i].content + '</span></h3>' +
            '<p class="comment-meta"> ' +
            dat[i].created_at
          str += '<a href="javascript:;" >回复</a> '
          str += '</p> ' +
            '</div>' +
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
          isAuto: true,
          end: '没有评论了',
          isLazyimg: true, // 是否懒加载图片
          done: function (page, next) { // 加载下一页
            _this.getList(page, next)
          }
        })
      })
    },
    newFlow () {
      this.$refs['LAY_flow'].$el.innerHTML = ''
      this.listQuery.page = 1
      this.flowlist()
    }
  },
  mounted () {
    this.$nextTick(function () {
    })
  }
}
</script>
