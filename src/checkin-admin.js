import { createApp } from 'vue';
import App from './CheckinApp.vue';
import { createGettext } from 'vue3-gettext';
import { createPinia } from 'pinia';
import { router } from './router/checkin-admin';
import { useContextStore } from './store/context';

const el = document.getElementById('studip-checkin-admin-app');
const preferredLanguage = el?.dataset?.preferredLanguage || null;

const app = createApp(App);

const gettext = createGettext({
    availableLanguages: {
        en: 'English',
        de: 'Deutsch',
    },
    defaultLanguage: 'de',
});
app.use(gettext);

const pinia = createPinia();
app.use(pinia);

const contextStore = useContextStore();

if (preferredLanguage) {
    contextStore.setPreferredLanguage(preferredLanguage);
}

app.use(router);

app.mount('#studip-checkin-admin-app');
