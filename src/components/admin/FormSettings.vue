<template>
    <div class="form-settings">
        <form class="default">
            <fieldset>
                <label>
                    {{ $gettext('Startet am') }}
                    <div class="date-container">
                        <input type="date" name="start-date" v-model="form['start-date']" :max="form['end-date']">
                        <StudipIcon
                            v-if="form?.['start-date']"
                            :size="16"
                            shape="trash"
                            role="button"
                            :title="$gettext('Dieses Datum löschen')"
                            @click="emptyFormDate('start-date')"
                        />
                    </div>
                    <br>
                    {{ $gettext('Endet am') }}
                    <div class="date-container">
                        <input type="date" name="end-date" v-model="form['end-date']" :min="form['start-date']">
                        <StudipIcon
                            v-if="form?.['end-date']"
                            shape="trash"
                            role="button"
                            :title="$gettext('Dieses Datum löschen')"
                            @click="emptyFormDate('end-date')"
                        />
                    </div>
                </label>
                <label>
                    <button class="button add" @click.prevent="openUserFilterDrawer">
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
    import StudipIcon from '@/components/studip/StudipIcon.vue';
    import { ref, computed, getCurrentInstance } from 'vue';
    import { storeToRefs } from 'pinia';
    import { useFormBuilderStore } from '@/store/form-builder';
    import { useDrawerStore } from '@/store/drawer';
    import { useUserFilterStore } from '@/store/user-filter';

    const { proxy } = getCurrentInstance();
    const userFilterStore = useUserFilterStore();
    const formBuilderStore = useFormBuilderStore();
    const drawerStore = useDrawerStore();

    const { allAppliedFields } = storeToRefs(userFilterStore);

    const emit = defineEmits(['save', 'cancel']);

    const { form } = storeToRefs(formBuilderStore);
    const isDateRangeValid = computed(() => {
        if (!form.value?.['start-date'] || !form.value?.['end-date']) {
            return true;
        }

        return new Date(form.value['start-date']) <= new Date(form.value['end-date']);
    });

    const saveForm = () => {
        // Do whatever needs to be done before save.
        if (formValidation()) {
            const formData = prepareFormData();
            emit('save', formData);
        } else {
            STUDIP.Report.error(proxy.$gettext('Etwas Fehlt!!!'));
        }
    };

    const cancelChanges = () => {
        // Do whatever needs to be done before cancel.
        // ...
        emit('cancel');
    };

    const formValidation = () => {
        if (
            !form.value.name ||
            !form.value.structure ||
            !allAppliedFields.value || allAppliedFields.value?.length === 0 ||
            !isDateRangeValid.value
        ) {
            return false;
        }
        return true;
    }

    const prepareFormData = () => {
        const formData = {};
        if (form.value?.id) {
            formData.id = form.value.id;
        }
        formData.name = form.value?.name ?? '';
        formData.description = form.value?.description ?? '';
        formData.structure = form.value?.structure ?? [];
        formData['start-date'] = form.value?.['start-date'] ?? null;
        formData['end-date'] = form.value?.['end-date'] ?? null;
        formData['filter-fields'] = allAppliedFields.value ?? [];

        return formData;
    }

    const openUserFilterDrawer = () => {
        drawerStore.openUserFilterConfigInDrawer(form.value['filter-id']);
    }

    const emptyFormDate = (dateProp) => {
        if (form.value?.[dateProp]) {
            form.value[dateProp] = null;
        }
    }

</script>

<style lang="scss">
    .form-settings {
        flex-basis: 25%;
        background-color: lightcoral;
    }
    .date-container {
        display: flex;
        gap: 1rem;
        justify-content: space-between;
        align-items: center;
        .icon-shape-trash {
            flex-shrink: 0;
        }
    }
</style>
