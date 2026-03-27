<template>
    <form v-if="form" class="default">
        <fieldset class="undecorated">
            <template v-for="element in form.structure" :key="element.id">
                <FormField
                    v-show="isVisible(element)"
                    :element="element"
                    v-model="formData[element.id]"
                    :read-only="readOnly"
                />
            </template>
        </fieldset>
        <footer v-if="!readOnly">
            <button class="button accept" @click.prevent="saveForm">
                {{ $gettext('Speichern') }}
            </button>

            <button class="button cancel" @click.prevent="cancel">
                {{ $gettext('Abbrechen') }}
            </button>
        </footer>
    </form>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useGettext } from 'vue3-gettext';
import { useFormStore } from '@/store/form';
import { useFormUserDataStore } from '@/store/form-user-data';
import { storeToRefs } from 'pinia';

import FormField from '@/components/shared/FormField.vue';

const { $gettext } = useGettext();
const formStore = useFormStore();
const formUserDataStore = useFormUserDataStore();
const { errors } = storeToRefs(formUserDataStore);

const props = defineProps({
    formId: {
        type: Number,
        required: true,
    },
    formDataId: Number,
    readOnly: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['done','close']);

const form = computed(() => {
    return formStore.byId(props.formId);
});

const userData = computed(() => {
    return props.formDataId ? formUserDataStore.byId(props.formDataId).data : {};
});

const formData = ref({});

onMounted(() => {
    form.value.structure.forEach((input) => {
        let value = null;

        if (Object.keys(userData.value).includes(input.id)) {
            value = userData.value[input.id];
        }

        formData.value[input.id] = value;
    });
});

const isVisible = (element) => {
    if (!element.payload?.['has-conditions'] || !element.payload?.['condition-target']) {
        return true;
    }

    const targetId = element.payload['condition-target'];
    const requiredValue = element.payload['condition-value'];
    const actualValue = formData.value[targetId];

    if (
        actualValue === undefined ||
        actualValue === null ||
        actualValue === '' ||
        (Array.isArray(actualValue) && actualValue.length === 0)
    ) {
        return false;
    }

    if (Array.isArray(requiredValue)) {
        if (Array.isArray(actualValue)) {
            return actualValue.some((val) => requiredValue.includes(val));
        }
        return requiredValue.includes(actualValue);
    }

    if (Array.isArray(actualValue)) {
        return actualValue.includes(requiredValue);
    }

    return String(actualValue) === String(requiredValue);
};

const validateFormData = () => {
    // TODO: check the relationships, as soon as it is implemented in FormInputs.

    // we check required elements.
    let invalidRequired = form.value.structure.some((input, index) => {
        return (input.required && !formData.value?.[input.id]) || formData.value?.[input.id] === null;
    });

    return !invalidRequired;
};

const usersFormData = computed(() => {
    return formUserDataStore.getByFormId(form.value.id) ?? null;
});
const hasFormData = computed(() => {
    return usersFormData.value !== null;
});

const saveForm = async () => {
    if (!validateFormData()) {
        STUDIP.Report.error($gettext('Etwas fehlt!'));
    }
    const payload = {
        'id': usersFormData.value?.id ?? null,
        'form-id': form.value.id,
        'form-version': form.value.version,
        'form-data': formData.value,
    };
    if (hasFormData.value) {
        await formUserDataStore.updateRecord(usersFormData.value.id, payload);
        emit('close');
    } else {
        await formUserDataStore.createRecord(payload);
        emit('done');
    }
    if (errors?.value) {
        // Show error!
        console.error(errors.value);
        STUDIP.Report.error($gettext('Das Formular konnte nicht gespeichert werden!'));
        return;
    } else {
        STUDIP.Report.success($gettext('Das Formular wurde erfolgreich gespeichert'));
    }
};

const cancel = () => {
    emit('close');
};
</script>
