<template>
    <div class="checkin-add-input">
        <div class="checkin-add-input-element">
            {{ capitalize(type) }}
        </div>
    </div>
</template>
<script setup>
import { defineProps, capitalize, computed } from 'vue';
import useInputElements from '@/composables/inputElements';

const props = defineProps({
    type: String
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
        border: solid gray;
    }
</style>
