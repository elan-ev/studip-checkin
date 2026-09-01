<template>
    <table class="default">
        <caption>
            {{
                $gettext('Liste der Nutzenden für Formular:') + ` ${form.name[lang]}`
            }}
        </caption>
        <thead>
            <tr>
                <th scope="col" width="50%">{{ $gettext('Name') }}</th>
                <th scope="col">{{ $gettext('Akiv') }}?</th>
                <th scope="col">{{ $gettext('Unsichbar') }}?</th>
                <th scope="col" class="actions">{{ $gettext('Aktionen') }}</th>
            </tr>
        </thead>
        <tbody>
            <template v-if="users.length === 0">
                <tr>
                    <td colspan="7">{{ $gettext('Kein Nutzer gefunden.') }}</td>
                </tr>
            </template>
            <template v-else>
                <RelatedUserItem v-for="user in users" :key="user.id" :user="user" />
            </template>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="9">
                    <Button class="button" :disabled="!hasMore" @click="loadMoreData">
                        {{ $gettext('Weitere Einträge laden') }}
                    </Button>
                </td>
            </tr>
        </tfoot>
    </table>
</template>

<script setup>
import { computed } from 'vue';
import RelatedUserItem from './RelatedUserItem.vue';
import { useContextStore } from '@/store/context';
import { useRelatedUserStore } from '@/store/related-user';

const contextStore = useContextStore();
const relatedUserStore = useRelatedUserStore();

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
    form: {
        type: Object,
        required: true,
    },
});

const lang = computed(() => {
    return contextStore.langSelector;
});

const hasMore = computed(() => {
    const pagination = relatedUserStore.getPaginationForForm(props.form.id);

    const hasMore = pagination.hasMore;

    const totalLoaded = pagination.total === props.users.length;

    return hasMore && !totalLoaded;
});

const loadMoreData = () => {
    relatedUserStore.fetchByFormId(props.form.id, { loadMore: true });
}
</script>
