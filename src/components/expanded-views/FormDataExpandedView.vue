<template>
    <FormData :formId="formId" :formDataId="formDataId" :readOnly="readOnly" @done="cleanFormRecord" @close="closeDrawer"/>
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
        readOnly: Boolean
    });

    const cleanFormRecord = () => {
        formStore.completeUserFormDataRecord(props.formId);
        STUDIP.Report.success(proxy.$gettext('Das Formular wurde erfolgreich gespeichert'));
        closeDrawer();
    }

    const closeDrawer = () => {
        drawerStore.closeDrawer();
    }
</script>

<style>

</style>
