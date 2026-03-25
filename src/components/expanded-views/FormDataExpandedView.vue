<template>
    <FormData
        :formId="formId"
        :formDataId="formDataId"
        :readOnly="readOnly"
        @done="cleanFormRecord"
        @close="closeDrawer"
    />
</template>

<script setup>
import FormData from '@/components/shared/FormData.vue';
import { useDrawerStore } from '@/store/drawer';
import { getCurrentInstance } from 'vue';
import { useFormStore } from '@/store/form';

const { proxy } = getCurrentInstance();
const formStore = useFormStore();
const drawerStore = useDrawerStore();

const props = defineProps({
    formId: {
        type: Number,
        required: true,
    },
    formDataId: Number,
    readOnly: Boolean,
});

const cleanFormRecord = () => {
    if (drawerStore.drawerContext.value === 'profile') {
        formStore.completeUserFormDataRecord(props.formId);
    }

    closeDrawer();
};

const closeDrawer = () => {
    drawerStore.closeDrawer();
};
</script>
