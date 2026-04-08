<template>
    <div class="form-field-wrapper" :class="[`type-${element.type}`, { 'has-error': error }]">
        <label :for="element.id" class="form-field-label">
            <span
                v-if="element.type !== 'checkbox'"
                :class="{ required: element.payload?.required === true && !readOnly }"
            >
                {{ element.payload?.label[lang] }}
            </span>
            <component
                :is="inputComponent"
                :id="element.id"
                :model-value="modelValue"
                @update:modelValue="$emit('update:modelValue', $event)"
                :element="element"
                :disabled="readOnly"
                :lang="lang"
                v-bind="extraProps"
            />

            <span
                v-if="element.type === 'checkbox'"
                :style="{ required: element.payload?.required === true && !readOnly }"
            >
                {{ element.payload?.label[lang] }}
            </span>
        </label>
        <span v-if="error" :id="`${element.id}-error`" class="field-error" aria-live="polite">{{ error }}</span>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import FormInputText from './inputs/FormInputText.vue';
import FormInputTextarea from './inputs/FormInputTextarea.vue';
import FormInputSelect from './inputs/FormInputSelect.vue';
import FormInputSwitch from './inputs/FormInputSwitch.vue';
import FormInputRadioGroup from './inputs/FormInputRadioGroup.vue';
import FormInputCheckboxGroup from './inputs/FormInputCheckboxGroup.vue';

import { useContextStore } from '@/store/context';

const contextStore = useContextStore();

const props = defineProps({
    element: Object,
    modelValue: [String, Number, Boolean, Array],
    readOnly: Boolean,
    error: String,
});

defineEmits(['update:modelValue']);

const lang = computed(() => {
    return contextStore.langSelector;
});

const inputComponent = computed(() => {
    const map = {
        text: FormInputText,
        email: FormInputText,
        url: FormInputText,
        textarea: FormInputTextarea,
        select: FormInputSelect,
        switch: FormInputSwitch,
        radio: FormInputRadioGroup,
        multiselect: FormInputCheckboxGroup,
    };
    return map[props.element.type] || FormInputText;
});

const extraProps = computed(() => {
    return {
        type: props.element.type,
        placeholder: props.element.payload?.placeholder,
        required: props.element.required,
        min: props.element.payload?.min,
        max: props.element.payload?.max,
    };
});
</script>

<style lang="scss">
.form-field-wrapper {
    margin-bottom: 1.5em;
    .form-field-label span {
        display: block;
        margin-bottom: 1em;
    }
    &.has-error {
        .form-field-label span,
        .field-error {
            color: var(--color--warning);
            text-indent: 0.25ex;
        }
        input[type='url'],
        input[type='email'],
        input[type='number'] {
            border-color: var(--color--warning);

            &:focus {
                outline-color: var(--color--warning);
                box-shadow: 0 0 0 3px rgba(214, 0, 0, 0.2);
            }
        }
    }
}
</style>
