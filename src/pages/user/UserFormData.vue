<template>
    <router-link :to="{name: 'user-forms'}">
        {{ $gettext('Go Back') }}
    </router-link>
    <FormData :formId="formId"/>
</template>

<script setup>
    import FormData from '@/components/shared/FormData.vue';
    const props = defineProps({
        formId: Number,
    });
</script>

<style>

</style>
