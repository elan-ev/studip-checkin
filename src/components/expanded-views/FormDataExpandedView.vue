<template>
    <p class="checkin-drawer-form-description">{{ form.description[lang] }}</p>
    <FormData
        :formId="formId"
        :formDataId="formDataId"
        :readOnly="readOnly"
        :inProfile="inProfile"
        @edit="editForm"
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

const inProfile = computed(() => {
    return drawerStore.drawerContext.value === 'profile';
});

const cleanFormRecord = () => {
    if (inProfile.value) {
        formStore.completeUserFormDataRecord(props.formId);
    }

    closeDrawer();
};

const closeDrawer = () => {
    drawerStore.closeDrawer();
};

const editForm = () => {
    drawerStore.openFormDataInDrawer(props.formId, props.formDataId, false, form.value.name[lang.value]);
}
</script>
<style lang="scss">
.checkin-drawer-form-description {
    margin-bottom: 30px;
}
</style>
