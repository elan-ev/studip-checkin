<template>
    <div class="form-checkbox-group-container">
        <div 
            v-for="(option, index) in element?.payload?.options" 
            :key="index" 
            class="checkbox-option"
        >
            <input
                :id="`${id}-${index}`"
                type="checkbox"
                :value="index"
                :checked="Array.isArray(modelValue) && modelValue.includes(index)"
                @change="toggleOption(index)"
                :disabled="disabled"
                :required="required && (!modelValue || modelValue.length === 0)"
                class="form-checkbox-field"
            >
            <label :for="`${id}-${index}`" class="checkbox-label">
                {{ option.text }}
            </label>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    },
    id: String,
    element: Object,
    disabled: Boolean,
    required: [Boolean, String]
});

const emit = defineEmits(['update:modelValue']);

const toggleOption = (index) => {
    const currentValues = Array.isArray(props.modelValue) ? [...props.modelValue] : [];
    const existingIndex = currentValues.indexOf(index);

    if (existingIndex > -1) {
        currentValues.splice(existingIndex, 1);
    } else {
        currentValues.push(index);
    }
    
    emit('update:modelValue', currentValues);
};
</script>

<style lang="scss" scoped>
.form-checkbox-group-container {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 4px;
}

.checkbox-option {
    display: flex;
    align-items: center;
    cursor: pointer;

    .form-checkbox-field {
        margin-right: 8px;
        cursor: pointer;
        accent-color: var(--color--brand-primary);
    }

    .checkbox-label {
        cursor: pointer;
        font-weight: normal;
    }
}
</style>