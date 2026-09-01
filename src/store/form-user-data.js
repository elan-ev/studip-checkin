import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from './api/kitsu-api.js';

export const useFormUserDataStore = defineStore('formUserDataStore', () => {
    const records = ref(new Map());
    const recordsByForm = ref(new Map());
    const paginationByForm = ref(new Map());
    const isLoading = ref(false);
    const isLoadingMore = ref(false);
    const errors = ref(false);

    function getPaginationForForm(formId) {
        if (!paginationByForm.value.has(formId)) {
            paginationByForm.value.set(formId, {
                offset: 0,
                limit: 30,
                total: 0,
                hasMore: true,
            });
        }
        return paginationByForm.value.get(formId);
    }

    function storeRecord(newRecord) {
        const formId = newRecord['form-id'];
        records.value.set(String(newRecord.id), newRecord);

        if (!recordsByForm.value.has(formId)) {
            recordsByForm.value.set(formId, []);
        }

        const data = recordsByForm.value.get(formId);
        const existingIndex = data.findIndex((m) => m.id === newRecord.id);

        if (existingIndex > -1) {
            data[existingIndex] = newRecord;
        } else {
            data.push(newRecord);
        }
    }

    function storeRecords(newRecords) {
        if (!newRecords || newRecords.length === 0) return;

        const formId = newRecords[0]['form-id'];

        newRecords.forEach((rec) => records.value.set(rec.id, rec));

        const currentData = recordsByForm.value.get(formId) || [];

        const updatedData = [...currentData];

        newRecords.forEach((newRecord) => {
            const existingIndex = updatedData.findIndex((m) => m.id === newRecord.id);
            if (existingIndex > -1) {
                updatedData[existingIndex] = newRecord;
            } else {
                updatedData.push(newRecord);
            }
        });


        recordsByForm.value.set(formId, updatedData);
    }

    function clearRecords() {
        records.value = new Map();
        recordsByForm.value = new Map();
        paginationByForm.value = new Map();
    }

    function byId(id) {
        void records.value.size;
        return records.value.get(String(id));
    }

    function getByFormId(formId) {
        return all.value.find((record) => record['form-id'] === Number(formId));
    }

    function byFormId(formId) {
        return recordsByForm.value.get(formId) || [];
    }

    const all = computed(() => {
        void records.value.size;
        return [...records.value.values()];
    });

    async function removeRecord(formUserDataId, deletePermanently = false) {
        const record = records.value.get(formUserDataId);
        if (!record) return;
        
        const formId = record['form-id'];
        records.value.delete(String(formUserDataId));
        if (recordsByForm.value.has(formId)) {
            const data = recordsByForm.value.get(formId);
            const filtered = data.filter((m) => m.id !== formUserDataId);
            recordsByForm.value.set(formId, filtered);
        }
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
            data.forEach((record) => {
                storeRecord(record);
            });
        } catch (err) {
            console.error('Error while fetching form user data', err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchAllForUser(userId) {
        isLoading.value = true;
        try {
            const { data } = await api.get(`users/${userId}/checkin-form-user-data`);
            data.forEach((record) => {
                storeRecord(record);
            });
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
            const { data } = await api.patch(`checkin-form-user-data`, payload);
            data.id = id;
            storeRecord(data);
        } catch (err) {
            console.error(`Error while updating form user data with id: ${id}`, err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchByFormId(formId, { loadMore = false } = {}) {
        const pagination = getPaginationForForm(formId);
        if (loadMore && (!pagination.hasMore || isLoadingMore.value)) {
            return;
        }

        if (loadMore) {
            isLoadingMore.value = true;
        } else {
            isLoading.value = true;
        }

        const currentOffset = loadMore ? pagination.offset + pagination.limit : 0;

        try {
            const { data, meta } = await api.get(`checkin-forms/${formId}/form-user-data`, {
                params: {
                    'page[offset]': currentOffset,
                    'page[limit]': pagination.limit,
                },
            });
            storeRecords(data);
            if (meta.page) {
                const total = meta.page.total ?? 0;
                const offset = meta.page.offset ?? currentOffset;
                const limit = meta.page.limit ?? pagination.limit;
                const hasMore = meta.page.hasMore ?? false;

                paginationByForm.value.set(formId, {
                    offset,
                    limit,
                    total,
                    hasMore,
                });
            }

        } catch (err) {
            console.error(`Error while fetching form user data for form id: ${formId}`, err);
            errors.value = err;
        } finally {
            isLoading.value = false;
            isLoadingMore.value = false;
        }
    }

    return {
        records,
        recordsByForm,
        isLoading,
        errors,
        all,
        byId,
        fetchAll,
        fetchAllForUser,
        fetchById,
        createRecord,
        updateRecord,
        removeRecord,
        fetchByFormId,
        getPaginationForForm,

        getByFormId,
        byFormId
    };
});
