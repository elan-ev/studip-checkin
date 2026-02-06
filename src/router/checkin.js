import { createMemoryHistory, createRouter } from 'vue-router';

import UserFormData from '../pages/user/UserFormData.vue';
import UserForms from '../pages/user/UserForms.vue';

const routes = [
    { name: 'user-forms', path: '/', component: UserForms },
    { name: 'user-form-data', path: '/form-data/:formId', component: UserFormData, props: true },
];

export const router = createRouter({
    history: createMemoryHistory(new URL(window.location.href).pathname),
    routes,
});
