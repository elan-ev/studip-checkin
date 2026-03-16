<template>
    <FormsList :forms="forms" />
</template>
<script setup>
import { onMounted, computed } from 'vue';
import FormsList from '../../components/profile/FormsList.vue';

import { useFormStore } from '@/store/form';
const formStore = useFormStore();
const userId = computed(() => {
    return window.STUDIP.USER_ID;
});

const forms = computed(() => {
    return formStore.all;
});

onMounted(async () => {
    await formStore.fetchByUserId(userId.value, true);
});
</script>
