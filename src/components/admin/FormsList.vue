<template>
    <table class="default">
        <caption>
            <span class="actions">
                <RouterLink :to="{ path: '/new' }" :title="$gettext('Neues Formular erstellen')">
                    <StudipIcon shape="add" />
                </RouterLink>
            </span>
            {{
                $gettext('Formulare')
            }}
        </caption>
        <thead>
            <tr>
                <th scope="col" width="50%">{{ $gettext('Name') }}</th>
                <th scope="col">{{ $gettext('Filter') }}</th>
                <th scope="col">{{ $gettext('Version') }}</th>
                <th scope="col">{{ $gettext('Startdatum') }}</th>
                <th scope="col">{{ $gettext('Enddatum') }}</th>
                <th scope="col">{{ $gettext('Status') }}</th>
                <th scope="col">{{ $gettext('Nutzende') }}</th>
                <th scope="col">{{ $gettext('Rückläufe') }}</th>
                <th scope="col" class="actions">{{ $gettext('Aktionen') }}</th>
            </tr>
        </thead>
        <tbody>
            <template v-if="forms.length === 0">
                <tr>
                    <td colspan="9">{{ $gettext('Keine Formulare gefunden.') }}</td>
                </tr>
            </template>
            <template v-else>
                <FormItem v-for="form in forms" :key="form.id" :form="form" />
            </template>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="9">
                    <RouterLink :to="{ path: '/new' }" class="button add">
                        {{ $gettext('Neues Formular erstellen') }}
                    </RouterLink>
                </td>
            </tr>
        </tfoot>
    </table>
</template>

<script setup>
import FormItem from './FormItem.vue';
import StudipIcon from '@/components/studip/StudipIcon.vue';

const props = defineProps({
    forms: {
        type: Array,
        required: true,
    },
});

// TODO: Add bulk actions!
</script>

<style></style>
