<template>
    <table class="default">
        <caption>
            <span class="actions">
                <RouterLink :to="{ path: '/new' }" :title="$gettext('Neues Formular erstellen')">
                    <StudipIcon shape="add" />
                </RouterLink>
            </span>
            {{ $gettext('Formulare') }}
        </caption>
        <thead>
            <tr>
                <th scope="col" width="50%" :aria-sort="getAriaSort('name')">
                    <button type="button" class="as-link sort-button" @click="sortBy('name')">
                        {{ $gettext('Name') }}
                        <StudipIcon v-if="sortKey === 'name'" :shape="sortOrder === 'asc' ? 'arr_1up' : 'arr_1down'"
                            size="12" />
                    </button>
                </th>
                <th scope="col">{{ $gettext('Filter') }}</th>
                <th scope="col">{{ $gettext('Version') }}</th>
                <th scope="col" :aria-sort="getAriaSort('start')">
                    <button type="button" class="as-link sort-button" @click="sortBy('start')">
                        {{ $gettext('Startdatum') }}
                        <StudipIcon v-if="sortKey === 'start'" :shape="sortOrder === 'asc' ? 'arr_1up' : 'arr_1down'"
                            size="12" />
                    </button>
                </th>
                <th scope="col" :aria-sort="getAriaSort('end')">
                    <button type="button" class="as-link sort-button" @click="sortBy('end')">
                        {{ $gettext('Enddatum') }}
                        <StudipIcon v-if="sortKey === 'end'" :shape="sortOrder === 'asc' ? 'arr_1up' : 'arr_1down'"
                            size="12" />
                    </button>
                </th>
                <th scope="col">{{ $gettext('Status') }}</th>
                <th scope="col" :aria-sort="getAriaSort('users')">
                    <button type="button" class="as-link sort-button" @click="sortBy('users')">
                        {{ $gettext('Nutzende') }}
                        <StudipIcon v-if="sortKey === 'users'" :shape="sortOrder === 'asc' ? 'arr_1up' : 'arr_1down'"
                            size="12" />
                    </button>
                </th>
                <th scope="col" :aria-sort="getAriaSort('responses')">
                    <button type="button" class="as-link sort-button" @click="sortBy('responses')">
                        {{ $gettext('Rückläufe') }}
                        <StudipIcon v-if="sortKey === 'responses'"
                            :shape="sortOrder === 'asc' ? 'arr_1up' : 'arr_1down'" size="12" />
                    </button>
                </th>
                <th scope="col" class="actions">{{ $gettext('Aktionen') }}</th>
            </tr>
        </thead>
        <tbody>
            <template v-if="sortedForms.length === 0">
                <tr>
                    <td colspan="9">{{ $gettext('Keine Formulare gefunden.') }}</td>
                </tr>
            </template>
            <template v-else>
                <FormItem v-for="form in sortedForms" :key="form.id" :form="form" />
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
import { ref, computed, onMounted } from 'vue';
import FormItem from './FormItem.vue';
import StudipIcon from '@/components/studip/StudipIcon.vue';

const props = defineProps({
    forms: {
        type: Array,
        required: true,
    },
});

const sortKey = ref('');
const sortOrder = ref('asc');

const lang = strtok(str_replace('_', '-', window.STUDIP?.URLHelper?.parameters?._language || 'de'), '-');

const sortedForms = computed(() => {
    if (!sortKey.value) {
        return props.forms;
    }

    return [...props.forms].sort((a, b) => {
        let modifier = sortOrder.value === 'asc' ? 1 : -1;
        let valA, valB;

        switch (sortKey.value) {
            case 'name':
                valA = a.name?.[lang] || a.name?.de || '';
                valB = b.name?.[lang] || b.name?.de || '';
                return valA.localeCompare(valB, lang) * modifier;

            case 'start':
                valA = a['start-date'] || 0;
                valB = b['start-date'] || 0;
                break;

            case 'end':
                valA = a['end-date'] || 0;
                valB = b['end-date'] || 0;
                break;

            case 'users':
                valA = Number(a?.['related-users']?.data?.length || 0);
                valB = Number(b?.['related-users']?.data?.length || 0);
                break;

            case 'responses':
                // Entspricht dataNum in FormItem
                valA = Number(a?.['form-user-data']?.data?.length || 0);
                valB = Number(b?.['form-user-data']?.data?.length || 0);
                break;

            default:
                return 0;
        }

        if (valA < valB) return -1 * modifier;
        if (valA > valB) return 1 * modifier;
        return 0;
    });
});

const sortBy = (key) => {
    if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortKey.value = key;
        sortOrder.value = 'asc';
    }
};

const getAriaSort = (key) => {
    if (sortKey.value !== key) return 'none';
    return sortOrder.value === 'asc' ? 'ascending' : 'descending';
};

function strtok(str, token) {
    return str.split(token)[0];
}
function str_replace(search, replace, subject) {
    return subject.replace(new RegExp(search, 'g'), replace);
}

onMounted(() => {
    sortBy('name');
})
</script>

<style lang="scss" scoped>
.sort-button {
    display: inline-flex;
    align-items: baseline;
    gap: 4px;
    background: none;
    border: none;
    color: var(--color--highlight, var(--base-color));
    font: inherit;
    cursor: pointer;
    padding: 0;
    text-align: left;
    width: 100%;

    &:focus-visible {
        outline: 2px solid var(--color--highlight, var(--base-color));
        outline-offset: 2px;
    }
}
</style>