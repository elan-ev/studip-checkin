<template>
    <div class="form-input-truefalse-container">
        <studip-switch
            :id="id"
            v-model="internalValue"
            :label="element?.payload?.label[lang] || ''"
            :disabled="disabled"
            :active-icon="element?.payload?.activeIcon || 'accept'"
            :inactive-icon="element?.payload?.inactiveIcon || 'decline'"
        />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import StudipSwitch from '@/components/studip/StudipSwitch.vue';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    id: String,
    disabled: Boolean,
    required: [Boolean, String],
    lang: {
        type: String,
        default: 'de',
    },
});

const emit = defineEmits(['update:modelValue']);

const internalValue = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});
</script>

<style lang="scss">
.form-input-truefalse-container {
    display: flex;
    align-items: center;
    margin-top: 4px;
}
</style>
