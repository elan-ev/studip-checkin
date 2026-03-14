<template>
    <div class="form-elements-list">
        <template v-for="element in elements" :key="element.id">
            <FormAddInputElement
                :type="element.type"
                :display-name="element.displayName"
                @click="addElement(element)"
            />
        </template>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import useInputElements from '../../composables/inputElements';
import FormAddInputElement from './FormAddInputElement.vue';

const inputElements = useInputElements();
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

const elements = computed(() => {
    const elements = [];
    for (const element of inputElements) {
        elements.push({
            id: element.type,
            type: element.type,
            displayName: element.displayName,
            payload: {
                placeholder: '',
                label: '',
                options: [],
                required: '0',
            },
            relationIndices: [],
        });
    }
    return elements;
});
</script>

<style>
.form-elements-list {
    flex-basis: 25%;
    max-width: 280px;
    list-style: none;
    padding: 0;

    display: grid;
    grid-template-columns: repeat(auto-fill, 130px);
    grid-auto-rows: min-content;
    align-content: start;
    justify-content: left;
    gap: 10px;
    height: calc(100vh - var(--offset-top, 150px) - 20px);
    overflow-y: auto;
}
</style>
