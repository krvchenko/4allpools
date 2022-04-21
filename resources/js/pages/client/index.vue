<template>
  <el-main>
    <el-form :inline="true" :model="filter" class="demo-form-inline">
      <el-form-item label="Фильтры">
        <el-select
          v-model="filter.column"
          placeholder="Выберите фильтр"
          filterable
          clearable
          style="width: 100%;"
        >
          <el-option
            v-for="item in filterOptions"
            :key="item.value"
            :label="item.label"
            :value="item.column">
          </el-option>
        </el-select>
      </el-form-item>

      <el-form-item v-if="filter.column === 'clients.id'">
        <el-input
          placeholder="Введите ID клиента"
          v-model="filter.value"
          clearable
          style="width: 100%;"
        />
      </el-form-item>

      <el-form-item v-if="filter.column === 'clients.name'">
        <el-select
          v-model="filter.value"
          clearable
          multiple
          filterable
          remote
          reserve-keyword
          placeholder="Введите имя клиента"
          :remote-method="fetchClients"
          style="width: 100%;"
        >
          <el-option
            v-for="item in clients"
            :key="item.value"
            :label="item.label"
            :value="item.value">
          </el-option>
        </el-select>
      </el-form-item>

      <el-form-item v-if="filter.column === 'manager.name'">
        <el-select
          v-model="filter.value"
          clearable
          multiple
          filterable
          remote
          reserve-keyword
          placeholder="Введите имя менеджера"
          :remote-method="fetchManagers"
          style="width: 100%;"
        >
          <el-option
            v-for="item in managers"
            :key="item.value"
            :label="item.label"
            :value="item.value">
          </el-option>
        </el-select>
      </el-form-item>

<!--       <el-form-item v-if="filter.column === 'manager.name'">
        <el-autocomplete
          v-model="filter.value"
          :fetch-suggestions="fetchManagers"
          placeholder="Введите имя менеджера"
        />
      </el-form-item> -->

      <el-form-item v-if="filter.column === 'clients.updated_at'">
        <el-date-picker
          v-model="filter.value"
          type="date"
          placeholder="Выберите день"
          format="dd.MM.yyyy"
          value-format="yyyy-MM-dd"
        />
      </el-form-item>

      <el-form-item>
        <el-button 
          v-if="filter.column"
          type="primary"
          @click="handleFilterSubmit"
        >
          Найти
        </el-button>
      </el-form-item>

      <el-form-item>
        <el-button 
          v-if="filter.column"
          type="danger"
          @click="handleFilterReset"
        >
          Очистить фильтры
        </el-button>
      </el-form-item>
    </el-form>

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
        label="Имя клиента"
        sortable
        prop="name"
        fixed="left">
        <template slot-scope="scope">
          <span>{{ scope.row.name }}</span>
        </template>
      </el-table-column>
      <el-table-column
        label="Имя менеджера"
        sortable
        prop="manager.name"
        fixed="left">
        <template slot-scope="scope">
          <span>{{ scope.row.manager.name }}</span>
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
            @input="handleSearch"/>
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
    filter: {
      column: null,
      value: null,
    },
    filterOptions: [{
      column: 'clients.id',
      value: null,
      label: 'ID клиента',
    },{
      column: 'clients.name',
      value: null,
      label: 'Имя клиента',
    },{
      column: 'manager.name',
      value: null,
      label: 'Имя менеджера',
    },{
      column: 'clients.updated_at',
      value: null,
      label: 'Дата изменения',
    }],
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
    clients: [],
    managers: [],
  }),

  mounted() {
    this.fetchData()
  },

  methods: {
    async fetchData() {
      this.loading = true
      await axios.get('/api/clients', {
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

    async fetchClients(q) {
      await axios.get('/api/clients', {
        params: {
          search: q,
          per_page: null,
          page: 1
        }
      }).then(res => {
        if (q !== '') {
          this.clients = res.data.map(item => {
            return {
              value: this.filter.column === 'clients.id' ? item.id : item.name,
              label: item.name
            }
          }).filter(item => {
            return item.label.toLowerCase().indexOf(q.toLowerCase()) > -1
          })
        } else {
          this.clients = []
        }
      })
    },

    async fetchManagers(q) {
      await axios.get('/api/managers', {
        params: {
          search: q,
          per_page: null,
          page: 1
        }
      }).then(res => {
        if (q !== '') {
          this.managers = res.data.map(item => {
            return {
              value: item.name,
              label: item.name
            }
          }).filter(item => {
            return item.label.toLowerCase().indexOf(q.toLowerCase()) > -1
          })
        } else {
          this.managers = []
        }
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

    handleSearch($event) {
      this.page = 1
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

    handleFilterReset() {
      this.filter = {
        column: null,
        value: null,
      },

      this.fetchData()
    },

    handleEdit(index, client) {
      this.$router.push({ name: 'clients.edit', params: { id: client.id } })
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
