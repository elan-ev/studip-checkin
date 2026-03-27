<template>
    <div class="form-field-wrapper" :class="`type-${element.type}`">
        <label :for="element.id"  class="form-field-label">
            <span v-if="element.type !== 'checkbox'">
                {{ element.payload?.label[lang] }}
                <span v-if="element.required" class="required-asterisk">*</span>
            </span>

            <component
                :is="inputComponent"
                :id="element.id"
                :model-value="modelValue"
                @update:model-value="$emit('update:modelValue', $event)"
                :element="element"
                :disabled="readOnly"
                :lang="lang"
                v-bind="extraProps"
            />

            <span v-if="element.type === 'checkbox'">
                {{ element.payload?.label[lang] }}
                <span v-if="element.required" class="required-asterisk">*</span>
            </span>
        </label>
        
        <span v-if="error" class="field-error">{{ error }}</span>
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
    error: String
});

defineEmits(['update:modelValue']);

const lang = computed(() => {
    return contextStore.langSelector;
});

const inputComponent = computed(() => {
    const map = {
        'text': FormInputText,
        'email': FormInputText,
        'url': FormInputText,
        'textarea': FormInputTextarea, 
        'select': FormInputSelect,
        'switch': FormInputSwitch,
        'radio': FormInputRadioGroup,
        'multiselect': FormInputCheckboxGroup
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
    .form-field-label span {
        display: block;
        margin-bottom: 1em;
    }
}
</style>