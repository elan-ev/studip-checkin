<template>
    <table class="default">
        <caption>
            {{
                $gettext('Liste der Nutzer unter Form:') + ` ${form.name[lang]}`
            }}
            <span class="actions">
                <a :href="exportLink" class="button checkin-caption-action-export-button">
                    <StudipIcon shape="export" class="checkin-caption-action-export-icon" />
                    {{ $gettext('CSV Export') }}
                </a>
            </span>
        </caption>
        <thead>
            <tr>
                <th scope="col" width="50%">{{ $gettext('Nutzende') }}</th>
                <th scope="col">{{ $gettext('Version des Formulars') }}</th>
                <th scope="col">{{ $gettext('letzte Änderung') }}</th>
                <th scope="col" class="actions">{{ $gettext('Aktionen') }}</th>
            </tr>
        </thead>
        <tbody>
            <template v-if="data.length === 0">
                <tr>
                    <td colspan="7">{{ $gettext('Keine Daten gefunden.') }}</td>
                </tr>
            </template>
            <template v-else>
                <FormUserDataItem v-for="formData in data" :key="formData.id" :formData="formData" :formId="form.id" />
            </template>
        </tbody>
    </table>
</template>

<script setup>
import { computed } from 'vue';
import StudipIcon from '@/components/studip/StudipIcon.vue';
import FormUserDataItem from './FormUserDataItem.vue';
import { useContextStore } from '@/store/context';

const contextStore = useContextStore();

const props = defineProps({
    data: {
        type: Array,
        required: true,
    },
    form: {
        type: Object,
        required: true,
    },
});
const exportLink = computed(() => {
    return window.STUDIP.ABSOLUTE_URI_STUDIP + 'plugins.php/studipcheckin/admin/export?id=' + props.form.id;
});

const lang = computed(() => {
    return contextStore.langSelector;
});
</script>

<style lang="scss">
.checkin-caption-action-export {
    &-button.button {
        margin: 0;
    }
    &-icon {
        display: inline-block;
        vertical-align: bottom;
        padding-right: 5px;
    }
}
</style>
