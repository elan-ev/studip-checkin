<template>
    <div class="form-settings">
        <form class="default form-settings-form">
            <fieldset>
                <legend>{{ $gettext('DE') }}</legend>
                <label>
                    <span class="required">{{ $gettext('Titel') }}</span>
                    <input type="text" id="form-title-de" v-model="form.name['de']" required />
                </label>
                <label>
                    {{ $gettext('Beschreibung') }}
                    <textarea
                        v-model="form.description['de']"
                        name="description-de"
                        class="form-settings-description"
                    />
                </label>
            </fieldset>
            <fieldset>
                <legend>{{ $gettext('EN') }}</legend>
                <label>
                    <span class="required">{{ $gettext('Titel') }}</span>
                    <input type="text" id="form-title-en" v-model="form.name['en']" required />
                </label>
                <label>
                    {{ $gettext('Beschreibung') }}
                    <textarea
                        v-model="form.description['en']"
                        name="description-en"
                        class="form-settings-description"
                    />
                </label>
            </fieldset>
            <label>
                <span class="required">{{ $gettext('Startet am') }}</span>
                <div class="date-container">
                    <input
                        type="date"
                        name="start-date"
                        v-model="form['start-date']"
                        :max="form['end-date']"
                        required
                    />
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
                <span class="required">{{ $gettext('Endet am') }}</span>
                <div class="date-container">
                    <input type="date" name="end-date" v-model="form['end-date']" :min="form['start-date']" required />
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
                <button
                    class="button"
                    :class="{ add: !hasUserFilter, edit: hasUserFilter }"
                    @click.prevent="openUserFilterDrawer"
                >
                    {{
                        hasUserFilter ? $gettext('Zielgruppenfilter ändern') : $gettext('Zielgruppenfilter hinzufügen')
                    }}
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
import { computed, ref } from 'vue';
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
const { form } = storeToRefs(formBuilderStore);

const emit = defineEmits(['save', 'cancel']);

const validationErrors = ref({
    name: false,
    structure: false,
    filters: false,
    startDate: false,
    endDate: false,
    dateRange: false,
});

const hasValidationErrors = computed(() => {
    return Object.values(validationErrors.value).includes(true);
});

const activeErrorMessages = computed(() => {
    const messages = [];
    if (hasValidationErrors.value) {
        if (validationErrors.value.name) messages.push($gettext('Bitte geben Sie einen Titel an.'));
        if (validationErrors.value.structure)
            messages.push($gettext('Bitte fügen Sie mindestens ein Formularelement ein.'));
        if (validationErrors.value.filters)
            messages.push($gettext('Bitte wählen Sie mindestens einen Zielgruppenfilter aus.'));
        if (validationErrors.value.startDate) messages.push($gettext('Bitte geben Sie ein Startdatum an.'));
        if (validationErrors.value.endDate) messages.push($gettext('Bitte geben Sie ein Enddatum an.'));
        if (validationErrors.value.dateRange) messages.push($gettext('Bitte prüfen Sie Start- und Enddatum.'));
    }
    return messages;
});

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
    formValidation();
    if (!hasValidationErrors.value) {
        const formData = prepareFormData();
        emit('save', formData);
    } else {
        const errorContent = '<ul><li>' + activeErrorMessages.value.join('</li><li>') + '</li></ul>';
        STUDIP.Report.warning($gettext('Bitte überprüfen Sie Ihre Eingaben.'), errorContent);
        console.error(validationErrors.value);
    }
};

const cancelChanges = () => {
    // Do whatever needs to be done before cancel.
    // ...
    emit('cancel');
};

const formValidation = () => {
    validationErrors.value.name = !form.value.name['de'] || !form.value.name['en'];
    validationErrors.value.structure = !form.value.structure || form.value.structure.length === 0;
    validationErrors.value.filters = !allAppliedFields.value || allAppliedFields.value?.length === 0;
    validationErrors.value.startDate = !form.value['start-date'];
    validationErrors.value.endDate = !form.value['end-date'];
    validationErrors.value.dateRange = !isDateRangeValid.value;
};

const prepareFormData = () => {
    const formData = {};
    if (form.value?.id) {
        formData.id = form.value.id;
    }
    formData.name = form.value?.name ?? { de: '', en: '' };
    formData.description = form.value?.description ?? { de: '', en: '' };
    formData.structure = form.value?.structure ?? [];
    formData['start-date'] = form.value?.['start-date'] ?? null;
    formData['end-date'] = form.value?.['end-date'] ?? null;
    formData['filter-fields'] = allAppliedFields.value ?? [];

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
        .form-settings-description {
            resize: vertical;
            min-height: 160px;
        }
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
