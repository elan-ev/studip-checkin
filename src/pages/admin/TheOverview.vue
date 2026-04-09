<template>
    <section class="checkin-overview">
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
    await formStore.fetchAll(['related-users', 'form-user-data', 'user-filter']);
    await userFilterStore.fetchAvailableFields();
});
</script>

<style lang="scss">
#plugin-studip_checkin-admin-index #content-wrapper {
    position: relative;
    padding: 15px 15px 50px 15px;
}

.button-undecorated {
    color: var(--base-color);
    border: none;
    cursor: pointer;
    background-color: transparent;
    height: 28px;
}
</style>
