<script setup>
import { reactive, watch } from 'vue';

const props = defineProps({
  initialValues: { type: Object, default: () => ({}) },
  categories: { type: Array, required: true },
  conditions: { type: Array, required: true },
  errors: { type: Object, default: () => ({}) },
  submitting: { type: Boolean, default: false },
  submitLabel: { type: String, default: 'Simpan' },
});

const emit = defineEmits(['submit', 'cancel']);

const emptyForm = () => ({
  name: '',
  category: '',
  brand: '',
  daily_price: null,
  stock: 1,
  condition: 'good',
  description: '',
});

const form = reactive(emptyForm());

// Fill the form when editing (values arrive after the API call resolves).
watch(
  () => props.initialValues,
  (values) => Object.assign(form, emptyForm(), pickFields(values)),
  { immediate: true },
);

function pickFields(values = {}) {
  const fields = Object.keys(emptyForm());

  return Object.fromEntries(
    Object.entries(values).filter(([key, value]) => fields.includes(key) && value !== null),
  );
}

function handleSubmit() {
  emit('submit', {
    ...form,
    brand: form.brand?.trim() || null,
    description: form.description?.trim() || null,
  });
}
</script>

<template>
  <form class="form card" novalidate @submit.prevent="handleSubmit">
    <div class="form__grid">
      <div class="field field--full">
        <label class="field__label" for="name">Nama alat <span class="req">*</span></label>
        <input
          id="name"
          v-model.trim="form.name"
          type="text"
          class="input"
          :class="{ 'input--invalid': errors.name }"
          maxlength="100"
          placeholder="mis. Tenda Dome 4 Orang"
          autocomplete="off"
        />
        <p v-if="errors.name" class="field__error">{{ errors.name }}</p>
      </div>

      <div class="field">
        <label class="field__label" for="category">Kategori <span class="req">*</span></label>
        <select id="category" v-model="form.category" class="input" :class="{ 'input--invalid': errors.category }">
          <option value="" disabled>Pilih kategori</option>
          <option v-for="option in categories" :key="option.value" :value="option.value">
            {{ option.label }}
          </option>
        </select>
        <p v-if="errors.category" class="field__error">{{ errors.category }}</p>
      </div>

      <div class="field">
        <label class="field__label" for="brand">Merek</label>
        <input
          id="brand"
          v-model="form.brand"
          type="text"
          class="input"
          :class="{ 'input--invalid': errors.brand }"
          maxlength="60"
          placeholder="mis. Eiger"
        />
        <p v-if="errors.brand" class="field__error">{{ errors.brand }}</p>
      </div>

      <div class="field">
        <label class="field__label" for="daily_price">Harga sewa / hari (Rp) <span class="req">*</span></label>
        <input
          id="daily_price"
          v-model.number="form.daily_price"
          type="number"
          min="0"
          step="1000"
          class="input"
          :class="{ 'input--invalid': errors.daily_price }"
          placeholder="35000"
        />
        <p v-if="errors.daily_price" class="field__error">{{ errors.daily_price }}</p>
      </div>

      <div class="field">
        <label class="field__label" for="stock">Stok <span class="req">*</span></label>
        <input
          id="stock"
          v-model.number="form.stock"
          type="number"
          min="0"
          step="1"
          class="input"
          :class="{ 'input--invalid': errors.stock }"
        />
        <p v-if="errors.stock" class="field__error">{{ errors.stock }}</p>
      </div>

      <fieldset class="field field--full">
        <legend class="field__label">Kondisi <span class="req">*</span></legend>
        <div class="choices">
          <label v-for="option in conditions" :key="option.value" class="choice">
            <input v-model="form.condition" type="radio" name="condition" :value="option.value" />
            <span :class="['badge', `badge--${option.value}`]">{{ option.label }}</span>
          </label>
        </div>
        <p v-if="errors.condition" class="field__error">{{ errors.condition }}</p>
      </fieldset>

      <div class="field field--full">
        <label class="field__label" for="description">Deskripsi</label>
        <textarea
          id="description"
          v-model="form.description"
          rows="3"
          class="input"
          :class="{ 'input--invalid': errors.description }"
          maxlength="1000"
          placeholder="Catatan kondisi, kelengkapan, dsb."
        />
        <p v-if="errors.description" class="field__error">{{ errors.description }}</p>
      </div>
    </div>

    <div class="form__actions">
      <button type="button" class="btn btn--outline" :disabled="submitting" @click="emit('cancel')">Batal</button>
      <button type="submit" class="btn btn--primary" :disabled="submitting">
        {{ submitting ? 'Menyimpan...' : submitLabel }}
      </button>
    </div>
  </form>
</template>

<style scoped>
.form {
  padding: 1.5rem;
}

.form__grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1.1rem 1.25rem;
}

.field--full {
  grid-column: 1 / -1;
}

fieldset.field {
  border: none;
  padding: 0;
  margin: 0;
}

.req {
  color: var(--color-danger);
}

.choices {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.choice {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  cursor: pointer;
}

.form__actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  margin-top: 1.5rem;
}

@media (max-width: 640px) {
  .form__grid {
    grid-template-columns: 1fr;
  }
}
</style>
