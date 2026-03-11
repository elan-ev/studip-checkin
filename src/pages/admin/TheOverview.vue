<template>
    <section class="checkin-overview">
        <header class="checkin-header">
            <h2 class="checkin-header-content">
                {{ $gettext('CheckIn-Plugin') }}
            </h2>
        </header>
        <div class="checkin-form-list-container">
            <div class="checkin-form-list">
                <FormsList :forms="formStore.all" />
            </div>
        </div>
    </section>
</template>
<script setup>
import { onMounted } from 'vue';
import { useFormStore } from '@/store/form';
import { useUserFilterStore } from '@/store/user-filter';
import FormsList from '@/components/admin/FormsList.vue';

const formStore = useFormStore();
const userFilterStore = useUserFilterStore();

onMounted(async () => {
    await formStore.fetchAll(['related-users', 'form-user-data']);
    await userFilterStore.fetchAvailableFields();
});
</script>

<style lang="scss">
#plugin-studip_checkin-admin-index #content-wrapper {
    position: relative;
}
#studip-checkin-admin-app {
    .checkin-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;

        .checkin-header-content {
            margin: 0;
            padding: 0;
            font-size: 22px;
        }
    }
}
</style>
