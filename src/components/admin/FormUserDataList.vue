<template>
  <table class="default">
        <caption>{{ $gettext('Liste der Nutzer unter Form:') + ` ${form.name}` }}</caption>
        <thead>
            <tr>
                <th scope="col" width="1%">
                    <input type="checkbox" name="data-bulk-selection" id="data-bulk-selection">
                </th>
                <th scope="col" width="50%">{{ $gettext('Nutzende') }}</th>
                <th scope="col">{{ $gettext('Version des Formulars') }}</th>
                <th scope="col">{{ $gettext('Datum') }}</th>
                <th scope="col" class="actions">{{ $gettext('Aktionen') }}</th>
            </tr>
        </thead>
        <tbody>
            <template v-if="data.length === 0">
                <tr>
                    <td colspan="8">{{ $gettext('Keinen Datei gefunden.') }}</td>
                </tr>
            </template>
            <template v-else>
                <FormUserDataItem
                    v-for="formData in data"
                    :key="formData.id"
                    :formData="formData"
                    :formId="form.id"
                />
            </template>
        </tbody>
    </table>
</template>

<script setup>
    import FormUserDataItem from './FormUserDataItem.vue';

    const props = defineProps({
        data: {
            type: Array,
            required: true,
        },
        form: {
            type: Object,
            required: true,
        }
    });

</script>

<style>

</style>
