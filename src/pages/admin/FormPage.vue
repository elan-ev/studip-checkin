<template>
  <div>
    <RouterLink :to="{ path: '/'}" class="button accept">
        {{ $gettext('Go back') }}
    </RouterLink>
  </div>
</template>

<script setup>
  import { defineProps, ref } from 'vue';
  import { useRoute } from 'vue-router';
  import { useFormStore } from '@/store/form';

  const formStore = useFormStore();
  const route = useRoute();

  const props = defineProps({
    isNew: {
      type: Boolean,
      required: true,
    },
  });
  const form = ref({});

  if (!props.isNew) {
    // Fetch the form data for editing
    const formId = route.params.id;
    form.value = formStore.byId(formId);
  }

  const saveForm = async () => {
    if (props.isNew) {
      // Logic to create a new form
      await formStore.createForm(form.value);
    } else {
      // Logic to update the existing form
      await formStore.updateForm(form.value.id, form.value);
    }
  };
</script>

<style>

</style>
