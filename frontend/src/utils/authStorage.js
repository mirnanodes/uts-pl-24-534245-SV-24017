const TOKEN_KEY = 'summitgear.token';
const USER_KEY = 'summitgear.user';

/**
 * Thin wrapper around localStorage. Every access is guarded because
 * storage can be unavailable (private mode, blocked cookies, ...).
 */
function read(key) {
  try {
    return window.localStorage.getItem(key);
  } catch {
    return null;
  }
}

function write(key, value) {
  try {
    if (value === null) {
      window.localStorage.removeItem(key);
    } else {
      window.localStorage.setItem(key, value);
    }
  } catch {
    // Storage unavailable: the session simply won't survive a reload.
  }
}

export const authStorage = {
  getToken() {
    return read(TOKEN_KEY);
  },

  getUser() {
    const raw = read(USER_KEY);
    if (!raw) return null;

    try {
      return JSON.parse(raw);
    } catch {
      return null;
    }
  },

  save(token, user) {
    write(TOKEN_KEY, token);
    write(USER_KEY, JSON.stringify(user));
  },

  clear() {
    write(TOKEN_KEY, null);
    write(USER_KEY, null);
  },
};
