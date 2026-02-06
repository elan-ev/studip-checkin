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
                    <td colspan="8">{{ $gettext('Keinen Formulare gefunden.') }}</td>
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
                            :title="$gettext('Formular ausfühlen')"
                            @click="goToFormData(form.id)"
                        />
                        <br>
                        Drawer
                        <StudipIcon
                            role="button"
                            shape="evaluation"
                            :title="$gettext('Formular ausfühlen')"
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
    import { onMounted } from 'vue';
    import { useRouter } from 'vue-router';
    import { useFormStore } from '@/store/form';
    import { useDrawerStore } from '@/store/drawer';

    const drawerStore = useDrawerStore();
    const formStore = useFormStore();
    const router = useRouter();

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

</script>

