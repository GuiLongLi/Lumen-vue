<template>
  <div>
    <el-row :gutter="0" justify="center">
      <el-col :xs="20" :sm="20" :md="20" :lg="20" :xl="20" :push="2" class="bg-info form-title text-center">
        {{ titleName }}标签
      </el-col>
    </el-row>
    <el-form
      ref="form"
      :rules="rule"
      :model="formData"
      v-loading="loading"
      status-icon
      element-loading-background="rgba(0, 0, 0, 0.5)"
      element-loading-text="正在加载中"
      class="demo-ruleForm">
      <el-input type="hidden" v-model="formData.id"></el-input>
      <el-row :gutter="0" justify="center" class="bg-white">
        <el-col :xs="20" :sm="20" :md="20" :lg="20" :xl="20" :push="2">
          <el-form-item label="标签" prop="tag">
            <el-input
              v-model="formData.tag"
              @keyup.enter.native="submitForm('form')"
              clearable>
            </el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="0" justify="center" class="bg-white">
        <el-col :xs="20" :sm="20" :md="20" :lg="20" :xl="20" :push="2">
          <el-form-item label="标题" prop="title">
            <el-input
              v-model="formData.title"
              @keyup.enter.native="submitForm('form')"
              clearable>
            </el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="0" justify="center" class="bg-white">
        <el-col :xs="20" :sm="20" :md="20" :lg="20" :xl="20" :push="2">
          <el-form-item label="副标题" prop="subtitle">
            <el-input
              v-model="formData.subtitle"
              @keyup.enter.native="submitForm('form')"
              clearable>
            </el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="0" justify="center" class="bg-white">
        <el-col :xs="20" :sm="20" :md="20" :lg="20" :xl="20" :push="2">
          <el-form-item label="描述" prop="meta_description">
            <el-input
              type="textarea"
              v-model="formData.meta_description"
              clearable>
            </el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="0" justify="center" class="bg-white">
        <el-col :xs="20" :sm="20" :md="20" :lg="20" :xl="20" :push="2">
          <el-form-item label="封面图片" prop="page_image">
            <el-input
              v-model="formData.page_image"
              @keyup.enter.native="submitForm('form')"
              clearable>
            </el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="0" justify="center" class="bg-white">
        <el-col :xs="20" :sm="20" :md="20" :lg="20" :xl="20" :push="2">
          <el-form-item label="排序" prop="reverse_direction">
            <el-radio-group v-model="formData.reverse_direction">
              <el-radio label="0" border >升序</el-radio>
              <el-radio label="1" border >降序</el-radio>
            </el-radio-group>
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
  </div>
</template>
<script>
import { Message } from 'element-ui'
export default {
  props: {
    formloading: false,
    titleName: '',
    formData: {
      id: 0,
      tag: '',
      title: '',
      subtitle: '',
      page_image: '',
      meta_description: '',
      reverse_direction: '0'
    }
  },
  data () {
    return {
      loading: false,
      rule: {
        tag: [
          {required: true, message: '请输入标签', trigger: 'blur'},
          {min: 1, max: 50, message: '标签长度在1到50个字', trigger: 'blur'}
        ],
        title: [
          {required: true, message: '请输入标题', trigger: 'blur'},
          {min: 1, max: 50, message: '标题长度在1到50个字', trigger: 'blur'}
        ],
        subtitle: [
          {required: true, message: '请输入副标题', trigger: 'blur'},
          {min: 1, max: 50, message: '副标题长度在1到50个字', trigger: 'blur'}
        ]
      }
    }
  },
  watch: {
    formloading: function (val, oldVal) {
      this.loading = val
    }
  },
  methods: {
    submitForm (formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          this.loading = true
          this.$store.dispatch('tagCreate', this.formData).then((msg) => {
            this.loading = false
            Message({
              showClose: true,
              message: msg,
              type: 'success'
            })
            this.$router.push({path: '/auth/tag'})
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
