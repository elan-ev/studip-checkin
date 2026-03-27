<template>
    <div class="form-settings">
        <form class="default form-settings-form">
            <label>
                {{ $gettext('Titel') }}
                <input type="text" id="form-title" v-model="form.name" />
            </label>
            <label>
                {{ $gettext('Beschreibung') }}
                <textarea name="description" v-model="form.description" />
            </label>
            <label>
                {{ $gettext('Startet am') }}
                <div class="date-container">
                    <input type="date" name="start-date" v-model="form['start-date']" :max="form['end-date']" />
                    <button
                        v-if="form?.['start-date']"
                        class="button-undecorated"
                        :title="$gettext('Startdatum löschen')"
                        @click="emptyFormDate('start-date')"
                    >
                        <StudipIcon shape="trash" :size="20" />
                    </button>
                </div>
                <br />
                {{ $gettext('Endet am') }}
                <div class="date-container">
                    <input type="date" name="end-date" v-model="form['end-date']" :min="form['start-date']" />
                    <button
                        v-if="form?.['end-date']"
                        class="button-undecorated"
                        :title="$gettext('Enddatum löschen')"
                        @click="emptyFormDate('end-date')"
                    >
                        <StudipIcon shape="trash" :size="20" />
                    </button>
                </div>
            </label>
            <div v-if="!hasUserFilter" class="messagebox messagebox_warning">
                {{ $gettext('Es wurde noch kein Zielgruppenfilter gesetzt') }}
            </div>
            <div v-else class="messagebox messagebox_info">
                {{
                    $ngettext(
                        '%{counter} Zielgruppenfilter wurde gesetzt',
                        '%{counter} Zielgruppenfilter wurden gesetzt',
                        userFiltersCounter,
                        { counter: userFiltersCounter },
                    )
                }}
            </div>
            <label>
                <button class="button" :class="{'add':  !hasUserFilter, 'edit': hasUserFilter}" @click.prevent="openUserFilterDrawer">
                    {{ hasUserFilter ?  $gettext('Zielgruppenfilter ändern') : $gettext('Zielgruppenfilter hinzufügen') }}
                </button>
            </label>
        </form>
        <div class="form-settings-actions">
            <button class="button accept" @click.prevent="saveForm">
                {{ $gettext('Speichern') }}
            </button>
            <button class="button cancel" @click.prevent="cancelChanges">
                {{ $gettext('Abbrechen') }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed} from 'vue';
import { useGettext } from 'vue3-gettext';
import { storeToRefs } from 'pinia';
import { useFormBuilderStore } from '@/store/form-builder';
import { useDrawerStore } from '@/store/drawer';
import { useUserFilterStore } from '@/store/user-filter';
import StudipIcon from '@/components/studip/StudipIcon.vue';

const { $gettext } = useGettext();
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

const userFiltersCounter = computed(() => {
    return allAppliedFields.value.length;
});

const hasUserFilter = computed(() => {
    return userFiltersCounter.value > 0;
});

const saveForm = () => {
    // Do whatever needs to be done before save.
    if (formValidation()) {
        const formData = prepareFormData();
        emit('save', formData);
    } else {
        STUDIP.Report.error($gettext('Etwas fehlt!'));
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
        !allAppliedFields.value ||
        allAppliedFields.value?.length === 0 ||
        !isDateRangeValid.value
    ) {
        return false;
    }
    return true;
};

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
console.log(formData);
    return formData;
};

const openUserFilterDrawer = () => {
    drawerStore.openUserFilterConfigInDrawer(form.value['filter-id']);
};

const emptyFormDate = (dateProp) => {
    if (form.value?.[dateProp]) {
        form.value[dateProp] = null;
    }
};
</script>

<style lang="scss">
.form-settings {
    flex-basis: 25%;
    max-width: 405px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border: none;

    .form-settings-form {
        padding: 10px;
    }

    .form-settings-actions {
        display: flex;
        flex-direction: row;
        justify-content: center;
        margin-bottom: -10px;
    }
}
.date-container {
    display: flex;
    gap: 10px;
    justify-content: space-between;
    align-items: center;

    .icon-shape-trash {
        flex-shrink: 0;
    }
}
</style>
