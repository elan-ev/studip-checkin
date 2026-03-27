<template>
    <table class="default">
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
    </table>
</template>

<script setup>
import { watch, onMounted } from 'vue';
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

onMounted(async () => {
    await formStore.fetchByUserId(props.userId);
});

const goToFormData = (formId) => {
    router.push({ path: `/form-data/${formId}` });
};

watch(
    () => records.value,
    (newValue, oldValue) => {
        if (newValue.size === 0) {
            // TODO: decide how to act here! of course confirm is not needed here! :D
            if (
                STUDIP.Dialog.confirm(
                    $gettext('Fertig! Sie können nun fortfahren.'),
                    () => {
                        window.location = STUDIP.URLHelper.getURL('dispatch.php/start');
                    },
                    STUDIP.Dialog.close(),
                )
            );
        }
    },
);
</script>
