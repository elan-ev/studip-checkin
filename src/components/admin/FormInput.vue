<template>
    <template v-if="labelAfter">
        <legend>{{ capitalize($gettext(props.element?.type)) }}</legend>
        <div class="checkin-form-input">
            <input
                :id="props.element?.payload?.label"
                :type="props.element?.type"
                :value="props.element?.payload?.value"
            >
            <label for="">{{ props.element?.payload?.label }}</label>
        </div>
    </template>
    <template v-else>
        <legend>{{ capitalize($gettext(props.element?.type)) }}</legend>
        <label for="">{{ props.element?.payload?.label }}</label>
        <input
            :id="props.element?.payload?.label"
            :type="props.element?.type"
            :value="props.element?.payload?.value"
        >
    </template>
</template>
<script setup>
import { computed, capitalize } from 'vue';

const props = defineProps({
    element: Object,
});

const labelAfter = computed(() => {
    const after = ['radio', 'checkbox']
    if (after.includes(props.element?.type)) {
        return true;
    }
    return false;
})

// TODO settings
</script>
<style lang="scss">
.checkin-form-input {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    label {
        margin-bottom: 0 !important;
    }
}
.checkin-form-input input[type="radio"] {
    margin: 0;
}
</style>
