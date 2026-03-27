<template>
    <table v-if="!loading && records.size !== 1" class="default">
        <caption>
            {{
                $gettext('Bitte füllen Sie die folgenden Formulare aus')
            }}
        </caption>
        <thead>
            <tr>
                <th scope="col">{{ $gettext('Name') }}</th>
                <th scope="col" width="25%">{{ $gettext('Aktionen') }}</th>
            </tr>
        </thead>
        <tbody>
            <template v-if="formStore.all.length === 0">
                <tr>
                    <td colspan="8">{{ $gettext('Keine Formulare gefunden.') }}</td>
                </tr>
            </template>
            <template v-else>
                <tr v-for="form in formStore.all">
                    <td>
                        {{ form.name }}
                    </td>
                    <td>
                        <button class="button" @click="goToFormData(form.id)">
                            {{ $gettext('Formular ausfüllen') }}
                        </button>
                    </td>
                </tr>
            </template>
        </tbody>
        <footer>
            <button class="button" @click="goToStart"> {{ $gettext('Zur Startseite') }}</button>
        </footer>
    </table>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useGettext } from 'vue3-gettext';
import { useRouter } from 'vue-router';
import { useFormStore } from '@/store/form';
import { storeToRefs } from 'pinia';

const { $gettext } = useGettext();
const router = useRouter();
const formStore = useFormStore();
const { records } = storeToRefs(formStore);

const props = defineProps({
    userId: {
        type: String,
        required: true,
    },
});

const loading = ref(true);

onMounted(async () => {
    loading.value = true;
    await formStore.fetchByUserId(props.userId);
    loading.value = false;
});

const goToFormData = (formId) => {
    router.push({ path: `/form-data/${formId}` });
};

const goToStart = () => {
    window.location = STUDIP.URLHelper.getURL('dispatch.php/start');
}

watch(
    () => records.value,
    (newValue, oldValue) => {
        if (newValue.size === 0) {
            goToStart();
        }
        if (newValue.size === 1) {
            const singleFormId = newValue.keys().next().value;
            goToFormData(singleFormId);
        }
    },
);
</script>
<style lang="scss">
$padding: 15px;
$banner: 40px;
$footer: 32px;
$diffHeight: $padding + $padding + $banner + $footer;
#studip-checkin-app {
    position: absolute;
    top: 40px;
    background-color: #fff;
    z-index: 1000;
    width: var(--checkin-overlay-width);
    height: var(--checkin-overlay-height);
    margin: 0;
    padding: var(--checkin-overlay-padding);
    left: 0;
    overflow: auto;
}
</style>