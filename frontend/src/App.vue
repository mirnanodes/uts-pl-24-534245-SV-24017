<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import AppHeader from '@/components/AppHeader.vue';
import AlertMessage from '@/components/AlertMessage.vue';
import { useAuth } from '@/composables/useAuth';
import { useFlash } from '@/composables/useFlash';

const route = useRoute();
const { isAuthenticated } = useAuth();
const { flash, clear } = useFlash();

const showHeader = computed(() => isAuthenticated.value && route.name !== 'login');
</script>

<template>
  <AppHeader v-if="showHeader" />

  <main :class="['app-main', { 'app-main--centered': !showHeader }]">
    <AlertMessage
      v-if="flash.message"
      class="app-flash"
      :type="flash.type"
      :message="flash.message"
      dismissible
      @close="clear"
    />

    <RouterView :key="route.fullPath" />
  </main>
</template>
