// import { createApp } from 'vue'
// import { createRouter, createWebHistory } from 'vue-router'
// import routes from '~pages'
// import App from './App.vue'

// import 'virtual:windi.css'
// import './assets/css/main.scss'

// const router = createRouter({
//     history: createWebHistory(),
//     routes,
// })

// const app = createApp(App)
// app.use(router)
// app.mount('#app')

import { createApp } from "vue";
import App from './App.vue'

import './assets/css/index.css'

// import 'virtual:windi.css'
// import './assets/css/main.scss'

import router from "./router";

const app = createApp(App)
app.use(router)
app.mount('#app')