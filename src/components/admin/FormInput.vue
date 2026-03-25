<template>
    <legend class="checkin-movable">
        <span>{{ element.displayName }}</span>
        <button
            v-if="index > 0"
            class="button-undecorated"
            @click.prevent="moveUp()"
            :title="$gettext('Nach oben bewegen')"
        >
            <StudipIcon shape="arr_2up" :size="20" />
        </button>
        <button
            v-if="index < formBuilderStore.form.structure.length - 1"
            class="button-undecorated"
            @click.prevent="moveDown()"
            :title="$gettext('Nach unten bewegen')"
        >
            <StudipIcon shape="arr_2down" :size="20" />
        </button>
        <button class="button-undecorated" :title="$gettext('Löschen')" @click.prevent.stop="emit('delete')">
            <StudipIcon shape="trash" :size="20" />
        </button>
    </legend>
    <div class="lang-wrapper">
        <div v-for="lang in ['de', 'en']" :key="lang" class="checkin-lang-group">
            <h3 class="checkin-lang-group-header">{{ lang.toUpperCase() }}</h3>
            <label>
                {{ $gettext('Beschriftung') }}
                <input type="text" v-model="element.payload.label[lang]" />
            </label>
            <label>
                {{ $gettext('Platzhalter') }}
                <input type="text" v-model="element.payload.placeholder[lang]" />
            </label>
            <section v-if="hasOptions">
                <h4>{{ $gettext('Optionen') }}</h4>
                <label v-for="(option, optIndex) in element.payload.options" :key="optIndex">
                    {{ $gettext('Wert der Option') }}
                    <div class="option-div">
                        <input type="text" v-model="element.payload.options[optIndex].text[lang]" />
                        <button
                            class="delete-option"
                            @click="removeOption(optIndex)"
                            :title="$gettext('Option Löschen')"
                        >
                            <StudipIcon shape="trash" :size="20" />
                        </button>
                    </div>
                </label>
            </section>
        </div>
    </div>
    <button v-if="hasOptions" class="button add" @click.prevent="addNewOption">
        {{ $gettext('Neue Option hinzufügen') }}
    </button>
    <section v-if="hasMinMax">
        <label>
            {{ $gettext('Minimum') }}
            <input type="number" v-model="element.payload.min" />
        </label>
        <label>
            {{ $gettext('Maximum') }}
            <input type="number" v-model="element.payload.max" />
        </label>
    </section>

    <label>
        {{ $gettext('Pflichtfeld') }}
        <input type="checkbox" v-model="element.payload.required" />
    </label>
    <section v-if="hasCondition" class="checkin-form-input-condition">
        <h3>
            {{ $gettext('Bedingung') }}
            <button
                class="button-undecorated"
                :title="$gettext('Bedingung entfernen')"
                @click.prevent="removeCondition"
            >
                <StudipIcon shape="trash" :size="20" />
            </button>
        </h3>
        <label>
            {{ $gettext('Ziel') }}
            <select v-model="element.payload['condition-target']">
                <template v-for="(storeElement, storeElementIndex) in storeElements" :key="element.id">
                    <option
                        v-if="storeElement.id !== element.id"
                        :value="storeElement.id"
                        :disabled="storeElementIndex > index"
                    >
                        {{ storeElement.payload.label }} ({{ storeElement.displayName }})
                    </option>
                </template>
            </select>
            <div v-if="targetElement" class="condition-value-config">
                <h4>
                    {{ $gettext('Wert muss sein') }}
                </h4>
                <component
                    :is="inputComponentForTarget"
                    v-model="element.payload['condition-value']"
                    :element="targetElement"
                />
            </div>
        </label>
    </section>
    <button v-else class="button add" @click.prevent="addCondition">
        {{ $gettext('Bedingung hinzufügen') }}
    </button>
</template>
<script setup>
import StudipIcon from '@/components/studip/StudipIcon.vue';
import { computed, capitalize, getCurrentInstance, onMounted } from 'vue';
import { useFormBuilderStore } from '@/store/form-builder';

