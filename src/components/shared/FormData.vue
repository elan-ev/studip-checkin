<template>
    <form class="default">
        <fieldset>
            <label v-for="(element, index) in form.structure">
                <template v-if="['radio', 'checkbox'].includes(element.type)">
                    <input
                        :id="element.id"
                        :type="element.type"
                        :readonly="readOnly"
                        v-model="formData[element.id]"
                    >
                    {{ element?.payload?.label }}
                </template>
                <template v-else>
                    {{ element?.payload?.label }}
                    <input v-if="element.type !== 'select'"
                        :id="element.id"
                        :type="element.type"
                        :readonly="readOnly"
                        v-model="formData[element.id]"
                    >
                    <select v-else
                        :id="element.id"
                        :readonly="readOnly"
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
        <footer>
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
    import { useFormStore } from '@/store/form';
    import { useFormUserDataStore } from '@/store/form-user-data';

    const formUserDataStore = useFormUserDataStore();
    const formStore = useFormStore();

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

    const form = computed(() => {
        return formStore.byId(props.formId);
    });

    const userData = computed(() => {
        return props.formDataId ? formUserDataStore.byId(props.formDataId).data : {};
    });

    const formData = ref({});

    onMounted(() => {
        form.value.structure.forEach(input => {
            let value = '';
            if (input.type == 'checkbox') {
                value = false;
            }
            formData.value[input.id] = value;
        });
    });

    const saveForm = () => {
        console.log('formData.value', formData.value);
    }

    const cancel = () => {
        alert('GO BACK');
    }
</script>

<style lang="scss">

</style>
