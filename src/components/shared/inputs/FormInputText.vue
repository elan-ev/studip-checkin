<template>
    <input
        :id="id"
        :type="type"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :placeholder="placeholder[lang]"
        :disabled="disabled"
        :required="required"
        :min="min"
        :max="max"
        class="form-input-field"
    />
</template>

<script setup>
defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    id: String,
    type: {
        type: String,
        default: 'text',
    },
    placeholder: String,
    disabled: Boolean,
    required: [Boolean, String],
    lang: {
        type: String,
        default: 'de',
    },
    min: {
        type: Number,
        default: undefined
    },
    max: {
        type: Number,
        default: undefined
    },
});

defineEmits(['update:modelValue']);
</script>
