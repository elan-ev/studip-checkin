<template>
    <tr>
        <td>
            <input type="checkbox" :name="`form-selection-${form.id}`" :id="`form-selection-${form.id}`">
        </td>
        <td>{{ form.name }}</td>
        <td>{{ form.version }}</td>
        <td>{{ startDate }}</td>
        <td>{{ endDate }}</td>
        <td>
            <router-link :to="{ path: `/form/${form.id}/related-users`}">{{ userNum }}</router-link>
        </td>
        <td>
            <router-link :to="{ path: `/form/${form.id}/user-data`}">{{ dataNum }}</router-link>
        </td>
        <td class="actions">
            <StudipActionMenu
                :context="$gettext('Formular')"
                :collapse-at="0"
                :items="[
                    { id: 1, label: $gettext('Bearbeiten'), icon: 'edit', emit: 'edit' },
                    { id: 2, label: $gettext('Löschen'), icon: 'trash', emit: 'delete' },
                ]"
                @edit="redirectEdit"
                @delete="deleteForm"
            />
        </td>
    </tr>
</template>

<script setup>
    import { computed, getCurrentInstance } from 'vue';
    import { useFormStore } from '@/store/form';
    import { useRouter } from 'vue-router';
    import StudipActionMenu from '@/components/studip/StudipActionMenu.vue';

    const { proxy } = getCurrentInstance();
    const formStore = useFormStore();
    const router = useRouter();

    const props = defineProps({
        form: {
            type: Object,
            required: true,
        },
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
        const [year, month, day] = props.form['start-date'].split('-')
        return `${day}.${month}.${year}`;
    });

    const endDate = computed(() => {
        if (!props.form?.['end-date']) {
            return '--';
        }
        const [year, month, day] = props.form['end-date'].split('-')
        return `${day}.${month}.${year}`;
    });

    const deleteForm = () => {
        if (STUDIP.Dialog.confirm(
                proxy.$gettext(`Are you sure you want to delete the form "${props.form.name}"?`),
                () => {
                    formStore.removeRecord(props.form.id, true);
                },
                STUDIP.Dialog.close()
            )
        );
    };

    const redirectEdit = () => {
        router.push({ path: `/edit/${props.form.id}` });
    };
</script>
