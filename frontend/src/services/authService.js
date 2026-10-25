import { http } from '@/api/http';

export const authService = {
  /**
   * @returns {Promise<{token: string, token_type: string, user: object}>}
   */
  async login({ email, password }) {
    const { data } = await http.post('/login', {
      email,
      password,
      device_name: 'web',
    });

    return data.data;
  },

  async logout() {
    await http.post('/logout');
  },

  async me() {
    const { data } = await http.get('/me');

    return data.data;
  },
};
