<template>
  <el-aside width="300px" style="background-color: rgb(238, 241, 246)">
    <el-menu>
      <el-menu-item v-for="(item, index) in menu" :key="index">
        <el-link
          style="display: block;"
          :underline="false"
          @click.prevent="handleClick(item.route)"
        >
          {{ item.title }}
        </el-link>
      </el-menu-item>
    </el-menu>
  </el-aside>
</template>

<script>

  export default {
    name: 'Sidebar',

    components: {},

    props: {},

    data: () => ({
      menu: [{
        title: 'Клиенты',
        route: 'clients.index'
      },{
        title: 'Менеджеры',
        route: 'managers.index'
      }]
    }),

    created() {},

    computed: {},

    watch: {},

    methods: {
      handleClick(route) {
        this.$router.push({ name: route })
      },
    },
  }
</script>

<style lang="scss"></style>