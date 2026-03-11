<template>
    <form class="default checkin-user-filter-form" @submit="submitUserFilter">
        <template v-for="(field, index) in allAppliedFields" :key="field.attributes.id">
            <p v-if="index >= 1" class="user-filter-fields-divider">
                {{ $gettext('und') }}
            </p>
            <section class="user-filter-fields-container">
                <select
                    v-if="allAvailableFields.length > 0"
                    v-model="field.attributes.type"
                    @change="applyFieldAttributes(field.attributes.type, field)"
                    :aria-label="$gettext('Feldname')"
                >
                    <option
                        v-for="(fData, fIndex) in allAvailableFields"
                        :key="fIndex"
                        :value="fData.typeparam !== null ? fData.type + '_' + fData.typeparam : fData.type"
                    >
                        {{ fData.name }}
                    </option>
                </select>
                <select
                    v-if="hasMultipleCompareOps(field.attributes.type)"
                    v-model="field.attributes['compare-operator']"
                    :aria-label="$gettext('Vergleichsoperator')"
                >
                    <option v-for="(name, op) in getConfigTypeCompareOps(field.attributes.type)" :key="op" :value="op">
                        {{ name }}
                    </option>
                </select>
                <select
                    v-if="hasMultipleValues(field.attributes.type)"
                    v-model="field.attributes.value"
                    :aria-label="$gettext('Wert')"
                >
                    <option
                        v-for="(name, value) in getConfigTypeValues(field.attributes.type)"
                        :key="value"
                        :value="value"
                    >
                        {{ name }}
                    </option>
                </select>
                <button
                    v-if="field.attributes.type"
                    class="user-filter-fields-button"
                    :title="$gettext('Dieses Feld löschen')"
                    @click="userFilterStore.removeField(field.attributes.id)"
                >
                    <StudipIcon :size="24" shape="trash" />
                </button>
            </section>
        </template>
        <section>
            <button class="button add" @click.prevent="addField">
                {{ $gettext('Feld hinzufügen') }}
            </button>
        </section>
    </form>
    <div class="user-filter-fields-actions">
        <button class="button accept" @click.prevent="submitUserFilter">
            {{ $gettext('Speichern') }}
        </button>
        <button class="button cancel" @click.prevent="cancel">
            {{ $gettext('Abbrechen') }}
        </button>
    </div>
</template>

<script setup>
import StudipIcon from '@/components/studip/StudipIcon.vue';
import { useUserFilterStore } from '@/store/user-filter';
import { useDrawerStore } from '@/store/drawer';
import { storeToRefs } from 'pinia';

const drawerStore = useDrawerStore();
const userFilterStore = useUserFilterStore();
const { allAvailableFields, allAppliedFields, errors } = storeToRefs(userFilterStore);

const props = defineProps({
    filterId: String,
});

const emit = defineEmits(['close']);

const addField = () => {
    userFilterStore.addEmptyField();
};

const applyFieldAttributes = (type, field) => {
    const config = userFilterStore.findConfigByType(type);
    if (config) {
        field.attributes.type = config.type;
        field.attributes.realtype = config.realtype;
        field.attributes.typeparam = config.typeparam;
        field.attributes['compare-operator'] = Object.keys(config['valid-compare-operators'])[0];
        field.attributes.value = Object.keys(config['valid-values'])[0];
    }
};

const getConfigTypeValues = (type) => {
    const config = userFilterStore.findConfigByType(type);
    if (config && config?.['valid-values']) {
        return config['valid-values'];
    }
    return {};
};

const hasMultipleValues = (type) => {
    const values = getConfigTypeValues(type);
    return Object.keys(values).length > 1;
};

const getConfigTypeCompareOps = (type) => {
    const config = userFilterStore.findConfigByType(type);
    if (config && config?.['valid-compare-operators']) {
        return config['valid-compare-operators'];
    }
    return {};
};

const hasMultipleCompareOps = (type) => {
    const compOps = getConfigTypeValues(type);
    return Object.keys(compOps).length > 1;
};

const cancel = async () => {
    if (props.filterId) {
        await userFilterStore.populateAppliedFieldsByFilterId(props.filterId);
    } else {
        userFilterStore.clearAppliedFields();
    }
    drawerStore.closeDrawer();
};

const submitUserFilter = async () => {
    await userFilterStore.applyUserFilter(allAppliedFields.value);
    if (errors.value) {
        // Display Error!
        return;
    }
    // Display success!
    drawerStore.closeDrawer();
};
</script>

<style lang="scss">
.user-filter-fields-divider {
    margin: 10px 0;
}
.user-filter-fields-button {
    color: var(--base-color);
    border: none;
    cursor: pointer;
    background-color: transparent;
    height: 28px;
}
form.default section.user-filter-fields-container {
    width: 100;
    display: flex;
    justify-items: center;
    align-items: center;
    gap: 15px;
    padding-top: 0;

    .icon-shape-trash {
        flex-shrink: 0;
    }
}
.user-filter-fields-actions {
    margin-top: 20px;
    background-color: var(--color--fieldset-header);
    padding: 0px 10px;
}
</style>
