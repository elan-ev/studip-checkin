<template>
    <div class="form-editor">
        <form class="default collapsable">
            <template v-for="(element, index) in formBuilderStore.form.structure" :key="index"
                v-if="formBuilderStore.form?.structure?.length"
            >
                <fieldset>
                    <!-- TODO: This could also be in the FormInput component, we have to decide! -->
                    <div class="form-editor-fieldset-delete">
                        <button class="button trash" @click.prevent="deleteElement(index)">
                            {{ $gettext('Entfernen') }}
                        </button>
                    </div>
                    <FormInput :element="element" :index="index"/>
                </fieldset>
                <FormEditorPlusButton
                    :index="(index + 1)"
                    @startAddingElement="addElementHere"
                />
            </template>
            <FormEditorPlusButton
                v-else
                :index="0"
                @startAddingElement="addElementHere"
            />
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
        if (STUDIP.Dialog.confirm(
                proxy.$gettext('Are you sure you want to delete the input?'),
                () => {
                    formBuilderStore.removeElementFromStructure(index);
                },
                STUDIP.Dialog.close()
            )
        );
    };
</script>

<style lang="scss">
    .form-editor {
        flex: 1;
        background-color: aqua;
    }
</style>
