<template>
    <textarea
        :id="id"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :placeholder="placeholder[lang]"
        :disabled="disabled"
        :required="required"
        class="form-textarea-field"
    ></textarea>
</template>

<script setup>
defineProps({
    modelValue: [String, Number],
    id: String,
    placeholder: String,
    disabled: Boolean,
    required: [Boolean, String],
    lang: {
        type: String,
        default: 'de',
    },
});

defineEmits(['update:modelValue']);
</script>
