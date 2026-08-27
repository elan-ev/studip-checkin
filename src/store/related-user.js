import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from './api/kitsu-api.js';

export const useRelatedUserStore = defineStore('relatedUserStore', () => {
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

        const users = recordsByForm.value.get(formId);
        const existingIndex = users.findIndex((m) => m.id === newRecord.id);

        if (existingIndex > -1) {
            users[existingIndex] = newRecord;
        } else {
            users.push(newRecord);
        }
    }

    function storeRecords(newRecords) {
        if (!newRecords || newRecords.length === 0) return;

        const formId = newRecords[0]['form-id'];

        newRecords.forEach((rec) => records.value.set(rec.id, rec));

        const currentUsers = recordsByForm.value.get(formId) || [];

        const updatedUsers = [...currentUsers];

        newRecords.forEach((newRecord) => {
            const existingIndex = updatedUsers.findIndex((m) => m.id === newRecord.id);
            if (existingIndex > -1) {
                updatedUsers[existingIndex] = newRecord;
            } else {
                updatedUsers.push(newRecord);
            }
        });


        recordsByForm.value.set(formId, updatedUsers);
    }

    function clearRecords() {
        records.value = new Map();
        recordsByForm.value = new Map();
        paginationByForm.value = new Map();
    }

    const all = computed(() => {
        void records.value.size;
        return  [...records.value.values()];
    });

    function byId(id) {
        void records.value.size;
        return records.value.get(String(id));
    }

    function byFormId(formId) {
        return recordsByForm.value.get(formId) || [];
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
            const { data, meta } = await api.fetch(`checkin-forms/${formId}/related-users`, {
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
            console.error(`Error while fetching related users for form with id: ${formId}`, err);
            errors.value = err;
        } finally {
            isLoading.value = false;
            isLoadingMore.value = false;
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
        const record = records.value.get(relatedUserId);
        if (!record) return;

        const formId = record['form-id'];
        records.value.delete(String(relatedUserId));
        if (recordsByForm.value.has(formId)) {
            const users = recordsByForm.value.get(formId);
            const filtered = users.filter((m) => m.id !== relatedUserId);
            recordsByForm.value.set(formId, filtered);
        }
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

    async function updateRecord(relatedUserId, relatedUserData) {
        isLoading.value = true;
        try {
            const { data } = await api.patch('checkin-related-users', relatedUserData);
            data.id = relatedUserId;
            storeRecord(data);
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
        records,
        recordsByForm,
        storeRecord,
        clearRecords,
        all,
        byId,
        byFormId,
        fetchByFormId,
        fetchAll,
        fetchById,
        removeRecord,
        updateRecord,
        createRecord,
        isLoading,
        isLoadingMore,
        errors,
        getPaginationForForm,
    };
});
