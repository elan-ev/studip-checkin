<template>
    <button class="button add checkin-add-input-element">
        {{ displayName }}
    </button>
</template>
<script setup>
import { computed } from 'vue';
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
<style lang="scss">

</style>
