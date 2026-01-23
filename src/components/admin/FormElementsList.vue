<template>
    <div class="form-elements-list" v-if="open">
        <ul>
            <li v-for="(element, index) in inputElements" :key="index">
                <FormInputElement :type="element.type" @click="addElement(element)"/>
            </li>
        </ul>
    </div>
</template>

<script setup>
    import { defineProps, defineEmits } from 'vue';
    import useInputElements from '../../composables/inputElements';
    import FormInputElement from './FormInputElement.vue';

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
</script>

<style>
    .form-elements-list {
        flex-basis: 25%;
        background-color: lightgreen;
    }
</style>
