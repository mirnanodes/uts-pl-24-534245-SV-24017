import { computed, reactive } from 'vue';
import { authService } from '@/services/authService';
import { authStorage } from '@/utils/authStorage';

// Module-level state: one shared auth session for the whole app.
const state = reactive({
  token: authStorage.getToken(),
  user: authStorage.getUser(),
});

export function useAuth() {
  const isAuthenticated = computed(() => Boolean(state.token));
  const user = computed(() => state.user);

  async function login(credentials) {
    const { token, user: authenticatedUser } = await authService.login(credentials);

    state.token = token;
    state.user = authenticatedUser;
    authStorage.save(token, authenticatedUser);
  }

  function clearSession() {
    state.token = null;
    state.user = null;
    authStorage.clear();
  }

  async function logout() {
    try {
      if (state.token) {
        await authService.logout();
      }
    } finally {
      // Always drop the local session, even if the API call failed.
      clearSession();
    }
  }

  return { user, isAuthenticated, login, logout, clearSession };
}
