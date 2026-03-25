import { ref, computed, toRaw } from 'vue';
import { defineStore } from 'pinia';

export const useFormBuilderStore = defineStore('formBuilderStore', () => {
    const form = ref(null);
    const pendingElementIndex = ref(null);
    const locales = ['de', 'en'];

    const elements = computed(() => {
        return form.value.structure || [];
    });
    function normalizeField(field) {
        if (typeof field === 'string' || !field) {
            const val = field || '';
            return { de: val, en: val };
        }
        locales.forEach((l) => {
            if (!(l in field)) field[l] = '';
        });
        return field;
    }

    function normalizeStructure(structure) {
        return structure.map((el) => {
            if (!el.payload) el.payload = {};

            el.payload.label = normalizeField(el.payload.label);
            el.payload.placeholder = normalizeField(el.payload.placeholder);

            if (el.payload.options && Array.isArray(el.payload.options)) {
                el.payload.options = el.payload.options.map((opt) => ({
                    ...opt,
                    text: normalizeField(opt.text),
                }));
            }
            return el;
        });
    }

    function initEmpty() {
        form.value = {
            'end-date': '',
            'start-date': '',
            'filter-id': '',
            name: '',
            structure: [],
            version: 1,
        };
    }

    async function initFromExisting(existingForm) {
        const clonedRawForm = structuredClone(toRaw(existingForm));
        if (clonedRawForm.structure) {
            clonedRawForm.structure = normalizeStructure(clonedRawForm.structure);
        }
        form.value = clonedRawForm;
        return true;
    }

    function addElementToStructure(index, element) {
        form.value.structure.splice(index, 0, element);
    }

    function removeElementFromStructure(index) {
        form.value.structure.splice(index, 1);
    }

    function reset() {
        initEmpty();
    }

    function preparePendingElement(index) {
        pendingElementIndex.value = index;
    }

    function finishPendingElement(element) {
        const index = pendingElementIndex.value ?? elements.value.length;
        const clonedRawElement = structuredClone(toRaw(element));
        if (!clonedRawElement.payload) {
            clonedRawElement.payload = {};
        }
        clonedRawElement.payload.label = normalizeField(clonedRawElement.payload.label);
        clonedRawElement.payload.placeholder = normalizeField(clonedRawElement.payload.placeholder);

        addElementToStructure(index, clonedRawElement);

        pendingElementIndex.value = null;
    }

    function changeElementPosition(oldIndex, newIndex, element) {
        form.value.structure.splice(oldIndex, 1);
        form.value.structure.splice(newIndex, 0, element);
    }

    return {
        form,
        elements,
        pendingElementIndex,
        initEmpty,
        reset,
        initFromExisting,
        addElementToStructure,
        removeElementFromStructure,
        preparePendingElement,
        finishPendingElement,
        changeElementPosition,
    };
});
