<template>
    <article class="checkin-user-form-data-wrapper">
        <header class="checkin-user-form-data-header">
            <h2>{{ form.name }}</h2>
            <p>{{ form.description }}</p>
        </header>
        <section>
            <FormData :formId="formId" :disable-cancel="records.size === 1" @done="cleanFormRecord" @close="goBack" />
        </section>
    </article>
</template>

<script setup>
import { computed, onMounted } from 'vue';
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

const setOverlay = () => {
    const el = document.getElementById('studip-checkin-app');
    const topBar = document.getElementById('top-bar');
    const footer = document.getElementById('main-footer');
    const padding = 30;

    const diffHeight = topBar.offsetHeight + footer.offsetHeight + 2 * padding;
    const diffWidth = 2 * padding;

    el.style.setProperty('--checkin-overlay-height', `calc( 100vH - ${diffHeight}px)`);
    el.style.setProperty('--checkin-overlay-width', `calc( 100vW - ${diffWidth}px)`);
    el.style.setProperty('--checkin-overlay-padding', ` ${padding}px`);
};

onMounted(() => {
    setOverlay();
});
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
