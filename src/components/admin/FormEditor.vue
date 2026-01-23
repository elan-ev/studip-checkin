<template>
    <div class="form-editor">
        <form class="default collapsable">
            <template v-for="(element, index) in formBuilderStore.form.structure" :key="index"
                v-if="formBuilderStore.form?.structure?.length"
            >
                <fieldset>
                    <legend>{{ capitalize($gettext(element.type)) }}</legend>
                    <label>
                        <span class="required">
                            {{ $gettext('Name') }}
                        </span>
                        <input
                            type="text"
                            v-model="element.name"
                        />
                    </label>
                </fieldset>
                <FormEditorPlusButton
                    :index="index"
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
    import { defineEmits, capitalize, watch, watchEffect } from 'vue';
    import { useFormBuilderStore } from '@/store/form-builder';
    import FormEditorPlusButton from './FormEditorPlusButton.vue';
    const formBuilderStore = useFormBuilderStore();
    const emit = defineEmits(['addElement']);

    const addElementHere = (index) => {
        emit('requestAddElement', index);
    };

    
</script>

<style>
    .form-editor {
        flex: 1;
        background-color: aqua;
    }
</style>
