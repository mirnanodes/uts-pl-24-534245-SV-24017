import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import { onUnauthorized } from '@/api/http';
import { useAuth } from '@/composables/useAuth';
import { useFlash } from '@/composables/useFlash';
import './assets/main.css';

const app = createApp(App);

app.use(router);

// Global 401 handling: drop the session and send the user back to login.
const { clearSession } = useAuth();
const { show } = useFlash();

onUnauthorized(() => {
  clearSession();

  const current = router.currentRoute.value;
  if (current.name !== 'login') {
    show('Sesi berakhir. Silakan login kembali.', 'warning');
    router.replace({ name: 'login', query: { redirect: current.fullPath } });
  }
});

app.mount('#app');
