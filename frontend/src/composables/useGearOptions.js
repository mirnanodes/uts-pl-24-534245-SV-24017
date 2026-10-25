import { ref } from 'vue';
import { gearService } from '@/services/gearService';

// Options rarely change, so the request is shared and cached per page load.
let pending = null;

export function useGearOptions() {
  const categories = ref([]);
  const conditions = ref([]);

  async function load() {
    pending ??= gearService.options().catch((error) => {
      pending = null;
      throw error;
    });

    const options = await pending;
    categories.value = options.categories;
    conditions.value = options.conditions;
  }

  return { categories, conditions, load };
}
