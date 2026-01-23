<template>
    <div class="form-elements-list" v-if="open">
        <ul>
            <li v-for="element in elements" :key="element.id">
                <button
                    class="button add"
                    @click="addElement(element)"
                >
                    {{ element.displayName }}
                </button>
            </li>
        </ul>
    </div>
</template>

<script setup>
    import { defineProps, defineEmits } from 'vue';

    const emit = defineEmits(['addElement']);
    const props = defineProps({
        open: {
            type: Boolean,
            required: true,
        },
    });

    const addElement = (element) => {
        emit('addElement', element);
    };

    // TODO: Here comes the elements anf the button!
    // Imaginary form elements for demonstration purposes.
    const elements = [
        { id: 'text', type: 'text', displayName: 'Text Field', payload: {}, relationIndices: [] },
        { id: 'checkbox', type: 'checkbox', displayName: 'Checkbox Field', payload: {}, relationIndices: [] },
        { id: 'email', type: 'email', displayName: 'Email Field', payload: {}, relationIndices: [] },
    ];
</script>

<style>
    .form-elements-list {
        flex-basis: 25%;
        background-color: lightgreen;
    }
</style>
