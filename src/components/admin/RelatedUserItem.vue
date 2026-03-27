<template>
    <tr>
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
import { computed } from 'vue';
import { useGettext } from 'vue3-gettext';
import { useRelatedUserStore } from '@/store/related-user';
import StudipActionMenu from '@/components/studip/StudipActionMenu.vue';

const { $gettext } = useGettext();
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
            $gettext('Möchten Sie den Benutzer wirklich aus diesem Formular löschen?'),
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
