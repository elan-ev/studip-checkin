<template>
    <tr>
        <td>{{ userInfo }}</td>
        <td>{{ formData.version }}</td>
        <td>{{ formData.chdate }}</td>
        <td class="actions">
            <StudipActionMenu
                :context="$gettext('Datensatz')"
                :collapse-at="0"
                :items="[
                    { id: 1, label: $gettext('Antwort betrachten'), icon: 'log', emit: 'showData' },
                    { id: 2, label: $gettext('Löschen'), icon: 'trash', emit: 'delete' },
                ]"
                @showData="openFormDataDrawer"
                @delete="deleteFormUserData"
            />
        </td>
    </tr>
</template>

<script setup>
import { computed } from 'vue';
import { useGettext } from 'vue3-gettext';
import { useFormUserDataStore } from '@/store/form-user-data';
import { useFormStore } from '@/store/form';
import { useDrawerStore } from '@/store/drawer';
import StudipActionMenu from '@/components/studip/StudipActionMenu.vue';

const { $gettext } = useGettext();
const formUserDataStore = useFormUserDataStore();
const formStore = useFormStore();
const drawerStore = useDrawerStore();

const props = defineProps({
    formData: {
        type: Object,
        required: true,
    },
    formId: {
        type: Number,
        required: true,
    },
});

const isUpToDate = computed(() => {
    const form = formStore.byId(props.formId);
    if (form) {
        return props.formData.version == form.version;
    }
    return false;
});

const userInfo = computed(() => {
    return `${props.formData.meta.user.fullname} (${props.formData.meta.user.username})`;
});

const deleteFormUserData = () => {
    if (
        STUDIP.Dialog.confirm(
            $gettext('Möchten Sie die Formulardaten wirklich löschen?'),
            () => {
                formUserDataStore.removeRecord(props.formData.id, true);
            },
            STUDIP.Dialog.close(),
        )
    );
};

const openFormDataDrawer = () => {
    drawerStore.openFormDataInDrawer(props.formId, props.formData.id, true);
};
</script>

<style lang="scss">
.action-button {
    background: none;
    border: none;
    cursor: pointer;

    .studip-icon {
        color: var(--color--highlight);
        vertical-align: middle;
    }
}
</style>
