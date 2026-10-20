<script setup>
import { computed } from 'vue';

const props = defineProps({
  meta: { type: Object, required: true },
});

const emit = defineEmits(['change']);

const hasPrevious = computed(() => props.meta.current_page > 1);
const hasNext = computed(() => props.meta.current_page < props.meta.last_page);

function go(page) {
  emit('change', page);
}
</script>

<template>
  <nav v-if="meta.last_page > 1" class="pagination" aria-label="Navigasi halaman">
    <button type="button" class="btn btn--outline btn--sm" :disabled="!hasPrevious" @click="go(meta.current_page - 1)">
      &larr; Sebelumnya
    </button>

    <span class="pagination__info">
      Halaman {{ meta.current_page }} dari {{ meta.last_page }}
      <small>({{ meta.total }} alat)</small>
    </span>

    <button type="button" class="btn btn--outline btn--sm" :disabled="!hasNext" @click="go(meta.current_page + 1)">
      Berikutnya &rarr;
    </button>
  </nav>
</template>

<style scoped>
.pagination {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-top: 1.25rem;
}

.pagination__info {
  font-size: 0.9rem;
  color: var(--color-muted);
  text-align: center;
}
</style>