import FormInputText from '@/components/shared/inputs/FormInputText.vue';
import FormInputTextarea from '@/components/shared/inputs/FormInputTextarea.vue';
import FormInputSelect from '@/components/shared/inputs/FormInputSelect.vue';
import FormInputSwitch from '@/components/shared/inputs/FormInputSwitch.vue';
import FormInputRadioGroup from '@/components/shared/inputs/FormInputRadioGroup.vue';
import FormInputCheckboxGroup from '@/components/shared/inputs/FormInputCheckboxGroup.vue';

const { proxy } = getCurrentInstance();
const formBuilderStore = useFormBuilderStore();
const emit = defineEmits(['delete']);

const props = defineProps({
    element: Object,
    index: String,
});

const hasOptions = computed(() => {
    const withOptions = ['radio', 'select', 'multiselect'];
    return withOptions.includes(props.element.type);
});

const hasMinMax = computed(() => {
    const withMinMax = ['number'];
    return withMinMax.includes(props.element.type);
});

const hasCondition = computed(() => {
    return props.element.payload['has-conditions'] ?? false;
});

const targetElement = computed(() => {
    const targetId = props.element.payload['condition-target'];
    return storeElements.value.find((el) => el.id === targetId);
});

const inputComponentForTarget = computed(() => {
    if (!targetElement.value) return null;

    const map = {
        text: FormInputText,
        email: FormInputText,
        url: FormInputText,
        textarea: FormInputTextarea,
        select: FormInputSelect,
        switch: FormInputSwitch,
        radio: FormInputRadioGroup,
        multiselect: FormInputCheckboxGroup,
    };

    return map[targetElement.value.type];
});

const storeElements = computed(() => {
    return formBuilderStore.elements;
});

const addCondition = () => {
    props.element.payload['has-conditions'] = true;
};

const removeCondition = () => {
    props.element.payload['has-conditions'] = false;
    props.element.payload['condition-target'] = null;
    props.element.payload['condition-value'] = null;
};

const moveUp = () => {
    // TODO checks for errors
    if (props.index > 0) {
        const newIndex = props.index - 1;
        formBuilderStore.changeElementPosition(props.index, newIndex, props.element);
    } else {
        STUDIP.Report.error(proxy.$gettext('Ungültige Position für das Element'));
    }
};

const moveDown = () => {
    // TODO checks for errors
    if (props.index < formBuilderStore.form.structure.length - 1) {
        const newIndex = props.index + 1;
        formBuilderStore.changeElementPosition(props.index, newIndex, props.element);
    } else {
        STUDIP.Report.error(proxy.$gettext('Ungültige Position für das Element'));
    }
};

const addNewOption = () => {
    let hasEmptyItem = props.element.payload.options.filter((item) => item.text === '');
    if (hasEmptyItem.length) {
        STUDIP.Report.warning(proxy.$gettext('Es ist eine leer Option ist vorhanden.'));
        return;
    }
    props.element.payload.options.push({
        text: {
            de: '',
            en: '',
        },
    });
};

const removeOption = (index) => {
    props.element.payload.options.splice(index, 1);
};

onMounted(() => {
    if (hasOptions.value && props.element.payload.options.length === 0) {
        addNewOption();
    }
});
</script>
<style lang="scss" scoped>
.lang-wrapper {
    display: flex;
    gap: 1.5rem;
    align-items: flex-start;
    margin-bottom: 1rem;

    .checkin-lang-group {
        flex: 1; 
        min-width: 0;
        border: 1px solid var(--color--tile-border);
        padding: 1rem;

        .checkin-lang-group-header {
            font-weight: 700;
            margin: 0 0 1em 0;
        }
    }
}
.delete-option {
    background: none;
    border: none;
    cursor: pointer;
    padding: 10px 20px;

    .studip-icon {
        color: var(--color--highlight, var(--base-color));
        vertical-align: middle;
    }
}
.option-div {
    display: flex;
    gap: 0.5rem;
    justify-content: start;
    align-items: center;
}
.checkin-movable {
    display: flex;
    align-items: center;
    span {
        flex-grow: 1;
    }
    button {
        align-self: flex-end;
        margin-left: 5px;
    }
}
.floating-checkbox {
    label {
        display: inline !important;
        padding-left: 10px;
    }
}
.checkin-form-input-condition {
    .button-undecorated {
        vertical-align: sub;
    }
}
</style>
