<template>
    <input
        :id="id"
        :type="type"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
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
});

defineEmits(['update:modelValue']);
</script>
