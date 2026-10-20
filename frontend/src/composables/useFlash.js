import { reactive, readonly } from 'vue';

// A single app-wide notification (e.g. "Alat berhasil dihapus").
const state = reactive({ message: '', type: 'success' });
let timer = null;

export function useFlash() {
  function clear() {
    state.message = '';
    clearTimeout(timer);
  }

  function show(message, type = 'success', duration = 4000) {
    clearTimeout(timer);
    state.message = message;
    state.type = type;

    if (duration > 0) {
      timer = setTimeout(clear, duration);
    }
  }

  return { flash: readonly(state), show, clear };
}
