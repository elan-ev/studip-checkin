import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from './api/kitsu-api.js';

export const useFormUserDataStore = defineStore('formUserDataStore', () => {
    const records = ref(new Map());
    const isLoading = ref(false);
    const errors = ref(false);

    function storeRecord(newRecord) {
        records.value.set(String(newRecord.id), newRecord);
    }

    function clearRecords() {
        records.value = new Map();
    }

    function byId(id) {
        void records.value.size;
        return records.value.get(String(id));
    }

    const all = computed(() => {
        void records.value.size;
        return  [...records.value.values()];
    });

    async function removeRecord(formUserDataId, deletePermanently = false) {
        records.value.delete(String(formUserDataId));
        if (deletePermanently) {
            isLoading.value = true;
            try {
                await api.delete('checkin-form-user-data', formUserDataId);
            } catch (err) {
                console.error(`Error while permanently deleting form user data with id: ${formUserDataId}`, err);
                errors.value = err;
            } finally {
                isLoading.value = false;
            }
        }
    }

    async function fetchAll() {
        isLoading.value = true;
        try {
            const { data } = await api.get('checkin-form-user-data');
            clearRecords();
            data.forEach((record => {
                storeRecord(record);
            }));
        } catch (err) {
            console.error('Error while fetching form user data', err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchById(id) {
        isLoading.value = true;
        try {
            const { data } = await api.get(`checkin-form-user-data/${id}`);
            storeRecord(data);
        } catch (err) {
            console.error(`Error while fetching form user data with id: ${id}`, err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    async function createRecord(payload) {
        isLoading.value = true;
        try {
            const { data } = await api.post('checkin-form-user-data', payload);
            storeRecord(data);
        } catch (err) {
            console.error('Error while creating form user data', err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    async function updateRecord(id, payload) {
        isLoading.value = true;
        try {
            const { data } = await api.patch(`checkin-form-user-data/${id}`, payload);
            data.id = id;
            storeRecord(data);
        } catch (err) {
            console.error(`Error while updating form user data with id: ${id}`, err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchByFormId(formId) {
        isLoading.value = true;
        try {
            const { data } = await api.get(`checkin-forms/${formId}/form-user-data`);
            clearRecords();
            data.forEach((record => {
                storeRecord(record);
            }));
        } catch (err) {
            console.error(`Error while fetching form user data for form id: ${formId}`, err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    return {
        records,
        isLoading,
        errors,
        all,
        byId,
        fetchAll,
        fetchById,
        createRecord,
        updateRecord,
        removeRecord,
        fetchByFormId,
    };
});
