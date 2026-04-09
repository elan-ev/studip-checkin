<template>
    <form v-if="form" class="default">
        <div class="sr-only" role="alert" aria-live="assertive">
            <p v-if="Object.keys(fieldErrors).length > 0">
                {{ $gettext('Das Formular enthält Fehler. Bitte überprüfen Sie die folgenden Felder:') }}
                <template v-for="(error, id) in fieldErrors" :key="id"> {{ getLabelForId(id) }}: {{ error }} </template>
            </p>
        </div>
        <fieldset class="undecorated">
            <template v-for="element in form.structure" :key="element.id">
                <FormField
                    v-show="isVisible(element)"
                    :element="element"
                    v-model="formData[element.id]"
                    :read-only="readOnly"
                    :error="fieldErrors[element.id]"
                />
            </template>
        </fieldset>
        <footer>
            <template v-if="!readOnly">
                <button class="button accept" @click.prevent="saveForm">
                    {{ $gettext('Speichern') }}
                </button>

                <button v-if="!disableCancel" class="button cancel" @click.prevent="cancel">
                    {{ $gettext('Abbrechen') }}
                </button>
            </template>
            <template v-else>
                <button class="button edit" @click.prevent="editForm">
                    {{ $gettext('Bearbeiten') }}
                </button>
            </template>
        </footer>
    </form>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import { useGettext } from 'vue3-gettext';
import { useFormStore } from '@/store/form';
import { useFormUserDataStore } from '@/store/form-user-data';
import { useContextStore } from '@/store/context';
import { storeToRefs } from 'pinia';
import FormField from '@/components/shared/FormField.vue';
import useValidation from '@/composables/useValidation';

const { validateField } = useValidation();
const { $gettext } = useGettext();
const formStore = useFormStore();
const formUserDataStore = useFormUserDataStore();
const contextStore = useContextStore();
const { errors } = storeToRefs(formUserDataStore);

const fieldErrors = ref({});

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
    disableCancel: {
        type: Boolean,
        default: false,
    },
    inProfile: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['done', 'close', 'edit']);

const lang = computed(() => {
    return contextStore.langSelector;
});

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
    let isValid = true;
    fieldErrors.value = {};

    form.value.structure.forEach((element) => {
        const visible = isVisible(element);
        const error = validateField(element, formData.value[element.id], visible);

        if (error) {
            fieldErrors.value[element.id] = error;
            isValid = false;
        }
    });

    return isValid;
};

const usersFormData = computed(() => {
    return formUserDataStore.getByFormId(form.value.id) ?? null;
});

const hasFormData = computed(() => {
    return usersFormData.value !== null;
});

const saveForm = async () => {
    if (!validateFormData()) {
        STUDIP.Report.warning($gettext('Bitte überprüfen Sie Ihre Eingaben.'));
        nextTick(() => {
            const firstErrorId = Object.keys(fieldErrors.value)[0];
            const element = document.getElementById(firstErrorId);
            if (element) {
                element.focus();
            }
        });
        return;
    }
    const payload = {
        id: usersFormData.value?.id ?? null,
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
        console.error(errors.value);
        STUDIP.Report.error($gettext('Das Formular konnte nicht gespeichert werden!'));
        return;
    } else {
        STUDIP.Report.success($gettext('Das Formular wurde erfolgreich gespeichert'));
    }
};

const getLabelForId = (id) => {
    const element = form.value.structure.find((el) => el.id === id);

    if (element && element.payload && element.payload.label) {
        return element.payload.label[lang.value] || id;
    }

    return id;
};

const cancel = () => {
    emit('close');
};

const editForm = () => {
    emit('edit');
};
</script>
