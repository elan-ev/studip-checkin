<template>
    <div class="form-settings">
        <form class="default">
            <fieldset>
                <label>
                    {{ $gettext('Startet am') }}
                    <input type="datetime-local" name="start-date" v-mode="form['start-date']">
                    {{ $gettext('Endet am') }}
                    <input type="datetime-local" name="end-date" v-mode="form['end-date']">
                </label>
                <label>
                    <button class="button add" @click.prevent="() => openUserFilterDialog = true">
                        {{ $gettext('User Filter hinzufügen') }}
                    </button>
                </label>
                <label>
                    {{ $gettext('Titel') }}
                    <input type="text" id="form-title" v-model="form.name">
                </label>
                <label>
                    {{ $gettext('Beschreibung') }}
                    <textarea name="description" v-model="formBuilderStore.form.description"/>
                </label>
            </fieldset>

            <footer>
                <button class="button accept" @click.prevent="saveForm">
                    {{ $gettext('Speichern') }}
                </button>
                <button class="button cancel" @click.prevent="cancelChanges">
                    {{ $gettext('Abbrechen') }}
                </button>
            </footer>
        </form>
    </div>
</template>

<script setup>
    import { ref, defineEmits } from 'vue';
    import { storeToRefs } from 'pinia';
    import { useFormBuilderStore } from '@/store/form-builder';
    const formBuilderStore = useFormBuilderStore();
    const emit = defineEmits(['save', 'cancel']);

    const { form } = storeToRefs(formBuilderStore);
    console.log("🚀 ~ form:", form)

    const openUserFilterDialog = ref(false);

    const saveForm = () => {
        // Do whatever needs to be done before save.
        // ...
        emit('save');
    };

    const cancelChanges = () => {
        // Do whatever needs to be done before cancel.
        // ...
        emit('cancel');
    };

</script>

<style>
    .form-settings {
        flex-basis: 25%;
        background-color: lightcoral;
    }
</style>
