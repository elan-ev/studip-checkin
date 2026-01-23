<template>
  <RouterLink :to="{ path: '/'}" class="link accept">
      {{ $gettext('Go back') }}
  </RouterLink>
  <div class="form-page">
    <FormElementsList
      :open="openElementsList"
      @addElement="performAddElement"
    />
    <FormEditor
      @requestAddElement="prepareAddingElement"
      @delete="deleteElementFromPayload"
    />
    <FormSettings
      @save="saveForm"
      @cancel="finishUpAndGoBack"
    />
  </div>
</template>

<script setup>
  import { defineProps, onBeforeUnmount, onMounted, watch, ref } from 'vue';
  import { useRoute, useRouter } from 'vue-router';
  import { useFormStore } from '@/store/form';
  import { useFormBuilderStore } from '@/store/form-builder';
  import FormElementsList from '@/components/admin/FormElementsList.vue';
  import FormEditor from '@/components/admin/FormEditor.vue';
  import FormSettings from '@/components/admin/FormSettings.vue';

  const formStore = useFormStore();
  const formBuilderStore = useFormBuilderStore();
  const route = useRoute();
  const router = useRouter();

  const props = defineProps({
    isNew: {
      type: Boolean,
      required: true,
    },
  });

  watch(
    () => route.params.id,
    async (id) => {
      formBuilderStore.reset();

      if (id && !props.isNew) {
        formBuilderStore.initFromExisting(formStore.byId(id));
      } else {
        formBuilderStore.initEmpty();
      }
    },
    { immediate: true }
  );

  onBeforeUnmount(() => {
    formBuilderStore.reset();
  });

  const saveForm = async () => {
    if (props.isNew) {
      // Logic to create a new form
      await formStore.createForm(formBuilderStore.form);
    } else {
      // Logic to update the existing form
      await formStore.updateForm(formBuilderStore.form.id, formBuilderStore.form);
    }
    finishUpAndGoBack();
  };

  const deleteElementFromPayload = (elementIndex) => {
    formBuilderStore.removeElementFromStructure(elementIndex);
  };

  const openElementsList = ref(false);

  const prepareAddingElement = (index) => {
    openElementsList.value = true;
    formBuilderStore.preparePendingElement(index);
  };

  const performAddElement = (element) => {
    formBuilderStore.finishPendingElement(element);
    openElementsList.value = false;
  };

  const finishUpAndGoBack = () => {
    formBuilderStore.reset();
    openElementsList.value = false;
    router.push({ name: 'admin' });
  };

</script>

<style>
  .form-page {
    height: 100%;
    width: 100%;
    display: flex;
    flex-direction: row;
    gap: 0.25rem;
    justify-content: space-between;
  }
</style>
