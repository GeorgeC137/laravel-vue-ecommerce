import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../views/Dashboard.vue';
import Products from '../views/Products/Products.vue';
import ProductForm from '../views/Products/ProductForm.vue';
import Users from '../views/Users/Users.vue';
import Categories from '../views/Categories/Categories.vue';
import Customers from '../views/Customers/Customers.vue';
import Orders from '../views/Orders/Orders.vue';
import OrderView from '../views/Orders/OrderView.vue';
import CustomerView from '../views/Customers/CustomerView.vue';
import Login from '../views/Login.vue';
import RequestPassword from '../views/RequestPassword.vue';
import ResetPassword from '../views/ResetPassword.vue';
import AppLayout from '../components/AppLayout.vue';
import NotFound from '../components/NotFound.vue';
import Report from '../views/Reports/Report.vue'
import OrdersReport from '../views/Reports/OrdersReport.vue'
import CustomersReport from '../views/Reports/CustomersReport.vue'
import store from '../store';

const routes = [
    {
        path: '/app',
        redirect: '/app/dashboard',
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
            },
            {
                path: 'categories',
                name: 'app.categories',
                component: Categories
            },
            {
                path: 'products/create',
                name: 'app.products.create',
                component: ProductForm
            },
            {
                path: 'products/:id',
                name: 'app.products.edit',
                component: ProductForm,
                props: {
                    id: (value) => /^\d+$/.test(value)
                }
            },
            {
                path: 'users',
                name: 'app.users',
                component: Users
            },
            {
                path: 'customers',
                name: 'app.customers',
                component: Customers
            },
            {
                path: 'customers/:id',
                name: 'app.customers.view',
                component: CustomerView
            },
            {
                path: 'orders',
                name: 'app.orders',
                component: Orders
            },
            {
                path: '/report',
                name: 'reports',
                component: Report,
                children: [
                    {
                        path: 'orders/:date?',
                        name: 'reports.orders',
                        component: OrdersReport
                    },
                    {
                        path: 'customers/:date?',
                        name: 'reports.customers',
                        component: CustomersReport
                    },
                ]
            },
            {
                path: 'orders/:id',
                name: 'app.orders.view',
                component: OrderView
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
