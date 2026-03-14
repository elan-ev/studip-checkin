<template>
    <div class="form-input-checkbox-container">
        <input
            :id="id"
            type="checkbox"
            :checked="modelValue"
            @change="$emit('update:modelValue', $event.target.checked)"
            :disabled="disabled"
            :required="required"
            class="form-checkbox-field"
        >
    </div>
</template>

<script setup>
defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    id: String,
    disabled: Boolean,
    required: [Boolean, String]
});

defineEmits(['update:modelValue']);
</script>

<style lang="scss" scoped>
.form-input-checkbox-container {
    display: inline-block;
    vertical-align: middle;
    margin-right: 8px;
}

.form-checkbox-field {
    cursor: pointer;
    width: 16px;
    height: 16px;
    accent-color: var(--color--brand-primary);

    &:disabled {
        cursor: not-allowed;
        opacity: 0.5;
    }
}
</style>