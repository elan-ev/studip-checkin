<template>
<div class="checkin-add-input">
    <button class="button add checkin-add-input-element">
        {{ displayName }}
    </button>
</div>
</template>
<script setup>
import { defineProps, computed } from 'vue';
import useInputElements from '../../composables/inputElements';

const props = defineProps({
    type: String,
    displayName: String
});

const inputElements = useInputElements();
const currentElement = computed(() => {
    return inputElements.find(element => element.type === props.type)
});


// TODO add icons etc
</script>
<style lang="css" scoped>
    .checkin-add-input {
        margin: auto;
        padding: 2px;
        border: 1px solid gray;
    }
</style>
