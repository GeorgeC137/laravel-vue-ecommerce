import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../views/Dashboard.vue';
import Products from '../views/Products/Products.vue';
import Login from '../views/Login.vue';
import RequestPassword from '../views/RequestPassword.vue';
import ResetPassword from '../views/ResetPassword.vue';
import AppLayout from '../components/AppLayout.vue';
import NotFound from '../components/NotFound.vue';
import store from '../store';

const routes = [
    {
        path: '/app',
        name: 'app',
        component: AppLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: 'dashboard',
                name: 'app.dashboard',
                component: Dashboard
            },
            {
                path: 'products',
                name: 'app.products',
                component: Products
            }
        ]
    },
    {
        path: '/login',
        name: 'Login',
        meta: { isGuest: true },
        component: Login
    },
    {
        path: '/:pathMatch(.*)',
        name: 'notFound',
        component: NotFound
    },
    {
        path: '/request-password',
        name: 'requestPassword',
        meta: { isGuest: true },
        component: RequestPassword
    },
    {
        path: '/reset-password/:token',
        name: 'resetPassword',
        meta: { isGuest: true },
        component: ResetPassword
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !store.state.user.token) {
        next({ name: 'Login' })
    } else if (to.meta.isGuest && store.state.user.token) {
        next({ name: 'app.dashboard' })
    } else {
        next()
    }
})

export default router;
