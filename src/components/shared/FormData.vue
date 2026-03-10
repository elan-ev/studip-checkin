<template>
    <form class="default" v-if="form">
        <fieldset>
            <!-- TODO: Apply Input Relationships when it is ready -->
            <label v-for="(element, index) in form.structure" :key="index">
                <template v-if="['radio', 'checkbox'].includes(element.type)">
                    <template v-if="element.type === 'checkbox'">
                        <input
                            :id="element.id"
                            :type="element.type"
                            :readonly="readOnly"
                            :disabled="readOnly"
                            :required="element?.required ?? false"
                            :true-value="true"
                            :false-value="false"
                            v-model="formData[element.id]"
                        >
                        {{ element?.payload?.label }}
                    </template>
                    <template v-else>
                        <span>
                            {{ element?.payload?.label }}
                        </span>
                        <template v-for="(option, rIndex) in element.payload.options" :key="rIndex">
                            <input
                                :id="element.id"
                                :name="element.id"
                                :type="element.type"
                                :readonly="readOnly"
                                :disabled="readOnly"
                                :required="element?.required ?? false"
                                v-model="formData[element.id]"
                                :value="rIndex"
                                :checked="formData[element.id] == rIndex"
                            >
                            {{ option.text }}
                        </template>
                    </template>
                </template>
                <template v-else>
                    {{ element?.payload?.label }}
                    <input v-if="element.type !== 'select'"
                        :id="element.id"
                        :type="element.type"
                        :readonly="readOnly"
                        :disabled="readOnly"
                        :placeholder="element.payload?.placeholder"
                        :required="element?.required ?? false"
                        v-model="formData[element.id]"
                    >
                    <select v-else
                        :id="element.id"
                        :readonly="readOnly"
                        :disabled="readOnly"
                        :required="element?.required ?? false"
                        v-model="formData[element.id]"
                    >
                        <option v-for="(op, index) in element?.payload?.options"
                            :value="index"
                        >
                            {{ $gettext(op.text) }}
                        </option>
                    </select>
                </template>
            </label>
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
    import { ref, computed, onMounted, getCurrentInstance } from 'vue';
    import { useFormStore } from '@/store/form';
    import { useFormUserDataStore } from '@/store/form-user-data';
    import { storeToRefs } from 'pinia';

    const { proxy } = getCurrentInstance();
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

    const emit = defineEmits(['done', 'close']);

    const form = computed(() => {
        return formStore.byId(props.formId);
    });

    const userData = computed(() => {
        return props.formDataId ? formUserDataStore.byId(props.formDataId).data : {};
    });

    const formData = ref({});

    onMounted(() => {
        form.value.structure.forEach(input => {
            let value = null;

            if (Object.keys(userData.value).includes(input.id)) {
                value = userData.value[input.id];
            }

            formData.value[input.id] = value;
        });
    });

    const validateFormData = () => {
        // TODO: check the relationships, as soon as it is implemented in FormInputs.

        // we check required elements.
        let invalidRequired = form.value.structure.some((input, index) => {
            return input.required &&
                !formData.value?.[input.id] ||
                formData.value?.[input.id] === null;
        });

        return !invalidRequired;
    }

    const saveForm = async () => {
        if (!validateFormData()) {
            STUDIP.Report.error(proxy.$gettext('Etwas fehlt!'));
        }
        const payload = {
            'form-id': form.value.id,
            'form-version': form.value.version,
            'form-data': formData.value,
        }
        await formUserDataStore.createRecord(payload);
        if (errors?.value) {
        // Show error!
            console.error(errors.value);
            STUDIP.Report.error(proxy.$gettext('Das Formular konnte nicht gespeichert werden!'));
            return;
        }
        emit('done');
    }

    const cancel = () => {
        emit('close');
    }
</script>