/**
 * Human-readable message from an Axios error.
 */
export function getErrorMessage(error, fallback = 'Terjadi kesalahan. Silakan coba lagi.') {
  if (!error?.response) {
    return 'Tidak dapat terhubung ke server. Pastikan backend sedang berjalan.';
  }

  return error.response.data?.message || fallback;
}

/**
 * Convert Laravel 422 errors into { field: 'first message' }.
 */
export function getValidationErrors(error) {
  if (error?.response?.status !== 422) {
    return {};
  }

  const errors = error.response.data?.errors ?? {};

  return Object.fromEntries(
    Object.entries(errors).map(([field, messages]) => [field, messages[0]]),
  );
}
