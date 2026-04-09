<template>
    <tr>
        <td>
            <button class="as-link" @click="showForm">{{ form.name[lang] }}</button>
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
import { computed } from 'vue';
import { useGettext } from 'vue3-gettext';
import StudipActionMenu from '@/components/studip/StudipActionMenu.vue';
import { useFormUserDataStore } from '@/store/form-user-data';
import { useDrawerStore } from '@/store/drawer';
import { useContextStore } from '@/store/context';

const { $gettext } = useGettext();
const formUserDataStore = useFormUserDataStore();
const drawerStore = useDrawerStore();
const contextStore = useContextStore();

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

const lang = computed(() => {
    return contextStore.langSelector;
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
        menu.push({ id: 1, label: $gettext('Anzeigen'), icon: 'log', emit: 'show' });
    }

    menu.push({ id: 2, label: hasFormData.value ? $gettext('Bearbeiten') : $gettext('Ausfüllen'), icon: 'edit', emit: 'edit' });

    return menu;
});

const editForm = () => {
    if (hasFormData.value) {
        drawerStore.openFormDataInDrawer(props.form.id, formData.value.id, false, props.form.name[lang.value]);
    } else {
        drawerStore.openFormDataInDrawer(props.form.id, undefined, false, props.form.name[lang.value]);
    }
};

const showForm = () => {
    if (hasFormData.value) {
        drawerStore.openFormDataInDrawer(props.form.id, formData.value.id, true, props.form.name[lang.value]);
    } else {
        console.error('form data not found!');
    }
};
</script>
