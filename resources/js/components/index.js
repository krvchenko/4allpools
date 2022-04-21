import Vue from 'vue'
import { HasError } from 'vform/components/bootstrap5'

// Components that are registered globaly.
[
  HasError,
].forEach(Component => {
  Vue.component(Component.name, Component)
})