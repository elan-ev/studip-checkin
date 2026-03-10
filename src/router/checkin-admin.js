import { createWebHistory, createRouter } from 'vue-router';

import TheOverview from '../pages/admin/TheOverview.vue';
import FormPage from '../pages/admin/FormPage.vue';
import RelatedUsersPage from '../pages/admin/RelatedUsersPage.vue';
import FormUserDataPage from '../pages/admin/FormUserDataPage.vue';

const routes = [
    { name: 'admin', path: '/', component: TheOverview },
    { name: 'new-form', path: '/new', component: FormPage, props: { isNew: true } },
    { name: 'edit-form', path: '/edit/:id', component: FormPage, props: { isNew: false } },
    { name: 'related-users', path: '/form/:formId/related-users', component: RelatedUsersPage, props: true },
    { name: 'form-user-data', path: '/form/:formId/user-data', component: FormUserDataPage, props: true },
];

const absoluteUriStudip = new URL(window.STUDIP.ABSOLUTE_URI_STUDIP);
const baseUrl = `${absoluteUriStudip.pathname}plugins.php/studipcheckin/admin/#`;

export const router = createRouter({
    history: createWebHistory(baseUrl),
    routes,
});
