import { createApp } from 'vue';
import App from './CheckinAdminApp.vue';
import { createGettext } from 'vue3-gettext';
import { createPinia } from 'pinia';

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

app.mount('#studip-checkin-admin-app');