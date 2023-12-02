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
        getProducts({ commit }, { url = null, search = '', perPage = 10 } = {}) {
            url = url || '/product'
            commit('setProductsLoading', true);
            return axiosClient.get(url, {
                    params: { search, per_page: perPage }
                })
                .then((res) => {
                    commit('setProductsLoading', false);
                    commit('setProducts', res.data);
                    return res;
                })
                .catch(() => {
                    commit('setProductsLoading', false)
                })
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
        }
    },
    getters: {},
    modules: {},
});

export default store;
