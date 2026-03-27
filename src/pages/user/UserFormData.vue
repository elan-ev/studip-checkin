<template>
    <div>
        <h2>{{ form.name }}</h2>
        <FormData :formId="formId" @done="cleanFormRecord" @close="goBack" />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useGettext } from 'vue3-gettext';
import { useRouter } from 'vue-router';
import { useFormStore } from '@/store/form';
import FormData from '@/components/shared/FormData.vue';

const { $gettext } = useGettext();
const router = useRouter();
const formStore = useFormStore();

const props = defineProps({
    formId: Number,
});

const form = computed(() => {
    return formStore.byId(props.formId);
});

const cleanFormRecord = () => {
    formStore.completeUserFormDataRecord(props.formId);
    STUDIP.Report.success($gettext('Das Formular wurde erfolgreich gespeichert'));
    goBack();
};

const goBack = () => {
    router.push({ name: 'user-forms' });
};
</script>