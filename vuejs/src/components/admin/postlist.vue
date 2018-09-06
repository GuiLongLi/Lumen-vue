<template>
  <div class="content-wrap content-wrap-auth">
    <Header></Header>
    <el-container>
      <el-main class="margin-top-15">
        <el-row :gutter="0" justify="left">
          <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="login-title">
            <el-breadcrumb separator-class="el-icon-arrow-right">
              <el-breadcrumb-item>文章管理</el-breadcrumb-item>
            </el-breadcrumb>
            <router-link tag="a" to="/auth/post/create" class="color-white post-button router-link-active">
              <el-button type="primary" icon="el-icon-edit" size="mini">创建文章</el-button>
            </router-link>
          </el-col>
        </el-row>
        <el-row :gutter="0" justify="center">
          <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
            <div class="block text-center">
              <el-pagination
                @current-change="handleCurrentChange"
                :current-page="listQuery.page"
                :page-size="listQuery.limit"
                layout="slot, prev, pager, next, jumper"
                :total="total"
                class="inline-block">
                <span class="el-pagination__total">共 {{ total }} 条数据</span>
              </el-pagination>
            </div>
            <el-table
              :data="list"
              border
              style="width: 100%"
              v-loading="listLoading"
              element-loading-background="rgba(0, 0, 0, 0.5)"
              element-loading-text="正在加载中">
              <el-table-column
                align="center"
                prop="title"
                label="标题">
              </el-table-column>
              <el-table-column
                align="center"
                prop="author"
                label="作者">
              </el-table-column>
              <el-table-column
                right
                align="center"
                prop="published_at"
                label="发布日期">
              </el-table-column>
              <el-table-column
                right
                align="center"
                prop="tags"
                label="标签">
                <template slot-scope="scope">
                  <el-tag
                    v-for="item in scope.row.tags"
                    :key="item"
                    type="primary"
                    class="margin-right-5"
                    disable-transitions>{{item}}</el-tag>
                </template>
              </el-table-column>
              <el-table-column
                align="center"
                fixed="right"
                label="操作"
                width="150">
                <template slot-scope="scope">
                  <el-button
                    size="mini"
                    @click="handleEdit(scope.$index, scope.row)">编辑</el-button>
                  <el-button
                    size="mini"
                    type="danger"
                    @click="handleDelete(scope.$index, scope.row)">删除</el-button>
                </template>
              </el-table-column>
            </el-table>
          </el-col>
        </el-row>
      </el-main>
    </el-container>
    <Footer></Footer>
  </div>
</template>
<script>
import Header from './layout/header.vue'
import Footer from '../layout/footer.vue'
import { Message } from 'element-ui'
export default {
  components: { Header, Footer },
  data () {
    return {
      title: '文章管理',
      uname: '',
      list: null,
      total: null,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 10
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
  created () {
    this.getList()
  },
  methods: {
    getList () {
      this.listLoading = true
      this.$store.dispatch('postList', this.listQuery).then((data) => {
        this.listLoading = false
        this.list = data.data
        this.total = data.total
      }).catch((e) => {
        let msg = e.toString().split(':')[1] || '未知错误206'
        Message({
          showClose: true,
          message: msg,
          type: 'error'
        })
        this.listLoading = false
      })
    },
    handleCurrentChange (val) {
      this.listQuery.page = val
      this.getList()
    },
    handleEdit (index, row) {
      this.$router.push({path: 'post/modify', query: {id: row.id}})
    },
    handleDelete (index, row) {
      this.$confirm('删除操作, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.listLoading = true
        this.$store.dispatch('postDelete', {id: row.id}).then((msg) => {
          this.listLoading = false
          // 删除的下标是 0 ，且 不是第一页
          if (index === 0 && this.listQuery.page !== 1) {
            // 当前页只剩下一个, 且 是最后一页
            if (this.total % this.listQuery.limit === 1 && this.listQuery.page === Math.ceil(this.total / this.listQuery.limit)) {
              // 返回到上一页
              this.listQuery.page = this.listQuery.page - 1
            }
          }
          Message({
            showClose: true,
            message: msg,
            type: 'success'
          })
          this.getList()
        }).catch((e) => {
          let msg = e.toString().split(':')[1] || '未知错误207'
          Message({
            showClose: true,
            message: msg,
            type: 'error'
          })
          this.listLoading = false
        })
      }).catch((e) => {})
    }
  },
  mounted: function () {
    // Code that will run only after the
    // entire view has been rendered
    this.$nextTick(function () {
    })
  }
}
</script>
