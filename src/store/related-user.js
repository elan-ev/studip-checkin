import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from './api/kitsu-api.js';

export const useRelatedUserStore = defineStore('relatedUserStore', () => {
    const records = ref(new Map());
    const isLoading = ref(false);
    const errors = ref(false);

    function storeRecord(newRecord) {
        records.value.set(String(newRecord.id), newRecord);
    }

    function clearRecords() {
        records.value = new Map();
    }

    const all = computed(() => {
        void records.value.size;
        return  [...records.value.values()];
    });

    function byId(id) {
        void records.value.size;
        return records.value.get(String(id));
    }

    async function fetchByFormId(formId) {
        isLoading.value = true;
        try {
            const { data } = await api.get(`checkin-forms/${formId}/related-users`);
            clearRecords();
            data.forEach((relatedUser => {
                storeRecord(relatedUser);
            }))
        } catch (err) {
            console.error(`Error while fetching related users for form with id: ${formId}`, err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchAll() {
        isLoading.value = true;
        try {
            const { data } = await api.get('checkin-related-users');
            clearRecords();
            data.forEach((relatedUser => {
                storeRecord(relatedUser);
            }))
        } catch (err) {
            console.error('Error while fetching all related users', err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchById(relatedUserId) {
        isLoading.value = true;
        try {
            const { data } = await api.get(`checkin-related-users/${relatedUserId}`);
            storeRecord(data);
        } catch (err) {
            console.error(`Error while fetching related user with id: ${relatedUserId}`, err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    async function removeRecord(relatedUserId, deletePermanently = false) {
        records.value.delete(String(relatedUserId));
        if (deletePermanently) {
            isLoading.value = true;
            try {
                await api.delete('checkin-related-users', relatedUserId);
            } catch (err) {
                console.error(`Error while permanently deleting related user record with id: ${relatedUserId}`, err);
                errors.value = err;
            } finally {
                isLoading.value = false;
            }
        }
    }

    function renewRecord(newRecord) {
        removeRecord(newRecord.id);
        storeRecord(newRecord);
    }

    async function updateRecord(relatedUserId, relatedUserData) {
        isLoading.value = true;
        try {
            const { data } = await api.patch(`checkin-related-users/${relatedUserId}`, relatedUserData);
            renewRecord(data);
        } catch (err) {
            console.error(`Error while updating related user record with id: ${relatedUserId}`, err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    async function createRecord(relatedUserData) {
        isLoading.value = true;
        try {
            const { data } = await api.post('checkin-related-users', relatedUserData);
            storeRecord(data);
        } catch (err) {
            console.error('Error while creating related user record', err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    return {
        storeRecord,
        clearRecords,
        all,
        byId,
        fetchByFormId,
        fetchAll,
        fetchById,
        removeRecord,
        updateRecord,
        createRecord,
        isLoading,
        errors,
    };
});
