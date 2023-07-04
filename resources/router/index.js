import { createRouter, createWebHistory } from "vue-router";

const routes = [
  {
    path: "/",
    name: "home",
    component: () => import("../pages/posts/home.vue"),
  },
  {
    path: "/post",
    name: "post",
    component: () => import("../pages/posts/Index.vue"),
  },
  {
    path: "/create",
    name: "post.create",
    component: () => import("../pages/posts/Create.vue"),
  },
  {
    path: "/edit/:id",
    name: "posts.edit",
    component: () => import("../pages/posts/Update.vue"),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router