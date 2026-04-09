import { createApp } from 'vue';
import App from './CheckinApp.vue';
import { createGettext } from 'vue3-gettext';
import { createPinia } from 'pinia';
import { router } from './router/checkin';
import { useContextStore } from './store/context';
import translations from './locales/translations.json';

const el = document.getElementById('studip-checkin-app');
if (el) {
    const userId = el?.dataset?.userId || null;
    const preferredLanguage = el?.dataset?.preferredLanguage || null;

    const app = createApp(App, {
        userId,
    });

    const pinia = createPinia();
    app.use(pinia);

    const contextStore = useContextStore();

    if (preferredLanguage) {
        contextStore.setPreferredLanguage(preferredLanguage);
    }

    const gettext = createGettext({
        availableLanguages: {
            en: 'English',
            de: 'Deutsch',
        },
        defaultLanguage: contextStore.langSelector || 'de',
        translations: translations,
        silent: true,
    });
    app.use(gettext);

    app.use(router);

    app.mount('#studip-checkin-app');
}
