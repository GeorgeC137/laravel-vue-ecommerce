import { createStore } from 'vuex';
import axiosClient from '../axios';

const store = createStore({
    state: {
        user: {
            data: {},
            token: sessionStorage.getItem('TOKEN')
        },
        products: {
            loading: false,
            links: [],
            from: null,
            to: null,
            total: null,
            limit: null,
            page: 1,
            data: [],
        },
        users: {
            loading: false,
            links: [],
            from: null,
            to: null,
            total: null,
            limit: null,
            page: 1,
            data: [],
        },
        customers: {
            loading: false,
            links: [],
            from: null,
            to: null,
            total: null,
            limit: null,
            page: 1,
            data: [],
        },
        orders: {
            loading: false,
            links: [],
            from: null,
            to: null,
            total: null,
            limit: null,
            page: 1,
            data: [],
        },
        toast: {
            show: false,
            message: '',
            delay: 5000
        },
        product: {
            data: {}
        },
        adminUsers: {
            data: {}
        },
        customer: {
            data: {}
        },
        countries: [],
        dateOptions: [
            { key: "1d", text: "Last Day" },
            { key: "1w", text: "Last Week" },
            { key: "2w", text: "Last 2 Weeks" },
            { key: "1m", text: "Last Month" },
            { key: "3m", text: "Last 3 Months" },
            { key: "6m", text: "Last 6 Months" },
            { key: "all", text: "All Time " },
        ]
    },
    actions: {
        login({ commit }, user) {
            return axiosClient.post('/login', user)
                .then(({ data }) => {
                    commit('setUser', data);
                    return data;
                })
        },
        logout({ commit }) {
            return axiosClient.post('/logout')
                .then(() => {
                    commit('logout')
                })
        },
        getCurrentUser({ commit }) {
            return axiosClient.get('/user')
                .then((res) => {
                    commit('getCurrentUser', res.data);
                    return res;
                })
        },
        getCountries({ commit }) {
            return axiosClient.get('/countries')
                .then(({ data }) => {
                    commit('setCountries', data)
                })
        },
        getProducts({ commit }, { url = null, search = '', perPage = 10, sort_field, sort_direction } = {}) {
            url = url || '/products'
            commit('setProductsLoading', true);
            return axiosClient.get(url, {
                    params: { search, per_page: perPage, sort_field, sort_direction }
                })
                .then((res) => {
                    commit('setProductsLoading', false);
                    commit('setProducts', res.data);
                    return res;
                })
                .catch(() => {
                    commit('setProductsLoading', false)
                })
        },
        getUsers({ commit }, { url = null, search = '', perPage = 10, sort_field, sort_direction } = {}) {
            url = url || '/users'
            commit('setUsersLoading', true);
            return axiosClient.get(url, {
                    params: { search, per_page: perPage, sort_field, sort_direction }
                })
                .then((res) => {
                    commit('setUsersLoading', false);
                    commit('setUsers', res.data);
                    return res;
                })
                .catch(() => {
                    commit('setUsersLoading', false)
                })
        },
        getCustomers({ commit }, { url = null, search = '', perPage = 10, sort_field, sort_direction } = {}) {
            url = url || '/customers'
            commit('setCustomersLoading', true);
            return axiosClient.get(url, {
                    params: { search, per_page: perPage, sort_field, sort_direction }
                })
                .then((res) => {
                    commit('setCustomersLoading', false);
                    commit('setCustomers', res.data);
                    return res;
                })
                .catch(() => {
                    commit('setCustomersLoading', false)
                })
        },
        getOrders({ commit }, { url = null, search = '', perPage = 10, sort_field, sort_direction } = {}) {
            url = url || '/orders'
            commit('setOrdersLoading', true);
            return axiosClient.get(url, {
                    params: { search, per_page: perPage, sort_field, sort_direction }
                })
                .then((res) => {
                    commit('setOrdersLoading', false);
                    commit('setOrders', res.data);
                    return res;
                })
                .catch(() => {
                    commit('setOrdersLoading', false)
                })
        },
        createProduct({ commit }, product) {
            if (product.image instanceof File) {
                const form = new FormData();
                form.append('title', product.title);
                form.append('image', product.image);
                form.append('price', product.price);
                form.append('description', product.description);
                product = form;
            }

            return axiosClient.post('/products', product)
                .then((res) => {
                    commit('setProduct', res.data)
                    return res;
                })
        },
        createUser({ commit }, user) {
            return axiosClient.post('/users', user)
                .then((res) => {
                    commit('setAdminUsers', res.data)
                    return res;
                })
        },
        createCustomer({ commit }, customer) {
            return axiosClient.post('/customers', customer)
                .then((res) => {
                    commit('setCustomer', res.data)
                    return res;
                })
        },
        updateProduct({ commit }, product) {
            const id = product.id
            if (product.image instanceof File) {
                const form = new FormData();
                form.append('id', product.id);
                form.append('title', product.title);
                form.append('image', product.image);
                form.append('price', product.price);
                form.append('description', product.description);
                form.append('_method', 'PUT');
                product = form;
            } else {
                product._method = 'PUT'
            }

            return axiosClient.post(`/products/${id}`, product)
                .then((res) => {
                    commit('setProduct', res.data)
                    return res;
                })
        },
        updateUser({ commit }, user) {
            return axiosClient.put(`/users/${user.id}`, user)
                .then((res) => {
                    commit('setAdminUsers', res.data)
                    return res;
                })
        },
        updateCustomer({ commit }, customer) {
            return axiosClient.put(`/customers/${customer.id}`, customer)
                .then((res) => {
                    commit('setCustomer', res.data)
                    return res;
                })
        },
        deleteProduct({  }, id) {
            return axiosClient.delete(`/products/${id}`)
        },
        deleteUser({  }, id) {
            return axiosClient.delete(`/users/${id}`)
        },
        deleteCustomer({  }, id) {
            return axiosClient.delete(`/customers/${id}`)
        },
        getProduct({  }, id) {
            return axiosClient.get(`/products/${id}`)
        },
        getUser({  }, id) {
            return axiosClient.get(`/users/${id}`)
        },
        getCustomer({  }, id) {
            return axiosClient.get(`/customers/${id}`)
        },
        getOrder({  }, id) {
            return axiosClient.get(`/orders/${id}`)
        }
    },
    mutations: {
        setUser: (state, userData) => {
            state.user.data = userData.user;
            state.user.token = userData.token;
            sessionStorage.setItem('TOKEN', userData.token);
        },
        logout: (state) => {
            state.user.data = {};
            state.user.token = null;
            sessionStorage.removeItem('TOKEN');

        },
        getCurrentUser: (state, user) => {
            state.user.data = user;
        },
        setProducts: (state, products) => {
            state.products.data = products.data
            state.products.links = products.meta.links
            state.products.from = products.meta.from
            state.products.to = products.meta.to
            state.products.page = products.meta.current_page
            state.products.limit = products.meta.per_page
            state.products.total = products.meta.total
        },
        setUsers: (state, users) => {
            state.users.data = users.data
            state.users.links = users.meta.links
            state.users.from = users.meta.from
            state.users.to = users.meta.to
            state.users.page = users.meta.current_page
            state.users.limit = users.meta.per_page
            state.users.total = users.meta.total
        },
        setCustomers: (state, customers) => {
            state.customers.data = customers.data
            state.customers.links = customers.meta.links
            state.customers.from = customers.meta.from
            state.customers.to = customers.meta.to
            state.customers.page = customers.meta.current_page
            state.customers.limit = customers.meta.per_page
            state.customers.total = customers.meta.total
        },
        setOrders: (state, orders) => {
            state.orders.data = orders.data
            state.orders.links = orders.meta.links
            state.orders.from = orders.meta.from
            state.orders.to = orders.meta.to
            state.orders.page = orders.meta.current_page
            state.orders.limit = orders.meta.per_page
            state.orders.total = orders.meta.total
        },
        setProductsLoading: (state, loading) => {
            state.products.loading = loading
        },
        setUsersLoading: (state, loading) => {
            state.users.loading = loading
        },
        setCustomersLoading: (state, loading) => {
            state.customers.loading = loading
        },
        setOrdersLoading: (state, loading) => {
            state.orders.loading = loading
        },
        setProduct: (state, product) => {
            state.product.data = product.data;
        },
        setAdminUsers: (state, adminUsers) => {
            state.adminUsers.data = adminUsers.data;
        },
        setCustomer: (state, customer) => {
            state.customer.data = customer.data;
        },
        hideToast: (state) => {
            state.toast.show = false;
            state.toast.message = '';
        },
        showToast: (state, message) => {
            state.toast.show = true;
            state.toast.message = message;
        },
        setCountries: (state, countries) => {
            state.countries = countries.data
        }
    },
    getters: {},
    modules: {},
});

export default store;
