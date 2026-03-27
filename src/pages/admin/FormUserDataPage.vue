<template>
    <RouterLink :to="{ path: '/' }" class="link accept">
        <button class="button">{{ $gettext('Zurück zur Übersicht') }}</button>
    </RouterLink>
    <div class="checkin-form-user-data">
        <div class="checkin-form-user-data-list-container">
            <div class="checkin-form-user-data-list">
                <FormUserDataList :data="formUserDataStore.all" :form="formStore.byId(formId)" />
            </div>
            <div class="checkin-form-user-data-list-footer">
                <!-- Here comes the pagination! -->
            </div>
        </div>
    </div>
    <StudipDrawer
        v-if="drawerStore.drawerAttachTarget"
        side="right"
        width="570px"
        :title="drawerStore.drawerTitle"
        :displayOverlay="true"
        :attachTo="drawerStore.drawerAttachTarget"
        :visible="drawerStore.isDrawerOpen"
        @close="drawerStore.closeDrawer"
    >
        <component :is="drawerStore.drawerComponent" v-bind="drawerStore.drawerProps" />
    </StudipDrawer>
</template>

<script setup>
import FormUserDataList from '@/components/admin/FormUserDataList.vue';
import StudipDrawer from '@/components/studip/StudipDrawer.vue';
import { onMounted  } from 'vue';
import { useFormUserDataStore } from '@/store/form-user-data';
import { useDrawerStore } from '@/store/drawer';
import { useFormStore } from '@/store/form';

const formStore = useFormStore();
const formUserDataStore = useFormUserDataStore();
const drawerStore = useDrawerStore();

const props = defineProps({
    formId: Number,
});

onMounted(async () => {
    await formUserDataStore.fetchByFormId(props.formId);
    drawerStore.setDrawerAttachTarget();
});
</script>
