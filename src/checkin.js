import { createApp } from 'vue';
import App from './CheckinApp.vue';
import { createGettext } from 'vue3-gettext';
import { createPinia } from 'pinia';

const el = document.getElementById('studip-checkin-app');
const userId = el?.dataset?.userId || null;

const app = createApp(App, {
  userId,
});

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

app.mount('#studip-checkin-app');
