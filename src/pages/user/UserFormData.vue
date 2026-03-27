<template>
    <div class="checkin-user-form-data-wrapper" ref="formWrapper">
        <h2 class="checkin-user-form-data-header">{{ form.name }}</h2>
        <FormData :formId="formId" @done="cleanFormRecord" @close="goBack" />
    </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue';
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

const formWrapper = ref(null);

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

const updateOffset = () => {
    if (formWrapper.value) {
        const rect = formWrapper.value.getBoundingClientRect();
        formWrapper.value.style.setProperty('--offset-top', `${rect.top}px`);
    }
};

onMounted(async () => {
    updateOffset();
    window.addEventListener('resize', updateOffset);
});
</script>

<style lang="scss">
.checkin-user-form-data-wrapper {
    max-height: calc(100vh - var(--offset-top, 150px) - 90px);
    max-width: 480px;
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
