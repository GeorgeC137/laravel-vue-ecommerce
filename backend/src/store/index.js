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
        product: {
            data: {}
        }
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
        getUser({ commit }) {
            return axiosClient.get('/user')
                .then((res) => {
                    commit('getUser', res.data);
                    return res;
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
        deleteProduct({  }, id) {
            return axiosClient.delete(`/products/${id}`)
        },
        getProduct({  }, id) {
            return axiosClient.get(`/products/${id}`)
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
        getUser: (state, user) => {
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
        setOrdersLoading: (state, loading) => {
            state.orders.loading = loading
        },
        setProduct: (state, product) => {
            state.product.data = product.data;
        }
    },
    getters: {},
    modules: {},
});

export default store;
