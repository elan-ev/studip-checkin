<template>
    <tr>
        <td><router-link :to="{ path: `/edit/${form.id}` }">{{ form.name[lang] }}</router-link></td>
        <td :title="filterText">{{ filterCounter }}</td>
        <td>{{ form.version }}</td>
        <td>{{ startDate }}</td>
        <td>{{ endDate }}</td>
        <td>{{ status }}</td>
        <td>
            <router-link :to="{ path: `/form/${form.id}/related-users` }" class="checkin-table-link">{{ userNum }}</router-link>
        </td>
        <td>
            <router-link :to="{ path: `/form/${form.id}/user-data` }" class="checkin-table-link">{{ dataNum }}</router-link>
        </td>
        <td class="actions">
            <StudipActionMenu
                :context="$gettext('Formular')"
                :collapse-at="0"
                :items="[
                    { id: 1, label: $gettext('Bearbeiten'), icon: 'edit', emit: 'edit' },
                    { id: 2, label: $gettext('Kopieren'), icon: 'copy', emit: 'copy' },
                    { id: 3, label: $gettext('Löschen'), icon: 'trash', emit: 'delete' },
                    { id: 4 , type: 'separator'},
                    { id: 5, label: $gettext('Rückläufe'), icon: 'reply', emit: 'showResponses' },
                    { id: 6, label: $gettext('Nutzende'), icon: 'group2', emit: 'showUsers' },
                    { id: 7, label: $gettext('Daten exportieren'), icon: 'export', type: 'link', url: exportURL },
                ]"
                @edit="redirectEdit"
                @copy="copyForm"
                @delete="deleteForm"
                @showResponses="redirectResponses"
                @showUsers="redirectRelatedUsers"
            />
        </td>
    </tr>
</template>

<script setup>
import { computed } from 'vue';
import { useGettext } from 'vue3-gettext';
import { useFormStore } from '@/store/form';
import { useContextStore } from '@/store/context';
import { useRouter } from 'vue-router';
import StudipActionMenu from '@/components/studip/StudipActionMenu.vue';

const { $gettext } = useGettext();
const formStore = useFormStore();
const contextStore = useContextStore();
const router = useRouter();

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

const lang = computed(() => {
    return contextStore.langSelector;
});


const filterCounter = computed(() => {
    const filters = props.form?.['user-filter']?.data?.fields;
    
    return filters ? Object.keys(filters).length : 0;
});

const filterText = computed(() => {
    const rawText = props.form?.['user-filter']?.data?.text ?? '';

    return rawText
        .replace(/\s*\(.*?\)\s*$/, '')
        .replace(/<[^>]+>/g, '')
        .replace(/&nbsp;/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
});

const status = computed(() => {
    if (isExpired.value) {
        return $gettext('abgelaufen');
    }

    if (isEndingSoon.value) {
        return $gettext('endet bald');
    }

    return $gettext('aktiv');
});

const isExpired = computed(() => {
    const endDate = props.form?.['end-date'];
    if (!endDate) return false;

    const today = new Date();
    const end = new Date(endDate);

    today.setHours(0, 0, 0, 0);
    end.setHours(0, 0, 0, 0);

    return end < today;
});

const isEndingSoon = computed(() => {
    const endDate = props.form?.endDate;
    if (!endDate) return false;

    const today = new Date();
    const end = new Date(endDate);

    today.setHours(0, 0, 0, 0);
    end.setHours(0, 0, 0, 0);

    const diffMs = end - today;
    const diffDays = diffMs / (1000 * 60 * 60 * 24);

    return diffDays >= 0 && diffDays <= 7;
});

const userNum = computed(() => {
    return props.form?.['related-users']?.data?.length ?? 0;
});

const dataNum = computed(() => {
    return props.form?.['form-user-data']?.data?.length ?? 0;
});

const startDate = computed(() => {
    if (!props.form?.['start-date']) {
        return '--';
    }
    const [year, month, day] = props.form['start-date'].split('-');
    return `${day}.${month}.${year}`;
});

const endDate = computed(() => {
    if (!props.form?.['end-date']) {
        return '--';
    }
    const [year, month, day] = props.form['end-date'].split('-');
    return `${day}.${month}.${year}`;
});

const exportURL = computed(() => {
    return window.STUDIP.ABSOLUTE_URI_STUDIP + 'plugins.php/studipcheckin/admin/export?id=' + props.form.id;
});

const copyForm = () => {
    formStore.copyForm(props.form.id);
}

const deleteForm = () => {
    if (
        STUDIP.Dialog.confirm(
            $gettext('Möchten Sie das Formular "%{name}" wirklich löschen?', { name: props.form.name[lang] }),
            () => {
                formStore.removeRecord(props.form.id, true);
            },
            STUDIP.Dialog.close(),
        )
    );
};

const redirectEdit = () => {
    router.push({ path: `/edit/${props.form.id}` });
};

const redirectResponses = () => {
    router.push({ path:  `/form/${props.form.id}/user-data` });
}

const redirectRelatedUsers = () => {
    router.push({ path:  `/form/${props.form.id}/related-users` });
}
</script>
<style lang="scss">
.checkin-table-link {
    display: block;
}
</style>