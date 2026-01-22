<template>
    <div>
        <RouterLink :to="{ name: 'user-form-data'}" class="button add">
            {{ $gettext('go to single form data') }}
        </RouterLink>
        <h1>The form goes here...</h1>
    </div>
</template>

<script setup>
    import { defineProps } from 'vue';

    const props = defineProps({
        userId: {
            type: String,
            required: true,
        },
    });

</script>

