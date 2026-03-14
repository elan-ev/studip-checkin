<template>
    <div class="form-field-wrapper" :class="`type-${element.type}`">
        <label :for="element.id">
            <span v-if="element.type !== 'checkbox'">
                {{ element.payload?.label }}
                <span v-if="element.required" class="required-asterisk">*</span>
            </span>

            <component
                :is="inputComponent"
                :id="element.id"
                :model-value="modelValue"
                @update:model-value="$emit('update:modelValue', $event)"
                :element="element"
                :disabled="readOnly"
                v-bind="extraProps"
            />

            <span v-if="element.type === 'checkbox'">
                {{ element.payload?.label }}
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
import FormInputCheckbox from './inputs/FormInputCheckbox.vue';
import FormInputRadioGroup from './inputs/FormInputRadioGroup.vue';

const props = defineProps({
    element: Object,
    modelValue: [String, Number, Boolean, Array],
    readOnly: Boolean,
    error: String
});

defineEmits(['update:modelValue']);

const inputComponent = computed(() => {
    const map = {
        'text': FormInputText,
        'email': FormInputText,
        'url': FormInputText,
        'textarea': FormInputTextarea, 
        'select': FormInputSelect,
        'checkbox': FormInputCheckbox,
        'radio': FormInputRadioGroup
    };
    return map[props.element.type] || FormInputText;
});

const extraProps = computed(() => {
    return {
        type: props.element.type,
        placeholder: props.element.payload?.placeholder,
        required: props.element.required
    };
});
</script>