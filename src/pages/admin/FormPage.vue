<template>
    <header class="checkin-header">
        <h2 class="checkin-header-content">
            {{ isNew ? $gettext('Neues Formular') : form.name }}
        </h2>
    </header>
    <div class="form-page">
        <FormElementsList :open="openElementsList" @addElement="performAddElement" @close="closeAddElement" />
        <FormEditor @requestAddElement="prepareAddingElement" @delete="deleteElementFromPayload" />
        <FormSettings @save="saveForm" @cancel="finishUpAndGoBack" />
    </div>
    <StudipDrawer
        v-if="drawerStore.drawerAttachTarget"
        side="right"
        width="570px"
        :displayOverlay="true"
        :title="drawerStore.drawerTitle"
        :attachTo="drawerStore.drawerAttachTarget"
        :visible="drawerStore.isDrawerOpen"
        @close="drawerStore.closeDrawer"
    >
        <component :is="drawerStore.drawerComponent" v-bind="drawerStore.drawerProps" />
    </StudipDrawer>
</template>

<script setup>
import { onBeforeUnmount, onMounted, watch, ref, getCurrentInstance } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useFormStore } from '@/store/form';
import { useFormBuilderStore } from '@/store/form-builder';
import { useDrawerStore } from '@/store/drawer';
import { useUserFilterStore } from '@/store/user-filter';
import FormElementsList from '@/components/admin/FormElementsList.vue';
import FormEditor from '@/components/admin/FormEditor.vue';
import FormSettings from '@/components/admin/FormSettings.vue';
import StudipDrawer from '@/components/studip/StudipDrawer.vue';
import { storeToRefs } from 'pinia';

const { proxy } = getCurrentInstance();
const formStore = useFormStore();
const formBuilderStore = useFormBuilderStore();
const drawerStore = useDrawerStore();
const userFilterStore = useUserFilterStore();

const { form } = storeToRefs(formBuilderStore);
const { errors } = storeToRefs(formStore);

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
            await formBuilderStore.initFromExisting(formStore.byId(id));
            if (form.value?.['filter-id']) {
                await userFilterStore.populateAppliedFieldsByFilterId(form.value['filter-id']);
            }
        } else {
            formBuilderStore.initEmpty();
        }
    },
    { immediate: true },
);

onBeforeUnmount(() => {
    formBuilderStore.reset();
    userFilterStore.reset();
});

onMounted(async () => {
    drawerStore.setDrawerAttachTarget();
});

const saveForm = async (formData) => {
    if (props.isNew) {
        // Logic to create a new form
        await formStore.createForm(formData);
    } else {
        // Logic to update the existing form
        await formStore.updateForm(formData);
    }
    if (errors?.value) {
        // Show error!
        console.error(errors.value);
        STUDIP.Report.error(proxy.$gettext('Beim Erstellen des Formulars ist ein Fehler aufgetreten.'));
        return;
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

const closeAddElement = () => {
    openElementsList.value = false;
    formBuilderStore.preparePendingElement(null);
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
    gap: 15px;
    justify-content: space-between;
}
</style>
