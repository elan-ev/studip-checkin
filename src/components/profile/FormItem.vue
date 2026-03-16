<template>
    <tr>
        <td>{{ form.name }}</td>
        <td>{{ form.version }}</td>
        <td class="actions"></td>
    </tr>
</template>
<script setup>

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});
</script>
