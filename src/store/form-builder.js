import { ref, computed, toRaw } from 'vue';
import { defineStore } from 'pinia';

export const useFormBuilderStore = defineStore('formBuilderStore', () => {
    const form = ref(null);
    const pendingElementIndex = ref(null);

    const elements = computed(() => {
        return form.value.structure || [];
    });

    function initEmpty() {
        form.value = {
            "end-date": "",
            "start-date": "",
            "filter-id": "",
            "name": "",
            "structure": [],
            "version": 1,
        }
    }

    async function initFromExisting(existingForm) {
        const clonedRawForm = structuredClone(toRaw(existingForm));
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
        if (pendingElementIndex.value !== null) {
            addElementToStructure(pendingElementIndex.value, element);
        }
        pendingElementIndex.value = null;
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
    }
});
