import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../utils/auth';

const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../Pages/LoginPage.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/registro',
    name: 'Registro',
    component: () => import('../Pages/LoginPage.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/catalogo-premios',
    name: 'CatalogoPremios',
    component: () => import('../Pages/CataloguesAwards.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/alianzas',
    name: 'Alianzas',
    component: () => import('../Pages/Alliance.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/home',
    name: 'Home',
    component: () => import('../Pages/Home.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    redirect: '/login'
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    }
    return { top: 0 };
  }
});

// Guard de navegación
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  const requiresAuth = to.meta.requiresAuth;
  const isAuthenticated = authStore.isAuthenticated;

  if (requiresAuth && !isAuthenticated) {
    next('/login');
  } else if (!requiresAuth && isAuthenticated && to.name === 'Login') {
    next('/catalogo-premios');
  } else {
    next();
  }
});

export default router;
