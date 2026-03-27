import { createApp } from 'vue';
import App from './CheckinApp.vue';
import { createGettext } from 'vue3-gettext';
import { createPinia } from 'pinia';
import { router } from './router/checkin';
import { useContextStore } from './store/context';

const el = document.getElementById('studip-checkin-app');
const userId = el?.dataset?.userId || null;
const preferredLanguage = el?.dataset?.preferredLanguage || null;

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

const contextStore = useContextStore();

if (preferredLanguage) {
  contextStore.setPreferredLanguage(preferredLanguage);
}

app.use(router);

app.mount('#studip-checkin-app');

const topBar = document.getElementById('top-bar');
const footer = document.getElementById('main-footer');
const padding = 30;

const diffHeight = topBar.offsetHeight + footer.offsetHeight + 2 * padding;
const diffWidth = 2 * padding;

el.style.setProperty('--checkin-overlay-height', `calc( 100vH - ${diffHeight}px)`);
el.style.setProperty('--checkin-overlay-width', `calc( 100vW - ${diffWidth}px)`);
el.style.setProperty('--checkin-overlay-padding', ` ${padding}px`);