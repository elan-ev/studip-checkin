<template>
    <select
        :id="id"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        :disabled="disabled"
        :required="required"
        class="form-select-field"
    >
        <option value="" disabled selected v-if="placeholder">
            {{ placeholder }}
        </option>

        <option v-for="(option, index) in element?.payload?.options" :key="index" :value="index">
            {{ $gettext(option.text) }}
        </option>
    </select>
</template>

<script setup>
defineProps({
    modelValue: [String, Number],
    id: String,
    element: Object,
    disabled: Boolean,
    required: [Boolean, String],
    placeholder: String,
});

defineEmits(['update:modelValue']);
</script>
