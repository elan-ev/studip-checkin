<template>
    <div class="form-page">
        <FormElementsList :open="openElementsList" @addElement="performAddElement" />
        <FormEditor @requestAddElement="prepareAddingElement" @delete="deleteElementFromPayload" />
        <FormSettings @save="saveForm" @cancel="finishUpAndGoBack" />
    </div>
    <StudipDrawer
        v-if="drawerStore.drawerAttachTarget"
        side="right"
        width="570px"
        :displayOverlay="true"
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

const finishUpAndGoBack = () => {
    formBuilderStore.reset();
    openElementsList.value = false;
    router.push({ name: 'admin' });
};
</script>

<style>
#content-wrapper {
    position: relative !important; /* TODO: Remove later, since the drawer did not work on 6.0.1 I had to add it!!! */
}
.form-page {
    height: 100%;
    width: 100%;
    display: flex;
    flex-direction: row;
    gap: 0.25rem;
    justify-content: space-between;
}
</style>
