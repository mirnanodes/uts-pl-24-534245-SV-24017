<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { env } from '@/config/env';
import { useAuth } from '@/composables/useAuth';
import { useFlash } from '@/composables/useFlash';

const router = useRouter();
const { user, logout } = useAuth();
const { show } = useFlash();
const loggingOut = ref(false);

async function handleLogout() {
  loggingOut.value = true;

  try {
    await logout();
    show('Anda telah logout.', 'success');
  } finally {
    loggingOut.value = false;
    router.replace({ name: 'login' });
  }
}
</script>

<template>
  <header class="header">
    <div class="header__inner">
      <RouterLink :to="{ name: 'gears.index' }" class="brand">
        <svg class="brand__logo" viewBox="0 0 32 32" aria-hidden="true">
          <rect width="32" height="32" rx="8" fill="currentColor" />
          <path d="M5 24 13 11l4 6 3-4 7 11z" fill="#f4efe4" />
          <path d="m13 11 2.2 3.4-2.2-1-2.2 1z" fill="#e0893a" />
        </svg>
        <span class="brand__name">{{ env.appName }}</span>
      </RouterLink>

      <nav class="nav" aria-label="Navigasi utama">
        <RouterLink :to="{ name: 'gears.index' }" class="nav__link">Daftar Alat</RouterLink>
        <RouterLink :to="{ name: 'gears.create' }" class="nav__link">Tambah Alat</RouterLink>
      </nav>

      <div class="header__user">
        <span class="header__name" :title="user?.email">{{ user?.name }}</span>
        <button type="button" class="btn btn--ghost btn--sm" :disabled="loggingOut" @click="handleLogout">
          {{ loggingOut ? 'Keluar...' : 'Logout' }}
        </button>
      </div>
    </div>
  </header>
</template>

<style scoped>
.header {
  background: var(--color-forest);
  color: var(--color-on-forest);
  position: sticky;
  top: 0;
  z-index: 10;
}

.header__inner {
  max-width: var(--max-width);
  margin: 0 auto;
  padding: 0.75rem 1rem;
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.brand {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  color: inherit;
  text-decoration: none;
  font-weight: 700;
  letter-spacing: 0.02em;
}

.brand__logo {
  width: 32px;
  height: 32px;
  color: var(--color-forest-dark);
}

.nav {
  display: flex;
  gap: 0.25rem;
  flex: 1;
}

.nav__link {
  color: inherit;
  text-decoration: none;
  padding: 0.4rem 0.75rem;
  border-radius: var(--radius-sm);
  opacity: 0.8;
}

.nav__link:hover,
.nav__link.router-link-exact-active {
  opacity: 1;
  background: rgb(255 255 255 / 0.12);
}

.header__user {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.header__name {
  font-size: 0.9rem;
  opacity: 0.9;
}

.btn--ghost {
  color: var(--color-on-forest);
  border-color: rgb(255 255 255 / 0.35);
}

@media (max-width: 640px) {
  .header__inner {
    flex-wrap: wrap;
    gap: 0.75rem;
  }

  .nav {
    order: 3;
    flex-basis: 100%;
  }

  .header__user {
    margin-left: auto;
  }

  .header__name {
    display: none;
  }
}
</style>
