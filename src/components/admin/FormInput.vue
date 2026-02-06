<template>
    <legend>{{ capitalize($gettext(element.type)) }}</legend>
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
</template>
<script setup>
    import StudipIcon from '@/components/studip/StudipIcon.vue';
    import { computed, capitalize, getCurrentInstance } from 'vue';

    const { proxy } = getCurrentInstance();

    const props = defineProps({
        element: Object,
    });

    const hasOptions = computed(() => {
        const withOptions = ['radio', 'select']
        if (withOptions.includes(props.element.type)) {
            return true;
        }
        return false;
    });

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
            color: var(--color--highlight);
            vertical-align: middle;
        }
    }
    .option-div {
        display: flex;
        gap: 0.5rem;
        justify-content: start;
        align-items: center;
    }
</style>
