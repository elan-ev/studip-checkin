<template>
    <legend class="checkin-movable">
        <span>{{ capitalize($gettext(element.type)) }}</span>
        <button
            v-if="index > 0"
            class="checkin-movable-up"
            @click.prevent="moveUp()"
            :title="$gettext('Nach oben bewegen')"
        >
            <StudipIcon shape="arr_2up" :size="16" />
        </button>
        <button
            v-if="index < formBuilderStore.form.structure.length - 1"
            class="checkin-movable-down"
            @click.prevent="moveDown()"
            :title="$gettext('Nach unten bewegen')"
        >
            <StudipIcon shape="arr_2down" :size="16" />
        </button>
    </legend>
    <label>
        {{ $gettext('Feld Text') }}
        <input type="text" v-model="element.payload.label">
    </label>
    <label>
        {{ $gettext('Platzhalter') }}
        <input type="text" v-model="element.payload.placeholder">
    </label>
    <section v-if="hasOptions">
        <h4>
            {{ $gettext('Optionen') }}
        </h4>
        <label v-for="(option, index) in element.payload.options" :key="index">
            {{ $gettext('Wert der Option') }}
            <div class="option-div">
                <input type="text" v-model="element.payload.options[index].text">
                <button
                    class="delete-option"
                    @click="removeOption(index)"
                    :title="$gettext('Option Löschen')"
                    :aria-label="$gettext('Option Löschen')"
                >
                    <StudipIcon shape="trash" :size="24" />
                </button>
            </div>
        </label>
        <button class="button add" @click.prevent="addNewOption">
            {{ $gettext('Neue Option hinzufügen') }}
        </button>
    </section>
    <div class="floatingCheckbox">
        <input type="checkbox" name="requiredField" v-model="element.payload.required">
        <label for="requiredField">{{ $gettext('Pflichtfeld') }}</label>
    </div>

</template>
<script setup>
    import StudipIcon from '@/components/studip/StudipIcon.vue';
    import { computed, capitalize, getCurrentInstance } from 'vue';
    import { useFormBuilderStore } from '@/store/form-builder';

    const { proxy } = getCurrentInstance();
    const formBuilderStore = useFormBuilderStore();

    const props = defineProps({
        element: Object,
        index: String,
    });

    const hasOptions = computed(() => {
        const withOptions = ['radio', 'select']
        if (withOptions.includes(props.element.type)) {
            return true;
        }
        return false;
    });

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
        let hasEmptyItem = props.element.payload.options.filter(item => item.text === '');
        if (hasEmptyItem.length) {
            STUDIP.Report.error(proxy.$gettext('Leer Option ist vorhanden!'));
            return;
        }
        props.element.payload.options.push({
            text: ''
        });
    }

    const removeOption = (index) => {
        props.element.payload.options.splice(index, 1);
    }

</script>
<style lang="scss" scoped>
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
</style>
