<template>
    <div class="checkin-user-form-data-wrapper">
        <h2 class="checkin-user-form-data-header">{{ form.name }}</h2>
        <FormData :formId="formId" :disable-cancel="records.size === 1" @done="cleanFormRecord" @close="goBack" />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useFormStore } from '@/store/form';
import { storeToRefs } from 'pinia';
import FormData from '@/components/shared/FormData.vue';

const router = useRouter();
const formStore = useFormStore();
const { records } = storeToRefs(formStore);

const props = defineProps({
    formId: Number,
});

const form = computed(() => {
    return formStore.byId(props.formId);
});

const cleanFormRecord = () => {
    formStore.completeUserFormDataRecord(props.formId);
    goBack();
};

const goBack = () => {
    router.push({ name: 'user-forms' });
};

</script>

<style lang="scss">
.checkin-user-form-data-wrapper {
    max-width: 800px;
    margin: 0 auto;

    .checkin-user-form-data-header {
        margin: 0 0 1em 0;
        border-bottom: solid thin var(--color--fieldset-header);
        padding: 0 0 10px 0;
    }

    form.default {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;

        fieldset {
            min-height: 400px;
        }

        footer {
            display: flex;
            flex-direction: row;
            justify-content: center;
        }
    }
}
</style>
