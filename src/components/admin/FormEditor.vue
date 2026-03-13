<template>
    <div class="form-editor">
        <form class="default collapsable">
            <template v-if="formBuilderStore.form?.structure?.length">
                <template v-for="(element, index) in formBuilderStore.form.structure" :key="element.id">
                    <fieldset>
                        <div class="form-editor-fieldset-delete">
                            <button class="button trash" @click.prevent="deleteElement(index)">
                                {{ $gettext('Entfernen') }}
                            </button>
                        </div>
                        <FormInput :element="element" :index="index" />
                    </fieldset>
                    <FormEditorPlusButton
                        v-if="index < formBuilderStore.form.structure.length - 1"
                        :index="index + 1"
                        @startAddingElement="addElementHere"
                    />
                </template>
            </template>
            <div v-else class="form-editor-empty-state">
                {{ $gettext('Bitte wählen Sie ein Element aus der Liste aus.') }}
            </div>
        </form>
    </div>
</template>

<script setup>
import { getCurrentInstance } from 'vue';
import { useFormBuilderStore } from '@/store/form-builder';
import FormEditorPlusButton from './FormEditorPlusButton.vue';
import FormInput from './FormInput.vue';

const { proxy } = getCurrentInstance();
const formBuilderStore = useFormBuilderStore();
const emit = defineEmits(['addElement']);

const addElementHere = (index) => {
    emit('requestAddElement', index);
};

const deleteElement = (index) => {
    if (
        STUDIP.Dialog.confirm(
            proxy.$gettext('Are you sure you want to delete the input?'),
            () => {
                formBuilderStore.removeElementFromStructure(index);
            },
            STUDIP.Dialog.close(),
        )
    );
};
</script>

<style lang="scss">
.form-editor {
    flex: 1;
    height: 100%;
    background-color: var(--color--global-background);
    overflow-y: auto;
    overflow-x: hidden;

    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
}
</style>
