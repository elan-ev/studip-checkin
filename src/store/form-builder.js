import { ref, computed, toRaw } from 'vue';
import { defineStore } from 'pinia';

export const useFormBuilderStore = defineStore('formBuilderStore', () => {
    const form = ref(null);
    const isAdding = ref(false);
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

    function initFromExisting(existingForm) {
        const clonedRawForm = structuredClone(toRaw(existingForm));
        form.value = clonedRawForm;
    }

    function addElementToStructure(index, element) {
        form.value.structure.splice(index, 0, element);
    }

    function removeElementFromStructure(index) {
        form.value.structure.splice(index, 1);
    }

    function reset() {
        form.value = null;
    }

    function preparePendingElement(index) {
        isAdding.value = true;
        pendingElementIndex.value = index;
    }

    function finishPendingElement(element) {
        if (pendingElementIndex.value !== null) {
            addElementToStructure(pendingElementIndex.value, element);
        }
        isAdding.value = false;
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
