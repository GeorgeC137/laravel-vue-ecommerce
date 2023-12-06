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
            url = url || '/product'
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
        createProduct({ commit }, product) {
            if (product.image instanceof File) {
                const form = new FormData();
                form.append('title', product.title);
                form.append('image', product.image);
                form.append('price', product.price);
                form.append('description', product.description);
                product = form;
            }

            return axiosClient.post('/product', product)
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

            return axiosClient.post(`/product/${id}`, product)
                .then((res) => {
                    commit('setProduct', res.data)
                    return res;
                })
        },
        deleteProduct({  }, id) {
            return axiosClient.delete(`/product/${id}`)
        },
        getProduct({  }, id) {
            return axiosClient.get(`/product/${id}`)
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
        setProductsLoading: (state, loading) => {
            state.products.loading = loading
        },
        setProduct: (state, product) => {
            state.product.data = product.data;
        }
    },
    getters: {},
    modules: {},
});

export default store;
