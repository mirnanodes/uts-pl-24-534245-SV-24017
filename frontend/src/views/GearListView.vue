<script setup>
import { onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue';
import AlertMessage from '@/components/AlertMessage.vue';
import PaginationBar from '@/components/PaginationBar.vue';
import { useFlash } from '@/composables/useFlash';
import { useGearOptions } from '@/composables/useGearOptions';
import { gearService } from '@/services/gearService';
import { getErrorMessage } from '@/utils/errors';
import { formatRupiah } from '@/utils/format';

const PER_PAGE = 10;
const SEARCH_DEBOUNCE_MS = 400;

const { show } = useFlash();
const { categories, load: loadOptions } = useGearOptions();

const gears = ref([]);
const meta = ref(null);
const loading = ref(true);
const errorMessage = ref('');
const deletingId = ref(null);
const filters = reactive({ search: '', category: '', page: 1 });

async function fetchGears() {
  loading.value = true;
  errorMessage.value = '';

  try {
    const response = await gearService.list({
      page: filters.page,
      per_page: PER_PAGE,
      search: filters.search || undefined,
      category: filters.category || undefined,
    });

    gears.value = response.data;
    meta.value = response.meta;
  } catch (error) {
    errorMessage.value = getErrorMessage(error, 'Gagal memuat data alat.');
  } finally {
    loading.value = false;
  }
}

// Debounce typing so we don't send a request on every keystroke.
let searchTimer = null;
watch(
  () => filters.search,
  () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
      filters.page = 1;
      fetchGears();
    }, SEARCH_DEBOUNCE_MS);
  },
);

watch(
  () => filters.category,
  () => {
    filters.page = 1;
    fetchGears();
  },
);

function changePage(page) {
  filters.page = page;
  fetchGears();
}

async function handleDelete(gear) {
  if (!window.confirm(`Hapus "${gear.name}"? Tindakan ini tidak bisa dibatalkan.`)) {
    return;
  }

  deletingId.value = gear.id;

  try {
    const response = await gearService.remove(gear.id);
    show(response.message ?? 'Alat berhasil dihapus.', 'success');

    // Step back a page if we just removed the last row of the page.
    if (gears.value.length === 1 && filters.page > 1) {
      filters.page -= 1;
    }

    await fetchGears();
  } catch (error) {
    show(getErrorMessage(error, 'Gagal menghapus alat.'), 'error');
  } finally {
    deletingId.value = null;
  }
}

onMounted(() => {
  fetchGears();
  loadOptions().catch(() => {
    // The category filter is optional; the list still works without it.
  });
});

onBeforeUnmount(() => clearTimeout(searchTimer));
</script>

<template>
  <section>
    <div class="page-header">
      <div>
        <h1 class="page-title">Katalog Alat Pendakian</h1>
        <p class="page-subtitle">Kelola alat yang tersedia untuk disewa.</p>
      </div>
      <RouterLink :to="{ name: 'gears.create' }" class="btn btn--primary">+ Tambah Alat</RouterLink>
    </div>

    <div class="toolbar card">
      <input
        v-model.trim="filters.search"
        type="search"
        class="input toolbar__search"
        placeholder="Cari nama atau merek..."
        aria-label="Cari alat"
      />
      <select v-model="filters.category" class="input toolbar__select" aria-label="Filter kategori">
        <option value="">Semua kategori</option>
        <option v-for="option in categories" :key="option.value" :value="option.value">
          {{ option.label }}
        </option>
      </select>
    </div>

    <AlertMessage v-if="errorMessage" type="error" :message="errorMessage" />

    <div class="card table-wrapper">
      <table class="table">
        <thead>
          <tr>
            <th>Nama Alat</th>
            <th>Kategori</th>
            <th>Merek</th>
            <th class="text-right">Harga / Hari</th>
            <th class="text-right">Stok</th>
            <th>Kondisi</th>
            <th class="text-right">Aksi</th>
          </tr>
        </thead>

        <tbody v-if="loading">
          <tr>
            <td colspan="7" class="table__state">Memuat data...</td>
          </tr>
        </tbody>

        <tbody v-else-if="!gears.length">
          <tr>
            <td colspan="7" class="table__state">
              Belum ada alat yang cocok.
              <RouterLink :to="{ name: 'gears.create' }">Tambah alat pertama</RouterLink>
            </td>
          </tr>
        </tbody>

        <tbody v-else>
          <tr v-for="gear in gears" :key="gear.id">
            <td data-label="Nama">
              <strong>{{ gear.name }}</strong>
              <small v-if="gear.description" class="table__desc">{{ gear.description }}</small>
            </td>
            <td data-label="Kategori">{{ gear.category_label }}</td>
            <td data-label="Merek">{{ gear.brand || '—' }}</td>
            <td data-label="Harga / Hari" class="text-right">{{ formatRupiah(gear.daily_price) }}</td>
            <td data-label="Stok" class="text-right">{{ gear.stock }}</td>
            <td data-label="Kondisi">
              <span :class="['badge', `badge--${gear.condition}`]">{{ gear.condition_label }}</span>
            </td>
            <td data-label="Aksi" class="text-right table__actions">
              <RouterLink :to="{ name: 'gears.edit', params: { id: gear.id } }" class="btn btn--outline btn--sm">
                Ubah
              </RouterLink>
              <button
                type="button"
                class="btn btn--danger btn--sm"
                :disabled="deletingId === gear.id"
                @click="handleDelete(gear)"
              >
                {{ deletingId === gear.id ? 'Menghapus...' : 'Hapus' }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <PaginationBar v-if="meta" :meta="meta" @change="changePage" />
  </section>
</template>

<style scoped>
.toolbar {
  display: flex;
  gap: 0.75rem;
  padding: 0.75rem;
  margin-bottom: 1rem;
}

.toolbar__search {
  flex: 1;
}

.toolbar__select {
  width: 200px;
}

.table__desc {
  display: block;
  color: var(--color-muted);
  font-weight: normal;
  margin-top: 0.15rem;
  max-width: 32ch;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.table__actions {
  white-space: nowrap;
}

.table__actions > * + * {
  margin-left: 0.4rem;
}

.table__state {
  text-align: center;
  padding: 2.5rem 1rem;
  color: var(--color-muted);
}

@media (max-width: 640px) {
  .toolbar {
    flex-direction: column;
  }

  .toolbar__select {
    width: 100%;
  }
}
</style>
