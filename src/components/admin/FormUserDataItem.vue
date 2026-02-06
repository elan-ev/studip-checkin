<template>
    <tr>
        <td>
            <input type="checkbox" :name="`data-selection-${formData.id}`" :id="`data-selection-${formData.id}`">
        </td>
        <td>{{ userInfo }}</td>
        <td>{{ formData.version }}</td>
        <td>{{ formData.chdate }}</td>
        <td>
            <button
                :disabled="!isUpToDate"
                class="action-button"
                @click="openFormDataDrawer"
            >
                <StudipIcon
                    role="button"
                    shape="log"
                    :title="$gettext('Antwort lesen')"
                />
            </button>
            <button class="action-button" @click="deleteFormUserData">
                <StudipIcon
                    role="button"
                    shape="trash"
                    :title="$gettext('Löschen')"
                />
            </button>
        </td>
    </tr>
</template>

<script setup>
    import StudipIcon from '@/components/studip/StudipIcon.vue';
    import { computed, getCurrentInstance } from 'vue';
    import { useFormUserDataStore } from '@/store/form-user-data';
    import { useFormStore } from '@/store/form';
    import { useDrawerStore } from '@/store/drawer';

    const { proxy } = getCurrentInstance();
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
        }
    });

    const isUpToDate = computed(() => {
        const form = formStore.byId(props.formId);
        if (form) {
            return props.formData.version == form.version;
        }
        return false;
    })

    const userInfo = computed(() => {
        return `${props.formData.meta.user.fullname} (${props.formData.meta.user.username})`;
    });

    const deleteFormUserData = () => {
        if (STUDIP.Dialog.confirm(
                proxy.$gettext('Are you sure you want to delete the form data?'),
                () => {
                    formUserDataStore.removeRecord(props.formData.id, true);
                },
                STUDIP.Dialog.close()
            )
        );
    };

    const openFormDataDrawer = () => {
        drawerStore.openFormDataInDrawer(props.formId, props.formData.id, true);
    }

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
