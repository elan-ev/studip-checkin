<template>
    <div class="form-elements-list" v-if="open">
        <ul>
            <li v-for="element in elements" :key="element.id">
                <FormAddInputElement
                    :type="element.type" :display-name="element.displayName" @click="addElement(element)" />
            </li>
        </ul>
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
        const elements = []
        for (const element of inputElements) {
            elements.push({
                id: element.type,
                type: element.type,
                displayName: element.displayName,
                payload: {
                    placeholder: '',
                    label: '',
                    options: []
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
        background-color: lightgreen;
    }
</style>
