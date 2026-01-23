<template>
    <div class="form-settings">
        <form class="default">
            <fieldset>
                <label>
                    {{ $gettext('Titel') }}
                    <input type="text" id="form-title" v-model="formBuilderStore.form.name">
                </label>
                <label>
                    {{ $gettext('Bescreibung') }}
                    <textarea name="description" v-model="formBuilderStore.form.description"/>
                </label>
            </fieldset>
        </form>
    </div>
</template>

<script setup>
    import { useFormBuilderStore } from '@/store/form-builder';
    const formBuilderStore = useFormBuilderStore();

</script>

<style>
    .form-settings {
        flex-basis: 25%;
        background-color: lightcoral;
    }
</style>
