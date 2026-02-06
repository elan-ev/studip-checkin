<template>
    <FormData :formId="formId" :formDataId="formDataId" :readOnly="readOnly" />
</template>

<script setup>
    import FormData from '@/components/shared/FormData.vue';
    const props = defineProps({
        formId: {
            type: Number,
            required: true,
        },
        formDataId: Number,
        readOnly: Boolean
    });
</script>

<style>

</style>
