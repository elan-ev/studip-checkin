<template>
    <table class="default">
        <caption>
            <span class="actions checkin-form-caption-actions">
                <button type="button" class="as-link" :title="$gettext('Filter anzeigen/ausblenden')"
                    :aria-expanded="isFilterOpen" @click="isFilterOpen = !isFilterOpen">
                    <StudipIcon shape="filter2" />
                </button>
                <RouterLink :to="{ path: '/new' }" :title="$gettext('Neues Formular erstellen')">
                    <StudipIcon shape="add" />
                </RouterLink>
            </span>
            {{ $gettext('Formulare') }}

        </caption>
        <thead>
            <tr v-show="isFilterOpen">
                <td colspan="9" class="checkin-filter-cell">
                    <form class="default checkin-filter-wrapper">
                        <div class="checkin-filter-group">
                            <label>{{ $gettext('Status') }}
                                <select v-model="selectedStatus">
                                    <option value="all">{{ $gettext('Alle') }}</option>
                                    <option value="active">{{ $gettext('aktiv') }}</option>
                                    <option value="endingSoon">{{ $gettext('endet bald') }}</option>
                                    <option value="expired">{{ $gettext('abgelaufen') }}</option>
                                </select>
                            </label>
                        </div>

                        <div class="checkin-filter-group">
                            <label>{{ $gettext('Zeitraum von') }}
                                <input type="date" v-model="filterStartDate"
                                    :max="filterEndDate || undefined" /></label>
                        </div>

                        <div class="checkin-filter-group">
                            <label>{{ $gettext('bis') }}
                                <input type="date" v-model="filterEndDate"
                                    :min="filterStartDate || undefined" /></label>
                        </div>

                        <div class="checkin-filter-group actions"
                            v-if="filterStartDate || filterEndDate || selectedStatus !== 'all'">
                            <button type="button" class="button secondary" @click="resetFilters">
                                {{ $gettext('Filter zurücksetzen') }}
                            </button>
                        </div>
                    </form>
                </td>
            </tr>
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
            <template v-if="filteredAndSortedForms.length === 0">
                <tr>
                    <td colspan="9">{{ $gettext('Keine Formulare gefunden.') }}</td>
                </tr>
            </template>
            <template v-else>
                <FormItem v-for="form in filteredAndSortedForms" :key="form.id" :form="form" />
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

const isFilterOpen = ref(false);
const selectedStatus = ref('all');
const filterStartDate = ref('');
const filterEndDate = ref('');

const lang = strtok(str_replace('_', '-', window.STUDIP?.URLHelper?.parameters?._language || 'de'), '-');

const checkExpired = (form) => {
    const endDate = form?.['end-date'];
    if (!endDate) return false;
    const today = new Date();
    const end = new Date(endDate);
    today.setHours(0, 0, 0, 0);
    end.setHours(0, 0, 0, 0);
    return end < today;
};

const checkEndingSoon = (form) => {
    const endDate = form?.['end-date'] || form?.endDate;
    if (!endDate) return false;
    const today = new Date();
    const end = new Date(endDate);
    today.setHours(0, 0, 0, 0);
    end.setHours(0, 0, 0, 0);
    const diffMs = end - today;
    const diffDays = diffMs / (1000 * 60 * 60 * 24);
    return diffDays >= 0 && diffDays <= 7;
};

const filteredAndSortedForms = computed(() => {
    let result = props.forms.filter(form => {
        if (selectedStatus.value !== 'all') {
            const isExpired = checkExpired(form);
            const isEndingSoon = checkEndingSoon(form);

            if (selectedStatus.value === 'expired' && !isExpired) return false;
            if (selectedStatus.value === 'endingSoon' && (!isEndingSoon || isExpired)) return false;
            if (selectedStatus.value === 'active' && (isExpired || isEndingSoon)) return false;
        }

        const formStart = form['start-date'] ? new Date(form['start-date']) : new Date('1970-01-01');
        const formEnd = form['end-date'] ? new Date(form['end-date']) : new Date('2099-12-31');

        formStart.setHours(0, 0, 0, 0);
        formEnd.setHours(0, 0, 0, 0);

        if (filterStartDate.value) {
            const filterStart = new Date(filterStartDate.value);
            filterStart.setHours(0, 0, 0, 0);
            if (formEnd < filterStart) return false;
        }

        if (filterEndDate.value) {
            const filterEnd = new Date(filterEndDate.value);
            filterEnd.setHours(0, 0, 0, 0);
            if (formStart > filterEnd) return false;
        }

        return true;
    });

    if (!sortKey.value) {
        return result;
    }

    return result.sort((a, b) => {
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

const resetFilters = () => {
    selectedStatus.value = 'all';
    filterStartDate.value = '';
    filterEndDate.value = '';
};

function strtok(str, token) {
    return str.split(token)[0];
}
function str_replace(search, replace, subject) {
    return subject.replace(new RegExp(search, 'g'), replace);
}

onMounted(() => {
    sortBy('name');
});
</script>

<style lang="scss" scoped>
.checkin-form-caption-actions {
    display: flex;
    gap: 8px;
}

.checkin-filter-cell {
    padding: 0 !important;
}

.checkin-filter-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    align-items: center;
    margin-bottom: 16px;
    padding: 12px;
    background-color: #f5f5f5;
    border: none;

    .checkin-filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;

        &.actions {
            margin-left: auto;
        }
    }
}

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