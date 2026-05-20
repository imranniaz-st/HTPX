import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

import DashboardView from '@/views/DashboardView.vue'
import ServersView from '@/views/ServersView.vue'
import ServerDetailsView from '@/views/ServerDetailsView.vue'
import ServerLogsView from '@/views/ServerLogsView.vue'
import AlertsView from '@/views/AlertsView.vue'
import LoginView from '@/views/LoginView.vue'
import AdminView from '@/views/AdminView.vue'

const routes = [
  {
    path: '/',
    redirect: '/dashboard',
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginView,
    meta: { requiresAuth: false },
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: DashboardView,
    meta: { requiresAuth: true },
  },
  {
    path: '/servers',
    name: 'Servers',
    component: ServersView,
    meta: { requiresAuth: true },
  },
  {
    path: '/servers/:id',
    name: 'ServerDetails',
    component: ServerDetailsView,
    meta: { requiresAuth: true },
  },
  {
    path: '/servers/:id/logs',
    name: 'ServerLogs',
    component: ServerLogsView,
    meta: { requiresAuth: true },
  },
  {
    path: '/alerts',
    name: 'Alerts',
    component: AlertsView,
    meta: { requiresAuth: true },
  },
  {
    path: '/admin',
    name: 'Admin',
    component: AdminView,
    meta: { requiresAuth: true },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  const requiresAuth = to.meta.requiresAuth !== false

  if (requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if (to.path === '/login' && authStore.isAuthenticated) {
    next('/dashboard')
  } else {
    next()
  }
})

export default router
