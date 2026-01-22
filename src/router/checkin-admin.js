import { createMemoryHistory, createRouter } from 'vue-router';

import TheOverview from '../pages/admin/TheOverview.vue';
import FormPage from '../pages/admin/FormPage.vue';

const routes = [
    { name: 'admin', path: '/', component: TheOverview },
    { name: 'new-form', path: '/new', component: FormPage, props: { isNew: true } },
    { name: 'edit-form', path: '/edit/:id', component: FormPage, props: { isNew: false } },
];

export const router = createRouter({
    history: createMemoryHistory(),
    routes,
});
