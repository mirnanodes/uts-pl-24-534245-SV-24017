<template>
  <section class="not-found card">
    <h1 class="page-title">404 — Jalur tidak ditemukan</h1>
    <p class="page-subtitle">Sepertinya kamu tersesat. Kembali ke base camp?</p>
    <RouterLink :to="{ name: 'gears.index' }" class="btn btn--primary">Ke daftar alat</RouterLink>
  </section>
</template>

<style scoped>
.not-found {
  max-width: 480px;
  margin: 3rem auto;
  padding: 2rem;
  text-align: center;
}

.not-found .btn {
  margin-top: 1rem;
}
</style>
