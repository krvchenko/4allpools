<template>
  <el-main>
    <el-row :gutter="20">
      <el-col :span="20" :offset="2">
        <el-card class="box-card">
          <div slot="header" class="clearfix">
            <el-page-header @back="goBack" title="Назад к списку">
              <div slot="content">
                <span>{{ form.name }}</span>
              </div>
            </el-page-header>
          </div>
          <el-form v-model="form" label-width="180px" v-loading="loading">
            <el-form-item
              label="Имя"
              :class="{ 'is-error': form.errors.has('name') }"
            >
              <el-input
                v-model="form.name"
                clearable
                name="name"
                placeholder="Имя"
                id="name"
              />
              <has-error :form="form" field="name" />
            </el-form-item>
            <el-form-item
              label="Email"
              :class="{ 'is-error': form.errors.has('email') }"
            >
              <el-input
                v-model="form.email"
                clearable
                name="email"
                type="email"
                placeholder="Email"
                id="email"
              />
              <has-error :form="form" field="email" />
            </el-form-item>

            <el-form-item
              label="Теги"
            >
              <el-select
                style="width: 100%;"
                v-model="form.tags"
                multiple
                filterable
                allow-create
                default-first-option
                placeholder="Choose tags for your article">
                <el-option
                  v-for="item in form.tags"
                  :key="item"
                  :label="item"
                  :value="item">
                </el-option>
              </el-select>
            </el-form-item>

            <el-form-item>
              <el-select
                v-model="form.manager_id"
                clearable
                filterable
                reserve-keyword
                placeholder="Введите имя менеджера"
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

            <el-form-item
              label="Дата обновления"
            >
              <el-date-picker
                v-model="form.updated_at"
                type="datetime"
                placeholder="Выберите день"
                format="yyyy-MM-dd"
                value-format="yyyy-MM-dd"
              />
            </el-form-item>

            <el-form-item>
              <el-button 
                type="primary"
                @click="handleSubmit"
                :loading="loading"
              >
                Сохранить
              </el-button>
              <el-button 
                type="danger"
                @click="handleDelete"
                :loading="loading"
              >
                Удалить
              </el-button>
            </el-form-item>
          </el-form>
        </el-card>
      </el-col>
    </el-row>
  </el-main>
</template>

<script>
import axios from 'axios'
import Form from 'vform'

export default {
  layout: 'basic',

  metaInfo () {},

  computed: {},

  data: () => ({
    loading: true,
    form: new Form({
      manager_id: null,
      name: null,
      email: null,
      tags: null,
      updated_at: null,
    }),
    managers: [],
  }),

  mounted() {
    this.fetchData()
    this.fetchManagers()
  },

  methods: {
    async fetchData() {
      this.loading = true
      await axios.get(`/api/clients/${this.$route.params.id}`).then(res => {
        this.form.keys().forEach(key => {
          this.form[key] = res.data[key]
        })
        this.loading = false
      })
    },

    async handleSubmit() {
      this.loading = true
      await this.form.patch(`/api/clients/${this.$route.params.id}`)
        .then(res => {
          this.loading = false
        })
        .catch(e => {
          this.loading = false
        })
    },

    async handleDelete() {
      this.$msgbox.confirm(`Вы уверены, что хотите удалить ${this.form.name}?`,{
        type: 'warning',
        confirmButtonText: 'Да',
        cancelButtonText: 'Нет',
      })
      .then(async () => {
        this.loading = true
        await this.form.delete(`/api/clients/${this.$route.params.id}`)
          .then(res => {
            this.loading = false
            this.$router.push({ name: 'clients.index' })
          })
          .catch(e => {
            this.loading = false
          })
      })
      .catch(action => {})
    },

    async fetchManagers(q) {
      await axios.get('/api/managers').then(res => {
        this.managers = res.data.map(item => {
          return {
            value: item.id,
            label: item.name
          }
        })
      })
    },

    goBack() {
      this.$router.push({ name: 'clients.index' })
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
