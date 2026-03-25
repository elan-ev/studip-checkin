import { createApp } from 'vue';
import App from './CheckinApp.vue';
import { createGettext } from 'vue3-gettext';
import { createPinia } from 'pinia';
import { router } from './router/checkin-profile';
import { useContextStore } from './store/context';

const preferredLanguage = el?.dataset?.preferredLanguage || null;

const contextStore = useContextStore();

if (preferredLanguage) {
  contextStore.setPreferredLanguage(preferredLanguage);
}

const app = createApp(App);

const gettext = createGettext({
    availableLanguages: {
      en: "English",
      de: "Deutsch",
    },
    defaultLanguage: "de",
});
app.use(gettext);

const pinia = createPinia();
app.use(pinia);

app.use(router);

app.mount('#studip-checkin-profile-app');
