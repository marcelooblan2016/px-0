import Vue from 'vue'

require('./bootstrap');

window.Vue = Vue
import VueLazyload from 'vue-lazyload'

Vue.use(VueLazyload)

// or with options
//const loadimage = require('./assets/loading.gif')
//const errorimage = require('./assets/error.gif')

Vue.use(VueLazyload, {
  preLoad: 1.3,
  //error: errorimage,
  //loading: loadimage,
  attempt: 1
})

// include validation configurations
require('./validator')

// Converter
Vue.component('convert', require('./components/Convert/Index.vue').default);

const app = new Vue({
    el: '#app',
  })
  