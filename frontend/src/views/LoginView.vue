<script setup>
import { reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AlertMessage from '@/components/AlertMessage.vue';
import { env } from '@/config/env';
import { useAuth } from '@/composables/useAuth';
import { useFlash } from '@/composables/useFlash';
import { getErrorMessage, getValidationErrors } from '@/utils/errors';

const route = useRoute();
const router = useRouter();
const { login } = useAuth();
const { show, clear } = useFlash();

const form = reactive({ email: '', password: '' });
const errors = ref({});
const errorMessage = ref('');
const submitting = ref(false);

async function handleSubmit() {
  submitting.value = true;
  errors.value = {};
  errorMessage.value = '';

  try {
    await login(form);
    clear();
    show('Login berhasil. Selamat datang!', 'success');

    const redirect = typeof route.query.redirect === 'string' ? route.query.redirect : null;
    // Only allow internal redirects (prevents open-redirect).
    router.replace(redirect?.startsWith('/') && !redirect.startsWith('//') ? redirect : { name: 'gears.index' });
  } catch (error) {
    errors.value = getValidationErrors(error);
    if (!Object.keys(errors.value).length) {
      errorMessage.value = getErrorMessage(error, 'Login gagal.');
    }
  } finally {
    submitting.value = false;
  }
}
</script>

<template>
  <section class="login">
    <div class="login__hero" aria-hidden="true">
      <svg viewBox="0 0 400 160" class="login__mountains">
        <path d="M0 160 110 40l55 70 45-45 70 50 50-35 70 80z" fill="currentColor" opacity="0.35" />
        <path d="M0 160 90 80l60 45 70-75 90 110z" fill="currentColor" />
        <path d="m220 50 14 17-14-6-14 6z" fill="#e0893a" />
      </svg>
    </div>

    <div class="login__card card">
      <h1 class="login__title">{{ env.appName }}</h1>
      <p class="login__subtitle">Masuk untuk mengelola katalog alat pendakian.</p>

      <AlertMessage v-if="errorMessage" type="error" :message="errorMessage" class="login__alert" />

      <form novalidate @submit.prevent="handleSubmit">
        <div class="field">
          <label class="field__label" for="email">Email</label>
          <input
            id="email"
            v-model.trim="form.email"
            type="email"
            class="input"
            :class="{ 'input--invalid': errors.email }"
            autocomplete="username"
            placeholder="admin@summitgear.test"
            required
          />
          <p v-if="errors.email" class="field__error">{{ errors.email }}</p>
        </div>

        <div class="field">
          <label class="field__label" for="password">Password</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            class="input"
            :class="{ 'input--invalid': errors.password }"
            autocomplete="current-password"
            required
          />
          <p v-if="errors.password" class="field__error">{{ errors.password }}</p>
        </div>

        <button type="submit" class="btn btn--primary btn--block" :disabled="submitting">
          {{ submitting ? 'Memproses...' : 'Login' }}
        </button>
      </form>
    </div>
  </section>
</template>

<style scoped>
.login {
  width: 100%;
  max-width: 420px;
  margin: 0 auto;
}

.login__hero {
  color: var(--color-forest);
  margin-bottom: -1px;
}

.login__mountains {
  display: block;
  width: 100%;
  height: auto;
}

.login__card {
  padding: 2rem;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}

.login__title {
  margin: 0;
  font-size: 1.6rem;
  color: var(--color-forest-dark);
}

.login__subtitle {
  margin: 0.25rem 0 1.5rem;
  color: var(--color-muted);
}

.login__alert {
  margin-bottom: 1rem;
}

.field + .field {
  margin-top: 1rem;
}

.btn--block {
  margin-top: 1.5rem;
}
</style>
