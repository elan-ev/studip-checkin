<template>
    <RouterLink :to="{ path: '/' }" class="link accept">
        <button class="button">{{ $gettext('Zurück zur Übersicht') }}</button>
    </RouterLink>
    <FormData :formId="formId" @done="cleanFormRecord" @close="goBack" />
</template>

<script setup>
import { getCurrentInstance } from 'vue';
import { useRouter } from 'vue-router';
import { useFormStore } from '@/store/form';
import FormData from '@/components/shared/FormData.vue';

const { proxy } = getCurrentInstance();
const router = useRouter();
const formStore = useFormStore();

const props = defineProps({
    formId: Number,
});

const cleanFormRecord = () => {
    formStore.completeUserFormDataRecord(props.formId);
    STUDIP.Report.success(proxy.$gettext('Das Formular wurde erfolgreich gespeichert'));
    goBack();
};

const goBack = () => {
    router.push({ name: 'user-forms' });
};
</script>