<template>
    <tr>
        <td>
            {{ form.name }}
        </td>
        <td>{{ form.version }}</td>
        <td class="actions">
            <StudipActionMenu
                :context="$gettext('Nutzer')"
                :collapse-at="0"
                :items="actionMenuItems"
                @show="showForm"
                @edit="editForm"
            />
        </td>
    </tr>
</template>
<script setup>
import { computed, getCurrentInstance } from 'vue';
const { proxy } = getCurrentInstance();
import StudipActionMenu from '@/components/studip/StudipActionMenu.vue';
import { useFormUserDataStore } from '@/store/form-user-data';
import { useDrawerStore } from '@/store/drawer';

const formUserDataStore = useFormUserDataStore();
const drawerStore = useDrawerStore();

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

const formData = computed(() => {
    return formUserDataStore.getByFormId(props.form.id) ?? null;
});

const hasFormData = computed(() => {
    return formData.value !== null;
});

const actionMenuItems = computed(() => {
    let menu = [];

    if (hasFormData.value) {
        menu.push({ id: 1, label: proxy.$gettext('Anzeigen'), icon: 'log', emit: 'show' });
    }

    menu.push({ id: 2, label: hasFormData.value ? proxy.$gettext('Bearbeiten') : proxy.$gettext('Ausfüllen'), icon: 'edit', emit: 'edit' });

    return menu;
});

const editForm = () => {
    if (hasFormData.value) {
        drawerStore.openFormDataInDrawer(props.form.id, formData.value.id, false);
    } else {
        drawerStore.openFormDataInDrawer(props.form.id, undefined, false);
    }
};

const showForm = () => {
    if (hasFormData.value) {
        drawerStore.openFormDataInDrawer(props.form.id, formData.value.id, true);
    } else {
        console.error('form data not found!');
    }
};
</script>
