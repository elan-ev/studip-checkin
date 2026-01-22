<template>
    <div class="checkin-overview">
        <div class="checkin-form-list-container">
            <div class="checkin-form-list-header">
                <RouterLink :to="{ path: '/new'}" class="button add">
                    {{ $gettext('Neues Form erstellen') }}
                </RouterLink>
            </div>
            <div class="checkin-form-list">
                <FormsList v-if="formStore.all.length > 0" :forms="formStore.all" />
            </div>
            <div class="checkin-form-list-footer">
                <!-- Here comes the pagination! -->
            </div>
        </div>
    </div>
</template>
<script setup>
    import { onMounted } from 'vue';
    import { useFormStore } from '@/store/form';
    import { useUserFilterStore } from '@/store/user-filter';
    import FormsList from '../../components/admin/FormsList.vue';

    const formStore = useFormStore();
    const userFilterStore = useUserFilterStore();

    onMounted(async () => {
        await formStore.fetchAll(['related-users', 'form-user-data']);
        await userFilterStore.fetchAvailableFields();
    });
</script>

<style lang="scss">
    .checkin-overview {
        display: flex;
        width: 100%;
        height: 100%;
        flex-direction: row;
        gap: 1rem;
    }
    .checkin-form-list-container {
        flex: 1;
        padding: 1rem;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
</style>
