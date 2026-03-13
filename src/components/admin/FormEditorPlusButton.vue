<template>
    <button
        class="form-editor-plus-container"
        :class="{ 'form-editor-plus-container-active': isActive }"
        @click.prevent="requestAddElementForIndex"
        type="button"
    >
        <div class="liner"></div>
        <StudipIcon :shape="isActive ? 'arr_eol-down' : 'add'" class="form-editor-plus-icon" />
        <div class="liner"></div>
    </button>
</template>

<script setup>
import { computed } from 'vue';
import StudipIcon from '@/components/studip/StudipIcon.vue';
import { useFormBuilderStore } from '@/store/form-builder';
const formBuilderStore = useFormBuilderStore();

const props = defineProps({
    index: {
        type: Number,
        required: true,
    },
});

const isActive = computed(() => {
    return props.index === formBuilderStore.pendingElementIndex;
});

const emit = defineEmits(['startAddingElement']);
const requestAddElementForIndex = () => {
    emit('startAddingElement', isActive.value ? null : props.index);
};
</script>

<style lang="scss">
.form-editor-plus-container {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    width: 100%;
    margin: 1rem 0;
    padding: 0;
    background: transparent;
    border: none;
    cursor: pointer;
    color: var(--base-color);

    .liner {
        flex: 1;
        border-bottom: dashed 2px var(--base-color);
    }
    .form-editor-plus-icon {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    &.form-editor-plus-container-active {
        .liner {
            border-bottom: solid 2px var(--base-color);
        }
    }
}
</style>
