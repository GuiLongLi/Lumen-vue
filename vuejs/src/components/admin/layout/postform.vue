<template>
  <div>
    <el-row :gutter="0" justify="center">
      <el-col :xs="20" :sm="20" :md="20" :lg="20" :xl="20" :push="2" class="bg-info form-title text-center">
        {{ titleName }}文章
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
      <el-input type="hidden" v-model="formData.old_tags"></el-input>
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
          <el-form-item label="内容" prop="content_html">
            <!--<el-input-->
              <!--type="textarea"-->
              <!--v-model="formData.content_html"-->
              <!--@keyup.enter.native="submitForm('form')"-->
              <!--clearable>-->
            <!--</el-input>-->
            <Uediter :value="formData.content_html" :config="ueditor.config" ref="ue" class="margin-top-40"></Uediter>
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
          <el-form-item label="封面图" prop="page_image">
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
          <el-form-item label="标签" prop="tag">
            <el-select v-model="formData.tags" multiple placeholder="请选择">
              <el-option
                v-for="item in tagoptions"
                :key="item.value"
                :label="item.label"
                :value="item.value"
                >
              </el-option>
            </el-select>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="0" justify="center" class="bg-white">
        <el-col :xs="20" :sm="20" :md="20" :lg="20" :xl="20" :push="2">
          <el-form-item label="发布日期" prop="published_at">
            <el-date-picker
              v-model="formData.published_at"
              value-format="yyyy-MM-dd HH:mm:ss"
              type="datetime"
              placeholder="选择日期时间"
              align="right"
              class="block">
            </el-date-picker>
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
import Uediter from '@/components/layout/ue.vue'
import { Message } from 'element-ui'
export default {
  components: {Uediter},
  props: {
    formloading: false,
    titleName: '',
    tagoptions: '',
    ueditor: {
      value: '',
      config: {}
    },
    formData: {
      id: 0,
      title: '',
      subtitle: '',
      content_html: '',
      page_image: '',
      published_at: new Date(),
      meta_description: '',
      old_tags: '',
      tags: []
    }
  },
  data () {
    return {
      loading: false,
      rule: {
        title: [
          {required: true, message: '请输入标题', trigger: 'blur'},
          {min: 1, max: 100, message: '标题长度在1到100个字', trigger: 'blur'}
        ],
        subtitle: [
          {required: true, message: '请输入副标题', trigger: 'blur'},
          {min: 1, max: 100, message: '副标题长度在1到100个字', trigger: 'blur'}
        ]
      }
    }
  },
  watch: {
    formloading: function (val, oldVal) {
      this.loading = !!val
    }
  },
  methods: {
    submitForm (formName) {
      this.formData.content_html = this.$refs.ue.getUEContent()
      if (this.formData.content_html === '' || this.formData.content_html === null) {
        Message({
          showClose: true,
          message: '内容不能为空',
          type: 'error'
        })
        return false
      }
      this.$refs[formName].validate((valid) => {
        if (valid) {
          this.loading = true
          this.$store.dispatch('postCreate', this.formData).then((msg) => {
            this.loading = false
            Message({
              showClose: true,
              message: msg,
              type: 'success'
            })
            this.$router.push({path: '/auth/post'})
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
            message: '你还有未填写的项目',
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
