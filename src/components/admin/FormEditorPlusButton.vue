<template>
    <div class="form-editor-plus-container">
        <div class="liner"></div>
        <button
            class="form-editor-plus-button"
            @click.prevent="requestAddElementForIndex"
        >
        </button>
        <div class="liner"></div>
    </div>
</template>

<script setup>
    const props = defineProps({
        index: {
            type: Number,
            required: true,
        },
    });
    const emit = defineEmits(['startAddingElement']);
    const requestAddElementForIndex = () => {
        emit('startAddingElement', props.index);
    };
</script>

<style lang="scss">
    .form-editor-plus-container {
        text-align: center;
        margin: 1rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-editor-plus-button {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        border: 1px solid darkblue;
        background-color: transparent;
        color: darkblue;
        font-size: 1.5rem;
        line-height: 1.98rem;
        cursor: pointer;
    }

    .form-editor-plus-button::before {
        content: '+';
    }

    .liner {
        flex: 1;
        height: 2px;
        background-color: darkblue;
    }
</style>
