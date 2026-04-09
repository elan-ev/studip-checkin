<template>
    <table class="default">
        <caption>
            {{
                $gettext('Formulare')
            }}
        </caption>
        <thead>
            <tr>
                <th scope="col" width="50%">{{ $gettext('Name') }}</th>
                <th scope="col">{{ $gettext('Version') }}</th>
                <th scope="col" class="actions">{{ $gettext('Aktionen') }}</th>
            </tr>
        </thead>
        <tbody>
            <template v-if="forms.length === 0">
                <tr>
                    <td colspan="3">{{ $gettext('Keine Formulare gefunden.') }}</td>
                </tr>
            </template>
            <template v-else>
                <FormItem v-for="form in forms" :key="form.id" :form="form" />
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
</script>
