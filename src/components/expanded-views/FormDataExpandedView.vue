<template>
    <p class="checkin-drawer-form-description">{{ form.description[lang] }}</p>
    <FormData
        :formId="formId"
        :formDataId="formDataId"
        :readOnly="readOnly"
        @done="cleanFormRecord"
        @close="closeDrawer"
    />
</template>

<script setup>
import { computed } from 'vue';
import FormData from '@/components/shared/FormData.vue';
import { useDrawerStore } from '@/store/drawer';
import { useFormStore } from '@/store/form';
import { useContextStore } from '@/store/context';

const formStore = useFormStore();
const drawerStore = useDrawerStore();
const contextStore = useContextStore();

const props = defineProps({
    formId: {
        type: Number,
        required: true,
    },
    formDataId: Number,
    readOnly: Boolean,
});

const form = computed(() => {
    return formStore.byId(props.formId);
});

const lang = computed(() => {
    return contextStore.langSelector;
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
<style lang="scss">
.checkin-drawer-form-description {
    margin-bottom: 30px;
}
</style>
