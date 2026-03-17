<template>
    <div class="form-editor">
        <form class="default collapsable">
            <template v-if="formBuilderStore.form?.structure?.length">
                <template v-for="(element, index) in formBuilderStore.form.structure" :key="element.id">
                    <fieldset>
                        <FormInput :element="element" :index="index" @delete="deleteElement(index)"/>
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
            proxy.$gettext('Möchten Sie dieses Feld wirklich löschen?'),
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
    border: 1px solid var(--color--tile-border);;
    border-radius: 4px;
    box-sizing: border-box;
}
</style>
