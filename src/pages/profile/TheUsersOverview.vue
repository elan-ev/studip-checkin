<template>
    <FormsList :forms="forms" />
    <StudipDrawer
        v-if="drawerStore.drawerAttachTarget"
        side="right"
        width="570px"
        :displayOverlay="true"
        :attachTo="drawerStore.drawerAttachTarget"
        :visible="drawerStore.isDrawerOpen"
        @close="drawerStore.closeDrawer"
    >
        <component :is="drawerStore.drawerComponent" v-bind="drawerStore.drawerProps" />
    </StudipDrawer>
</template>
<script setup>
import { onMounted, computed } from 'vue';
import FormsList from '@/components/profile/FormsList.vue';
import StudipDrawer from '@/components/studip/StudipDrawer.vue';

import { useFormUserDataStore } from '@/store/form-user-data';
import { useDrawerStore } from '@/store/drawer';
import { useFormStore } from '@/store/form';

const formStore = useFormStore();
const formUserDataStore = useFormUserDataStore();
const drawerStore = useDrawerStore();

const userId = computed(() => {
    return window.STUDIP.USER_ID;
});

const forms = computed(() => {
    return formStore.all;
});

onMounted(async () => {
    await formStore.fetchByUserId(userId.value, true);
    await formUserDataStore.fetchAllForUser(userId.value);
    drawerStore.setDrawerAttachTarget();
    drawerStore.setDrawerContext('profile');
});
</script>

<style lang="scss">
#plugin-studip_checkin-profile-index #content-wrapper {
    position: relative;
}
</style>