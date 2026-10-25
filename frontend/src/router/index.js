import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '@/composables/useAuth';

const routes = [
  {
    path: '/',
    redirect: { name: 'gears.index' },
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/LoginView.vue'),
    meta: { guestOnly: true, title: 'Login' },
  },
  {
    path: '/gears',
    name: 'gears.index',
    component: () => import('@/views/GearListView.vue'),
    meta: { requiresAuth: true, title: 'Daftar Alat' },
  },
  {
    path: '/gears/create',
    name: 'gears.create',
    component: () => import('@/views/GearFormView.vue'),
    meta: { requiresAuth: true, title: 'Tambah Alat' },
  },
  {
    path: '/gears/:id/edit',
    name: 'gears.edit',
    component: () => import('@/views/GearFormView.vue'),
    props: true,
    meta: { requiresAuth: true, title: 'Ubah Alat' },
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/views/NotFoundView.vue'),
    meta: { title: 'Halaman tidak ditemukan' },
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior: () => ({ top: 0 }),
});

// Route guard: protected pages need a token, the login page does not.
router.beforeEach((to) => {
  const { isAuthenticated } = useAuth();

  if (to.meta.requiresAuth && !isAuthenticated.value) {
    return { name: 'login', query: { redirect: to.fullPath } };
  }

  if (to.meta.guestOnly && isAuthenticated.value) {
    return { name: 'gears.index' };
  }

  return true;
});

router.afterEach((to) => {
  document.title = to.meta.title ? `${to.meta.title} · SummitGear` : 'SummitGear';
});

export default router;
