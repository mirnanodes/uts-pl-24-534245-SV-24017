<script setup>
defineProps({
  message: { type: String, required: true },
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value),
  },
  dismissible: { type: Boolean, default: false },
});

defineEmits(['close']);
</script>

<template>
  <div :class="['alert', `alert--${type}`]" role="alert">
    <span class="alert__message">{{ message }}</span>
    <button
      v-if="dismissible"
      type="button"
      class="alert__close"
      aria-label="Tutup pesan"
      @click="$emit('close')"
    >
      &times;
    </button>
  </div>
</template>

<style scoped>
.alert {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  border-radius: var(--radius);
  border: 1px solid transparent;
  font-size: 0.95rem;
}

.alert__message {
  flex: 1;
}

.alert__close {
  background: none;
  border: none;
  font-size: 1.25rem;
  line-height: 1;
  cursor: pointer;
  color: inherit;
  opacity: 0.7;
}

.alert__close:hover {
  opacity: 1;
}

.alert--success {
  background: var(--color-success-bg);
  border-color: var(--color-success-border);
  color: var(--color-success);
}

.alert--error {
  background: var(--color-danger-bg);
  border-color: var(--color-danger-border);
  color: var(--color-danger);
}

.alert--warning {
  background: var(--color-warning-bg);
  border-color: var(--color-warning-border);
  color: var(--color-warning);
}

.alert--info {
  background: var(--color-info-bg);
  border-color: var(--color-info-border);
  color: var(--color-info);
}
</style>
