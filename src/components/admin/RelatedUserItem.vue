<template>
    <tr>
        <td>
            <input type="checkbox" :name="`user-selection-${user.id}`" :id="`user-selection-${user.id}`" />
        </td>
        <td>{{ userInfo }}</td>
        <td>
            <input type="checkbox" v-model="user.active" :checked="user.active" @change="updateUser" />
        </td>
        <td>
            <input type="checkbox" v-model="user.hide" :checked="user.hide" @change="updateUser" />
        </td>
        <td class="actions">
            <StudipActionMenu
                :context="$gettext('Nutzer')"
                :collapse-at="0"
                :items="[{ id: 1, label: $gettext('Löschen'), icon: 'trash', emit: 'delete' }]"
                @delete="deleteUser"
            />
        </td>
    </tr>
</template>

<script setup>
import { computed, getCurrentInstance } from 'vue';
import { useRelatedUserStore } from '@/store/related-user';
import StudipActionMenu from '@/components/studip/StudipActionMenu.vue';

const { proxy } = getCurrentInstance();
const relatedUserStore = useRelatedUserStore();

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const userInfo = computed(() => {
    return `${props.user.meta.user.fullname} (${props.user.meta.user.username})`;
});

const deleteUser = () => {
    if (
        STUDIP.Dialog.confirm(
            proxy.$gettext(`Are you sure you want to delete the user "${props.user.fullname}" from this form?`),
            () => {
                relatedUserStore.removeRecord(props.user.id, true);
            },
            STUDIP.Dialog.close(),
        )
    );
};

const updateUser = async () => {
    await relatedUserStore.updateRecord(props.user.id, props.user);
};
</script>
