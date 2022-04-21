<template>
  <transition name="page" mode="out-in">
    <el-container style="max-width: 1320px; margin: 0 auto;">
      <el-header>
        <b style="font-size: 20px; line-height: 60px;">LaravelVue</b>
      </el-header>
      <el-container>
        <sidebar />
        <router-view />
      </el-container>
      <el-footer>
        <b style="font-size: 14px; line-height: 60px;">LaravelVue</b>
      </el-footer>
    </el-container>
  </transition>
</template>

<script>
import Header from '~/components/Header'
import Sidebar from '~/components/Sidebar'

export default {
  name: 'MainLayout',

  components: {
    Header,
    Sidebar,
  }
}
</script>
