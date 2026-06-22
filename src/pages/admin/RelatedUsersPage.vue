<template>
    <RouterLink :to="{ path: '/' }" class="link accept">
        <button class="button">{{ $gettext('Zurück zur Übersicht') }}</button>
    </RouterLink>
    <template v-if="!relatedUsersLoading">
        <div class="checkin-related-users">
            <div class="checkin-related-users-list-container">
                <div class="checkin-related-users-list">
                    <RelatedUsersList :users="relatedUserStore.all" :form="formStore.byId(formId)" />
                </div>
            </div>
        </div>
        <StudipDrawer v-if="drawerStore.drawerAttachTarget" side="right" width="570px" :displayOverlay="true"
            :title="drawerStore.drawerTitle" :attachTo="drawerStore.drawerAttachTarget"
            :visible="drawerStore.isDrawerOpen" @close="drawerStore.closeDrawer">
            <component :is="drawerStore.drawerComponent" v-bind="drawerStore.drawerProps" />
        </StudipDrawer>
    </template>
    <template v-else>
        <LoadingSplash :message="$gettext('Liste wird geladen...')" />
    </template>
</template>

<script setup>
import RelatedUsersList from '@/components/admin/RelatedUsersList.vue';
import StudipDrawer from '@/components/studip/StudipDrawer.vue';
import LoadingSplash from '@/components/shared/LoadingSplash.vue';
import { computed, onMounted } from 'vue';
import { useRelatedUserStore } from '@/store/related-user';
import { useDrawerStore } from '@/store/drawer';
import { storeToRefs } from 'pinia';
import { useFormStore } from '@/store/form';

const formStore = useFormStore();
const relatedUserStore = useRelatedUserStore();
const drawerStore = useDrawerStore();
const { records } = storeToRefs(relatedUserStore);

const props = defineProps({
    formId: Number,
});

const relatedUsersLoading = computed(() => {
    return relatedUserStore.isLoading;
});

onMounted(async () => {
    await relatedUserStore.fetchByFormId(props.formId);
    drawerStore.setDrawerAttachTarget();
});
</script>
