import { createWebHistory, createRouter } from 'vue-router';
import TheUsersOverview from '../pages/profile/TheUsersOverview.vue';

const routes = [
    { name: 'profile', path: '/', component: TheUsersOverview }
];

const absoluteUriStudip = new URL(window.STUDIP.ABSOLUTE_URI_STUDIP);
const baseUrl = `${absoluteUriStudip.pathname}plugins.php/studipcheckin/profile/#`;

export const router = createRouter({
    history: createWebHistory(baseUrl),
    routes,
});
