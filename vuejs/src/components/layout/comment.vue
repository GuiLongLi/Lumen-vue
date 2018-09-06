<template>
  <div>
    <el-form ref="form" :rules="rule" :model="formData" status-icon class="demo-ruleForm">
      <el-input type="hidden" v-model="aid" class="input-hidden"></el-input>
      <el-input type="hidden" v-model="formData.pid" class="input-hidden"></el-input>
      <el-row :gutter="0" justify="center">
        <el-col :xs="22" :sm="22" :md="22" :lg="22" :xl="22" :push="1">
          <el-form-item label="理性发贴,谢绝地域攻击。" prop="content_html">
            <el-input
            type="textarea"
            placeholder="抵制低俗，文明上网，登录发贴"
            v-model="formData.content_html"
            clearable>
            </el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row type="flex" justify="right">
        <el-col :xs="22" :sm="22" :md="22" :lg="22" :xl="22" :push="1">
          <el-form-item class="margin-left-0 float-right">
            <el-button type="primary" @click="submitForm('form')" size="mini">提交</el-button>
          </el-form-item>
        </el-col>
      </el-row>
    </el-form>
  </div>
</template>
<script>
import { Message } from 'element-ui'
export default {
  props: {
    aid: '',
    commentList: ''
  },
  data () {
    return {
      formData: {
        aid: '',
        pid: '',
        content_html: ''
      },
      rule: {
        content_html: [
          {required: true, message: '请输入评论', trigger: 'blur'},
          {min: 1, max: 5000, message: '评论长度在1到5000个字', trigger: 'blur'}
        ]
      }
    }
  },
  methods: {
    login () {
      this.$router.push({path: '/auth/login', query: {callback: this.$route.fullPath}})
    },
    submitForm (formName) {
      if (!window.localStorage.token) {
        this.login()
      }
      this.formData.aid = this.aid
      this.$refs[formName].validate((valid) => {
        if (valid) {
          this.loading = true
          this.$store.dispatch('articleComment', this.formData).then((msg) => {
            this.loading = false
            Message({
              showClose: true,
              message: msg,
              type: 'success'
            })
            this.formData.content_html = ''
            this.$emit('commentList')
          }).catch((e) => {
            let msg = e.toString().split(':')[1] || '未知错误201'
            Message({
              showClose: true,
              message: msg,
              type: 'error'
            })
            this.loading = false
          })
        } else {
          Message({
            showClose: true,
            message: '验证错误',
            type: 'error'
          })
          return false
        }
      })
    }
  }
}
</script>
