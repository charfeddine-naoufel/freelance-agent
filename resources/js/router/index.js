import { createRouter, createWebHistory } from 'vue-router';

import Dashboard from '../pages/Dashboard.vue';
import Profile from '../pages/Profile/Profile.vue';

const routes = [

    {
        path: '/',
        name: 'dashboard',
        component: Dashboard,
    },

    {
        path: '/profile',
        name: 'profile',
        component: Profile,
    },

];

const router = createRouter({

    history: createWebHistory(),

    routes,

});

export default router;