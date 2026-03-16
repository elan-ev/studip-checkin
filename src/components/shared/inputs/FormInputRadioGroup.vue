<template>
    <div class="form-radio-group-container">
        <div 
            v-for="(option, index) in element?.payload?.options" 
            :key="index" 
            class="radio-option"
        >
            <input
                :id="`${id}-${index}`"
                :name="id"
                type="radio"
                :value="index"
                :checked="Number(modelValue) === index"
                @change="$emit('update:modelValue', index)"
                :disabled="disabled"
                :required="required && index === 0"
                class="form-radio-field"
            >
            <label :for="`${id}-${index}`" class="radio-label">
                {{ option.text }}
            </label>
        </div>
    </div>
</template>

<script setup>
defineProps({
    modelValue: [String, Number],
    id: String,
    element: Object,
    disabled: Boolean,
    required: [Boolean, String]
});

defineEmits(['update:modelValue']);
</script>

<style lang="scss" scoped>
.form-radio-group-container {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 4px;
}

.radio-option {
    display: flex;
    align-items: center;
    cursor: pointer;

    .form-radio-field {
        margin-right: 8px;
        cursor: pointer;
        accent-color: var(--color--brand-primary);
    }

    .radio-label {
        cursor: pointer;
        font-weight: normal;
    }
}
</style>