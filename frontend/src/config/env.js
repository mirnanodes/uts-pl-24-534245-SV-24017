/**
 * Single source of runtime configuration.
 * Values come from Vite environment variables (VITE_*), never hard-coded.
 */
const apiUrl = import.meta.env.VITE_API_URL;

if (!apiUrl) {
  throw new Error(
    'VITE_API_URL belum diatur. Salin frontend/.env.example menjadi .env, ' +
      'atau kirim build arg VITE_API_URL saat build Docker.',
  );
}

export const env = Object.freeze({
  apiUrl: apiUrl.replace(/\/+$/, ''),
  appName: import.meta.env.VITE_APP_NAME || 'SummitGear',
});
