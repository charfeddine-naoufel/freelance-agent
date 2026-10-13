
import { createRouter, createWebHistory } from 'vue-router';

import AppLayout from '../layouts/AppLayout.vue';
import Dashboard from '../pages/Dashboard.vue';
import Profile from '../pages/Profile/Profile.vue';
import Login from '../pages/Auth/Login.vue';
import Skills from '../pages/Skills/Skills.vue';
import Experiences from '../pages/Experiences/Experiences.vue';

import auth from '../services/auth';

const routes = [
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { guestOnly: true },
    },
    {
        path: '/',
        component: AppLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'dashboard',
                component: Dashboard,
            },
            {
                path: 'profile',
                name: 'profile',
                component: Profile,
            },
            {
                path: 'skills',
                name: 'skills',
                component: Skills,
            },
            {
                path: 'experiences',
                name: 'experiences',
                component: Experiences,
            },
        ],
    },

];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to) => {
    const isAuthenticated = auth.isAuthenticated();

    if (to.matched.some((record) => record.meta.requiresAuth)
        && !isAuthenticated) {
        return {
            name: 'login',
            query: { redirect: to.fullPath },
        };
    }

    if (to.meta.guestOnly && isAuthenticated) {
        return { name: 'dashboard' };
    }
});

export default router;