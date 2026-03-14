<template>
    <button class="checkin-add-input-element">
        <StudipIcon :shape="elementIcon" :size="50" />
        {{ displayName }}
    </button>
</template>
<script setup>
import { computed } from 'vue';
import StudipIcon from '@/components/studip/StudipIcon.vue';
import useInputElements from '@/composables/inputElements';

const props = defineProps({
    type: {
        type: String,
        required: true,
    },
    displayName: {
        type: String,
        required: true,
    },
});

const inputElements = useInputElements();
const currentElement = computed(() => {
    return inputElements.find((element) => element.type === props.type);
});

const elementIcon = computed(() => {
    return currentElement.value.icon !== '' ? currentElement.value.icon : 'add';
});
</script>
<style lang="scss">
$square-button-size: 130px;
.checkin-add-input-element {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    max-height: $square-button-size;
    max-width: $square-button-size;
    min-width: $square-button-size;
    min-height: $square-button-size;
    padding: 10px;
    background-color: transparent;
    color: var(--color--brand-primary);
    border: solid thin var(--color--tile-border);
    cursor: pointer;

    .studip-icon {
        height: 50px;
        margin-bottom: 8px;
    }
    span {
        min-width: 110px;
    }
    &:hover {
        border-color: var(--color--brand-primary);
    }
}
</style>
