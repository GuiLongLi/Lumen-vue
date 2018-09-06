<template>
  <el-container>
    <el-header>
      <el-row :gutter="10">
        <el-col :xs="2" :sm="3" :md="3" :lg="3" :xl="6"><div class="grid-content"></div></el-col>
        <el-col :xs="10" :sm="9" :md="9" :lg="9" :xl="6" class="text-left">
          <router-link tag="a" to="/">博客</router-link>
        </el-col>
        <el-col :xs="10" :sm="9" :md="9" :lg="9" :xl="6" class="text-right">
          <el-dropdown trigger="click" class="hidden-sm-and-up">
            <span class="el-dropdown-link">
              <i class="el-icon-menu hidden-sm-and-up size-15"></i>
            </span>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item class="clearfix">
                <router-link tag="a" to="/auth/login" class="color-gray padding-5-8">登录</router-link>
              </el-dropdown-item>
              <el-dropdown-item class="clearfix">
                <router-link tag="a" to="/auth/register" class="color-gray padding-5-8">注册</router-link>
              </el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
          <div class="hidden-xs-only">
            <router-link tag="a" to="/auth/login" class="color-gray padding-5-8">登录</router-link>
            <router-link tag="a" to="/auth/register" class="color-gray padding-5-8">注册</router-link>
          </div>
        </el-col>
        <el-col :xs="2" :sm="3" :md="3" :lg="3" :xl="6"><div class="grid-content"></div></el-col>
      </el-row>
    </el-header>
  </el-container>
</template>
