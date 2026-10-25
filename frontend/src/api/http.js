import axios from 'axios';
import { env } from '@/config/env';
import { authStorage } from '@/utils/authStorage';

/**
 * Shared Axios instance. Every service imports this, so the base URL
 * and the auth header live in exactly one place.
 */
export const http = axios.create({
  baseURL: env.apiUrl,
  timeout: 15000,
  headers: {
    Accept: 'application/json',
  },
});

let unauthorizedHandler = null;

/**
 * Register what should happen when the API answers 401
 * (registered in main.js to avoid a circular import with the router).
 */
export function onUnauthorized(handler) {
  unauthorizedHandler = handler;
}

// Attach the bearer token to every outgoing request.
http.interceptors.request.use((config) => {
  const token = authStorage.getToken();

  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
});

// An expired or revoked token logs the user out globally.
http.interceptors.response.use(
  (response) => response,
  (error) => {
    const isUnauthorized = error.response?.status === 401;
    const isLoginRequest = error.config?.url?.endsWith('/login');

    if (isUnauthorized && !isLoginRequest && unauthorizedHandler) {
      unauthorizedHandler();
    }

    return Promise.reject(error);
  },
);
