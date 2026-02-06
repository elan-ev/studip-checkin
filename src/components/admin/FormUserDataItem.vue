<template>
    <tr>
        <td>
            <input type="checkbox" :name="`data-selection-${formData.id}`" :id="`data-selection-${formData.id}`">
        </td>
        <td>{{ userInfo }}</td>
        <td>{{ formData.version }}</td>
        <td>{{ formData.chdate }}</td>
        <td>
            <StudipIcon
                role="button"
                shape="eye"
                @click="openFormDataDrawer"
            />
            <button class="button trash" @click="deleteFormUserData">
                {{ $gettext('Delete') }}
            </button>
        </td>
    </tr>
</template>

<script setup>
    import StudipIcon from '@/components/studip/StudipIcon.vue';
    import { computed, watch } from 'vue';
    import { useFormUserDataStore } from '@/store/form-user-data';
    import { useDrawerStore } from '@/store/drawer';

    const formUserDataStore = useFormUserDataStore();
    const drawerStore = useDrawerStore();

    const props = defineProps({
        formData: {
            type: Object,
            required: true,
        },
        formId: {
            type: Number,
            required: true,
        }
    });

    const userInfo = computed(() => {
        return `${props.formData.meta.user.fullname} (${props.formData.meta.user.username})`;
    });

    const deleteFormUserData = () => {
        // TODO: Add confirmation dialog and handler!
        if (confirm(`Are you sure you want to delete the form data?`)) {
            formUserDataStore.removeRecord(props.formData.id, true);
        }
    };

    const openFormDataDrawer = () => {
        drawerStore.openFormDataInDrawer(props.formId, props.formData.id, true);
    }

</script>
