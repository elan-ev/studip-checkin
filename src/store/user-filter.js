import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from './api/kitsu-api.js';

export const useUserFilterStore = defineStore('userFilterStore', () => {
    const availableFields = ref(new Map());
    const fields = ref(new Map());
    const isLoading = ref(false);
    const errors = ref(false);

    function storeAppliedField(field) {
        fields.value.set(String(field.attributes.id), field);
    }

    function clearAppliedFields() {
        fields.value = new Map();
    }

    const allAppliedFields = computed(() => {
        void fields.value.size;
        return  [...fields.value.values()];
    });

    function storeAvailableField(field) {
        availableFields.value.set(String(field.type), field);
    }

    function clearAvailableFields() {
        availableFields.value = new Map();
    }

    const allAvailableFields = computed(() => {
        void availableFields.value.size;
        return  [...availableFields.value.values()];
    });

    /**
     * Fetches the Checkin User Filter Fields.
     * @param {string} context
     * @param {string} target
     */
    async function fetchAvailableFields(context = 'StudipCheckin', target = '') {
        isLoading.value = true;
        errors.value = false;
        const filter = {
            context,
            target,
        };
        try {
            const { data } = await api.get('checkin-user-filter-fields', filter);
            clearAvailableFields(); // Clear existing fields before storing new ones.
            data.forEach((field => {
                storeAvailableField(field);
            }))
        } catch (err) {
            console.error('Error while fetching user filter fields', err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    /**
     * Calls the backend to apply for the user filter with given filters/fields.
     * The purpose of this function is to make sure that the provided filters/fields are valid.
     * NOTE: This function does not save the user filter at this point only return the accepted fields to be applied later on when saving forms.
     * @param {*} filters the array of user filters/fields to be applied:
     *           possible value for filters array:
     *             [{
     *                 attributes: {
     *                    type: '<filter_type>', // e.g. UserFilterFields\\StudipCheckin\\StudipCheckinGenderFilter
     *                    compare-operator: '=',
     *                   value: '<value>'
     *                 }
     *             }]
     */
    async function applyUserFilter(filters) {
        isLoading.value = true;
        errors.value = false;
        try {
            const { data } = await api.post('checkin-user-filters', { filters: filters });
            if (data?.fields) {
                clearAppliedFields();
                for (const fieldId in data.fields) {
                    storeAppliedField(data.fields[fieldId]);
                }
            }
        } catch (err) {
            console.error('Error while applying user filter', err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    async function populateAppliedFieldsByFilterId(filterId) {
        isLoading.value = true;
        errors.value = false;
        try {
            const { data } = await api.get(`user-filters/${filterId}`);
            if (data?.fields) {
                clearAppliedFields();
                for (const fieldId in data.fields) {
                    storeAppliedField(data.fields[fieldId]);
                }
            }
        } catch (err) {
            console.error('Error while fetching user filter', err);
            errors.value = err;
        } finally {
            isLoading.value = false;
        }
    }

    function addEmptyField() {
        const hasNew = allAppliedFields.value.some(field => field.attributes.type === null);
        if (hasNew) {
            return;
        }
        const emptyField = {
            attributes: {
                id: `new-${fields.value.size}`,
                type: null,
                typeparam: null,
                'compare-operator': '',
                value: ''
            }
        };
        storeAppliedField(emptyField);
    }

    function findConfigByType(type) {
        void availableFields.value.size;
        return availableFields.value.get(String(type));
    }

    function removeField(id) {
        fields.value.delete(String(id));
    }

    function reset() {
        clearAppliedFields();
        isLoading.value = false;
        errors.value = false;
    }

    return {
        isLoading,
        errors,
        fields,
        allAppliedFields,
        availableFields,
        allAvailableFields,
        fetchAvailableFields,
        applyUserFilter,
        populateAppliedFieldsByFilterId,
        addEmptyField,
        findConfigByType,
        clearAppliedFields,
        removeField,
        reset,
    };
});
