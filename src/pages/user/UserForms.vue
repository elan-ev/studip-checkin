<template>
    <article v-if="!loading && records.size !== 1" class="checkin-user-forms">
        <header class="checkin-user-forms-header">
            <h2>{{ $gettext('Bitte füllen Sie die folgenden Formulare aus') }}</h2>
        </header>
        <section>
            <ul class="checkin-user-forms-list">
                <li v-for="form in formStore.all" class="checkin-user-forms-list-item">
                    <button @click="goToFormData(form.id)" class="checkin-user-forms-list-item-button undecorated">
                        <h3>{{ form.name[lang] }}</h3>
                        <p>{{ form.description[lang] }}</p>
                    </button>
                </li>
            </ul>
        </section>
        <footer class="checkin-user-forms-footer">
            <button class="undecorated as-link" @click="goToStart">{{ $gettext('Zur Startseite') }}</button>
        </footer>
    </article>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { useGettext } from 'vue3-gettext';
import { useRouter } from 'vue-router';
import { useFormStore } from '@/store/form';
import { useContextStore } from '@/store/context';
import { storeToRefs } from 'pinia';

const { $gettext } = useGettext();
const router = useRouter();
const formStore = useFormStore();
const { records } = storeToRefs(formStore);
const contextStore = useContextStore();


const props = defineProps({
    userId: {
        type: String,
        required: true,
    },
});

const loading = ref(true);

const lang = computed(() => {
    return contextStore.langSelector;
});

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
};

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
#plugin-studip_checkin-redirect-index {
    #navigation-level-1,
    #current-page-structure {
        display: none;
    }
}

.checkin-user-forms {
    max-width: 800px;
    margin: 0 auto;

    &-header {
        text-align: center;
        margin-bottom: 30px;
    }

    &-list {
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding: 0;

        &-item {
            display: flex;
            align-items: center;
            padding: 10px 5px;
            background: var(--color--global-background);
            gap: 15px;
            transition:
                transform 0.1s,
                box-shadow 0.1s;

            &:not(:last-child) {
                border-bottom: 1px solid var(--color--tile-border);
            }

            &-button.undecorated {
                text-align: left;
                width: 100%;
                border-left: solid 4px var(--active-color);
                padding-left: 10px;

                h3 {
                    margin-top: 5px;
                    color: var(--base-color);
                }

                &:hover h3 {
                    text-decoration: underline;
                }
            }
        }
    }

    &-footer {
        text-align: right;
        margin-top: 30px;
    }
}
</style>
