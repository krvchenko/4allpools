<template>
  <el-main>
    <el-table
      :data="data"
      :style="tableStyles"
      @sort-change="handleSortChange"
      v-loading.lock="loading">
      <el-table-column
        label="ID"
        width="60"
        sortable
        prop="id"
        fixed="left">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column
        label="Имя менеджера"
        sortable
        prop="name"
        fixed="left">
        <template slot-scope="scope">
          <span>{{ scope.row.name }}</span>
        </template>
      </el-table-column>
      <el-table-column
        label="Изменён"
        sortable
        prop="updated_at"
        fixed="left">
        <template slot-scope="scope">
          <span>{{ scope.row.updated_at }}</span>
        </template>
      </el-table-column>
      <el-table-column
        label="Email"
        sortable
        prop="email"
        fixed="left">
        <template slot-scope="scope">
          <span>{{ scope.row.email }}</span>
        </template>
      </el-table-column>
      <el-table-column
        align="right"
        fixed="right"
        width="180px"
        >
        <template slot="header" slot-scope="scope">
          <el-input
            v-model="search"
            size="medium"
            placeholder="Поиск"
            clearable
            @input="handleFilterChange"/>
        </template>
        <template slot-scope="scope">
          <el-button-group>
            <el-button
              size="mini"
              type="primary"
              @click="handleEdit(scope.$index, scope.row)"
            >
              Редактировать
            </el-button>
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
    <el-pagination
      @size-change="handleSizeChange"
      @current-change="handleCurrentChange"
      :current-page.sync="current_page"
      :page-sizes="page_sizes"
      :page-size="per_page"
      :total="total"
      :background="true"
      layout="total, prev, pager, next, sizes"
    />
  </el-main>
</template>

<script>
import { mapGetters } from 'vuex'
import axios from 'axios'

export default {
  layout: 'basic',

  metaInfo () {},

  computed: {
  },

  data: () => ({
    loading: true,
    tableStyles: {
      margin: '0 0 20px',
      width: '100%',
    },
    page_sizes: [10, 20, 30, 40, 50],
    page: 1,
    per_page: 10,
    search: '',
    sort: 'id',
    order: 'desc',
    from: null,
    to: null,
    next_page_url: null,
    prev_page_url: null,
    current_page: null,
    last_page: null,
    total: null,
    data: [],
    links: [],
  }),

  mounted() {
    this.fetchData()
  },

  methods: {
    async fetchData() {
      this.loading = true
      await axios.get('/api/managers', {
        params: {
          sort: this.sort,
          order: this.order,
          search: this.search,
          filter: this.filter,
          page: this.page,
          per_page: this.per_page,
        }
      }).then(res => {
        Object.keys(res.data).forEach(key => {
          this[key] = res.data[key]
        })
        this.loading = false
      })
    },

    handleFilterSubmit() {
      this.fetchData()
    },

    handleSizeChange($event) {
      this.per_page = $event
      this.fetchData()
    },

    handleCurrentChange($event) {
      this.page = $event
      this.fetchData()
    },

    handleFilterChange($event) {
      setTimeout(() => {
        this.fetchData()
      }, 300)
    },

    handleSortChange($event) {
      const orders = {
        ascending: 'asc',
        descending: 'desc'
      }
      this.sort = $event.prop
      this.order = orders[$event.order]
      this.fetchData()
    },

    handleEdit(index, manager) {
      this.$router.push({ name: 'managers.edit', params: { id: manager.id } })
    },
  },
}
</script>

<style scoped>
.top-right {
  position: absolute;
  right: 10px;
  top: 18px;
}

.title {
  font-size: 85px;
}
</style>
