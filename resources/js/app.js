import Vue from 'vue'

require('./bootstrap');

window.Vue = Vue

// include validation configurations
require('./validator')

// Converter
Vue.component('convert', require('./components/Convert/Index.vue').default);

const app = new Vue({
    el: '#app',
  })
  