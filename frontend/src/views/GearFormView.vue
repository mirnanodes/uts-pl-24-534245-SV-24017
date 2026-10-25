<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import AlertMessage from '@/components/AlertMessage.vue';
import GearForm from '@/components/GearForm.vue';
import { useFlash } from '@/composables/useFlash';
import { useGearOptions } from '@/composables/useGearOptions';
import { gearService } from '@/services/gearService';
import { getErrorMessage, getValidationErrors } from '@/utils/errors';

// `id` comes from the route (/gears/:id/edit); absent on /gears/create.
const props = defineProps({
  id: { type: String, default: null },
});

const router = useRouter();
const { show } = useFlash();
const { categories, conditions, load: loadOptions } = useGearOptions();

const isEdit = computed(() => Boolean(props.id));
const gear = ref({});
const loading = ref(true);
const loadFailed = ref(false);
const submitting = ref(false);
const errors = ref({});
const errorMessage = ref('');

async function loadPage() {
  loading.value = true;
  loadFailed.value = false;
  errorMessage.value = '';

  try {
    const [, existing] = await Promise.all([
      loadOptions(),
      isEdit.value ? gearService.find(props.id) : Promise.resolve({}),
    ]);

    gear.value = existing;
  } catch (error) {
    loadFailed.value = true;
    errorMessage.value = getErrorMessage(error, 'Gagal memuat data.');
  } finally {
    loading.value = false;
  }
}

async function handleSubmit(payload) {
  submitting.value = true;
  errors.value = {};
  errorMessage.value = '';

  try {
    const response = isEdit.value
      ? await gearService.update(props.id, payload)
      : await gearService.create(payload);

    show(response.message, 'success');
    router.push({ name: 'gears.index' });
  } catch (error) {
    errors.value = getValidationErrors(error);
    errorMessage.value = Object.keys(errors.value).length
      ? 'Periksa kembali isian yang ditandai.'
      : getErrorMessage(error, 'Gagal menyimpan data.');
  } finally {
    submitting.value = false;
  }
}

onMounted(loadPage);
</script>

<template>
  <section class="form-page">
    <div class="page-header">
      <div>
        <RouterLink :to="{ name: 'gears.index' }" class="back-link">&larr; Kembali ke daftar</RouterLink>
        <h1 class="page-title">{{ isEdit ? 'Ubah Alat' : 'Tambah Alat' }}</h1>
        <p class="page-subtitle">
          {{ isEdit ? 'Perbarui informasi alat pendakian.' : 'Daftarkan alat baru ke katalog sewa.' }}
        </p>
      </div>
    </div>

    <AlertMessage v-if="errorMessage" type="error" :message="errorMessage" class="form-page__alert" />

    <div v-if="loading" class="card form-page__loading">Memuat formulir...</div>

    <div v-else-if="loadFailed" class="card form-page__loading">
      <button type="button" class="btn btn--outline" @click="loadPage">Coba lagi</button>
    </div>

    <GearForm
      v-else
      :initial-values="gear"
      :categories="categories"
      :conditions="conditions"
      :errors="errors"
      :submitting="submitting"
      :submit-label="isEdit ? 'Simpan Perubahan' : 'Tambah Alat'"
      @submit="handleSubmit"
      @cancel="router.push({ name: 'gears.index' })"
    />
  </section>
</template>

<style scoped>
.form-page {
  max-width: 760px;
}

.back-link {
  display: inline-block;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
}

.form-page__alert {
  margin-bottom: 1rem;
}

.form-page__loading {
  padding: 2rem;
  text-align: center;
  color: var(--color-muted);
}
</style>
