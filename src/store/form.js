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
        return  [...records.value.values()];
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
            const { data } = await api.fetch(generatePathWithIncludes('checkin-forms', includePaths));
            data.forEach((form => {
                storeRecord(form);
            }))
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
            const { data } = await api.fetch(generatePathWithIncludes(`checkin-forms/${id}`, includePaths));
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
            const { data } = await api.post(generatePathWithIncludes('checkin-forms', includePaths), formData);
            storeRecord(data);
        } catch (err) {
            console.error('Error while creating form', err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    async function updateForm(id, formData) {
        isLoading.value = true;
        try {
            const { data } = await api.patch(generatePathWithIncludes(`checkin-forms/${id}`, includePaths), formData);
            storeRecord(data);
        } catch (err) {
            console.error(`Error while updating form id: ${id}`, err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    function generatePathWithIncludes(basePath, includePaths) {
        if (includePaths.length === 0) {
            return basePath;
        }
        let includes = includePaths.map((p) => `include=${p}`);
        return `${basePath}?${includes.join('&')}`;
    }

    async function fetchByUserId(userId) {
        isLoading.value = true;
        try {
            const { data } = await api.get(`checkin-user-forms/${userId}`);
            clearRecords();
            data.forEach((form => {
                storeRecord(form);
            }))
        } catch (err) {
            console.error(`Error while fetching forms for user with id: ${userId}`, err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
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
        updateForm,
        fetchAll,
        fetchByUserId,
    };
});
