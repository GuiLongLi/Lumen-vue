<template>
  <div class="content-wrap content-wrap-login">
    <Header></Header>
    <el-container>
      <el-main class="margin-top-15">
        <el-row :gutter="0" justify="center">
          <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="bg-info form-title text-center">
            用户登录
          </el-col>
        </el-row>
        <el-form ref="form" :rules="rule" :model="form" status-icon  class="demo-ruleForm">
          <el-row :gutter="0" justify="center" class="bg-white">
            <el-col :xs="20" :sm="20" :md="20" :lg="20" :xl="20" :push="2">
              <el-form-item label="邮箱地址" prop="email">
              <el-input
                placeholder=""
                v-model="form.email"
                @keyup.enter.native="submitForm('form')"
                clearable>
              </el-input>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="0" justify="center" class="bg-white">
            <el-col :xs="20" :sm="20" :md="20" :lg="20" :xl="20" :push="2">
              <el-form-item label="密码" prop="password">
                <el-input
                  type="password"
                  placeholder=""
                  v-model="form.password"
                  @keyup.enter.native="submitForm('form')"
                  clearable>
                </el-input>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="0" justify="center" class="bg-white text-center">
            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
              <el-form-item class="margin-left-0">
                <el-button type="primary" @click="submitForm('form')">提交</el-button>
                <el-button @click="resetForm('form')">重置</el-button>
              </el-form-item>
            </el-col>
          </el-row>
        </el-form>
      </el-main>
    </el-container>
    <Footer></Footer>
  </div>
</template>
<script>
import Header from '../layout/header.vue'
import Footer from '../layout/footer.vue'
import { Message } from 'element-ui'
export default {
  components: { Header, Footer },
  data () {
    let validateEmail = (rule, value, callback) => {
      let preg = /^([0-9A-Za-z\-_.]+)@([0-9a-z]+\.[a-z]{2,5}(\.[a-z]{2})?)$/g
      if (value === '') {
        callback(new Error('请输入邮箱'))
      } else if (!preg.test(value)) {
        callback(new Error('邮箱格式不正确'))
      } else {
        callback()
      }
    }
    let validatePass = (rule, value, callback) => {
      if (value === '') {
        callback(new Error('请输入密码'))
      } else {
        callback()
      }
    }
    return {
      title: '用户登录',
      form: {
        email: '',
        password: ''
      },
      rule: {
        email: [
          {validator: validateEmail, trigger: 'blur'}
        ],
        password: [
          {validator: validatePass, trigger: 'blur'}
        ]
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
    submitForm (formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          this.loading = true
          this.$store.dispatch('login', this.form).then((msg) => {
            this.loading = false
            Message({
              showClose: true,
              message: msg,
              type: 'success'
            })
          }).catch((e) => {
            let msg = e.toString().split(':')[1] || '未知错误204'
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
    },
    resetForm (formName) {
      this.$refs[formName].resetFields()
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
