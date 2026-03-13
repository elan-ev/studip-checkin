<template>
    <CheckinHeader>
        <template #title>
            {{ isNew ? $gettext('Neues Formular') : form.name }}
        </template>
    </CheckinHeader>
    <div class="form-page" ref="formPage">
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
import { onBeforeUnmount, onMounted, onUnmounted, watch, ref, getCurrentInstance } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useFormStore } from '@/store/form';
import { useFormBuilderStore } from '@/store/form-builder';
import { useDrawerStore } from '@/store/drawer';
import { useUserFilterStore } from '@/store/user-filter';
import FormElementsList from '@/components/admin/FormElementsList.vue';
import FormEditor from '@/components/admin/FormEditor.vue';
import FormSettings from '@/components/admin/FormSettings.vue';
import CheckinHeader from '@/components/admin/CheckinHeader.vue';
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

const formPage = ref(null);

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

onUnmounted(() => {
    window.removeEventListener('resize', updateOffset);
});

onMounted(async () => {
    drawerStore.setDrawerAttachTarget();
    updateOffset();
    window.addEventListener('resize', updateOffset);
});

const updateOffset = () => {
    if (formPage.value) {
        const rect = formPage.value.getBoundingClientRect();
        formPage.value.style.setProperty('--offset-top', `${rect.top}px`);
    }
};

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
    height: calc(100vh - var(--offset-top, 150px) - 20px); 
    width: 100%;
    display: flex;
    flex-direction: row;
    gap: 15px;
    justify-content: space-between;
    overflow: hidden;
    margin-bottom: 20px;
    box-sizing: border-box;
}
</style>
