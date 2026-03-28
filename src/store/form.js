import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from './api/kitsu-api.js';

export const useFormStore = defineStore('formStore', () => {
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
        return [...records.value.values()];
    });

    async function removeRecord(formId, deletePermanently = false) {
        let performRemove = true;
        if (deletePermanently) {
            isLoading.value = true;
            try {
                await api.delete('checkin-forms', formId);
            } catch (err) {
                console.error(`Error while permanently deleting form with id: ${formId}`, err);
                errors.value = err;
                performRemove = false;
            } finally {
                isLoading.value = false;
            }
        }
        if (performRemove) {
            records.value.delete(String(formId));
        }
    }

    function byId(id) {
        void records.value.size;
        return records.value.get(String(id));
    }

    async function fetchAll(includePaths = []) {
        isLoading.value = true;
        try {
            const config = prepareRequestConfig(includePaths);
            const { data } = await api.fetch('checkin-forms', config);
            if (data) {
                clearRecords();
                data.forEach((form) => {
                    storeRecord(form);
                });
            }
        } catch (err) {
            console.error('Error while fetching forms', err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchById(id, includePaths = []) {
        isLoading.value = true;
        try {
            const config = prepareRequestConfig(includePaths);
            const { data } = await api.fetch(`checkin-forms/${id}`, config);
            storeRecord(data);
        } catch (err) {
            console.error(`Error while fetching form by id: ${id}`, err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    async function createForm(formData, includePaths = []) {
        isLoading.value = true;
        try {
            const config = prepareRequestConfig(includePaths);
            const { data } = await api.post('checkin-forms', formData, config);
            storeRecord(data);
        } catch (err) {
            console.error('Error while creating form', err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    async function copyForm(formId) {
        isLoading.value = true;
        const formData = {
                type: 'checkin-forms',
                'source-id': formId
        }
        try {
            const { data } = await api.post('checkin-forms/copy', formData);
            storeRecord(data);
        } catch (err) {
            console.error('Error while coping form', err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    async function updateForm(formData, includePaths = []) {
        isLoading.value = true;
        try {
            const config = prepareRequestConfig(includePaths);
            const { data } = await api.patch('checkin-forms', formData, config);
            data.id = formData.id;
            storeRecord(data);
        } catch (err) {
            console.error(`Error while updating form id: ${formData.id}`, err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    function prepareRequestConfig(includePaths = []) {
        const config = {};
        if (includePaths.length > 0) {
            config.params = {
                include: includePaths.join(','),
            };
        }
        return config;
    }

    async function fetchByUserId(userId, all = false) {
        isLoading.value = true;
        try {
            const { data } = await api.get(`checkin-user-forms/${userId}/${all ? 'all' : 'pending'}`);
            clearRecords();
            data.forEach((form) => {
                storeRecord(form);
            });
        } catch (err) {
            console.error(`Error while fetching forms for user with id: ${userId}`, err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    function completeUserFormDataRecord(formId) {
        records.value.delete(String(formId));
    }

    return {
        records,
        storeRecord,
        clearRecords,
        removeRecord,
        isLoading,
        errors,
        all,
        byId,
        fetchById,
        createForm,
        copyForm,
        updateForm,
        fetchAll,
        fetchByUserId,
        completeUserFormDataRecord,
    };
});
