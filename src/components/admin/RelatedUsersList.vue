<template>
    <table class="default">
        <caption>{{ $gettext('Liste der Nutzenden für Formular:') + ` ${form.name}` }}</caption>
        <thead>
            <tr>
                <th scope="col" width="1%">
                    <input type="checkbox" name="users-bulk-selection" id="users-bulk-selection">
                </th>
                <th scope="col" width="50%">{{ $gettext('Name') }}</th>
                <th scope="col">{{ $gettext('Akiv?') }}</th>
                <th scope="col">{{ $gettext('Unsichbar?') }}</th>
                <th scope="col" class="actions">{{ $gettext('Aktionen') }}</th>
            </tr>
        </thead>
        <tbody>
            <template v-if="users.length === 0">
                <tr>
                    <td colspan="8">{{ $gettext('Keinen Nutzer gefunden.') }}</td>
                </tr>
            </template>
            <template v-else>
                <RelatedUserItem
                    v-for="user in users"
                    :key="user.id"
                    :user="user"
                />
            </template>
        </tbody>
    </table>
</template>

<script setup>
    import RelatedUserItem from './RelatedUserItem.vue';

    const props = defineProps({
        users: {
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
