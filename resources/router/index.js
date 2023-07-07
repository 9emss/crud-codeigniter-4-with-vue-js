import { createRouter, createWebHistory } from "vue-router";
import Login from '../pages/auth/Login.vue'

const routes = [
  {
    path: "/",
    name: "home",
    component: () => import("../pages/posts/home.vue"),
  },
  {
    path: "/auth",
    name: "login",
    component: Login,
  },
  {
    path: "/post",
    name: "post",
    component: () => import("../pages/posts/Index.vue"),
    meta: {
      requiresAuth: true
    }
  },
  {
    path: "/create",
    name: "post.create",
    component: () => import("../pages/posts/Create.vue"),
    meta: {
      requiresAuth: true
    }
  },
  {
    path: "/edit/:id",
    name: "posts.edit",
    component: () => import("../pages/posts/Update.vue"),
    meta: {
      requiresAuth: true
    }
  },
  {
    path: "/about",
    name: "about",
    component: () => import("../pages/about.vue"),
  },
];



const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('token')

  // If the route requires authentication and user is not authenticated, then redirect to the login page
  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/')
  } else {
    next()
  }

})

export default router