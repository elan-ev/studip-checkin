<template>
    <table class="default">
        <caption>{{ $gettext('Bitte füllen Sie die folgenden Formulare aus') }}</caption>
        <thead>
            <tr>
                <th scope="col">{{ $gettext('Name') }}</th>
                <th scope="col" width="25%">{{ $gettext('Aktionen') }}</th>
            </tr>
        </thead>
        <tbody>
            <template v-if="formStore.all.length === 0">
                <tr>
                    <td colspan="8">{{ $gettext('Keine Formulare gefunden.') }}</td>
                </tr>
            </template>
            <template v-else>
                <tr v-for="form in formStore.all">
                    <td>
                        {{ form.name }}
                    </td>
                    <td>
                        Redirect
                        <StudipIcon
                            role="button"
                            shape="evaluation"
                            :title="$gettext('Formular ausfüllen')"
                            @click="goToFormData(form.id)"
                        />
                        <br>
                        Drawer
                        <StudipIcon
                            role="button"
                            shape="evaluation"
                            :title="$gettext('Formular ausfüllen')"
                            @click="openFormDataDrawer(form.id)"
                        />
                    </td>
                </tr>
            </template>
        </tbody>
    </table>
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
    import StudipIcon from '@/components/studip/StudipIcon.vue';
    import StudipDrawer from '@/components/studip/StudipDrawer.vue';
    import { watch, onMounted } from 'vue';
    import { useGettext } from 'vue3-gettext';
    import { useRouter } from 'vue-router';
    import { useFormStore } from '@/store/form';
    import { useDrawerStore } from '@/store/drawer';
    import { storeToRefs } from 'pinia';

    const { $gettext } = useGettext();
    const router = useRouter();
    const drawerStore = useDrawerStore();
    const formStore = useFormStore();
    const { records } = storeToRefs(formStore);

    const props = defineProps({
        userId: {
            type: String,
            required: true,
        },
    });

    onMounted(async () => {
        await formStore.fetchByUserId(props.userId);
        drawerStore.setDrawerAttachTarget();
    });

    const goToFormData = (formId) => {
        router.push({ path: `/form-data/${formId}` });
    }

    const openFormDataDrawer = (formId) => {
        drawerStore.openFormDataInDrawer(formId);
    }

    watch(() => records.value, (newValue, oldValue) => {
        if (newValue.size === 0) {
            // TODO: decide how to act here! of course confirm is not needed here! :D
            if (STUDIP.Dialog.confirm(
                $gettext('Fertig! Sie können nun fortfahren.'),
                () => {
                    window.location = STUDIP.URLHelper.getURL('dispatch.php/start');
                },
                STUDIP.Dialog.close())
            );
        }
    });

</script>

