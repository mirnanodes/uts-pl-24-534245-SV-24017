import { http } from '@/api/http';

const resource = (id) => `/gears/${encodeURIComponent(id)}`;

/**
 * CRUD calls for the /api/gears resource.
 */
export const gearService = {
  /**
   * @returns {Promise<{data: object[], meta: object, links: object}>}
   */
  async list(params = {}) {
    const { data } = await http.get('/gears', { params });

    return data;
  },

  async find(id) {
    const { data } = await http.get(resource(id));

    return data.data;
  },

  async create(payload) {
    const { data } = await http.post('/gears', payload);

    return data;
  },

  async update(id, payload) {
    const { data } = await http.put(resource(id), payload);

    return data;
  },

  async remove(id) {
    const { data } = await http.delete(resource(id));

    return data;
  },

  /**
   * @returns {Promise<{categories: object[], conditions: object[]}>}
   */
  async options() {
    const { data } = await http.get('/gear-options');

    return data.data;
  },
};
