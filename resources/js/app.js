import Vue from 'vue'
import store from '~/store'
import router from '~/router'
import App from '~/components/App'

import ElementUI from 'element-ui'
import locale from 'element-ui/lib/locale/lang/ru-RU'

Vue.use(ElementUI, { locale })

// import '~/plugins'
import '~/components'

Vue.config.productionTip = false

new Vue({
  store,
  router,
  ...App
})
