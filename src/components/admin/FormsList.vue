<template>
    <table class="default">
        <caption>{{ $gettext('Liste der Forms') }}</caption>
        <thead>
            <tr>
                <th scope="col" width="1%">
                    <input type="checkbox" name="form-bulk-selection" id="form-bulk-selection">
                </th>
                <th scope="col">{{ $gettext('Name') }}</th>
                <th scope="col">{{ $gettext('Version') }}</th>
                <th scope="col">{{ $gettext('Startdatum') }}</th>
                <th scope="col">{{ $gettext('Enddatum') }}</th>
                <th scope="col">{{ $gettext('Zahl der Nutzern') }}</th>
                <th scope="col">{{ $gettext('Zahl der Datei') }}</th>
                <th scope="col">{{ $gettext('Aktionen') }}</th>
            </tr>
        </thead>
        <tbody>
            <template v-if="forms.length === 0">
                <tr>
                    <td colspan="8">{{ $gettext('Keine Formulare gefunden.') }}</td>
                </tr>
            </template>
            <template v-else>
                <FormItem
                    v-for="form in forms"
                    :key="form.id"
                    :form="form"
                />
            </template>
        </tbody>
    </table>
</template>

<script setup>
    import FormItem from './FormItem.vue';

    const props = defineProps({
        forms: {
            type: Array,
            required: true,
        },
    });

    // TODO: Add bulk actions!

</script>

<style>

</style>
