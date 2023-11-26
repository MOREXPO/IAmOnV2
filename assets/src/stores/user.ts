import { defineStore } from 'pinia';
import axios from 'axios';
export const userStore = defineStore({
    id: "user",
    state: () => ({
        user: {},
        token: "",
        loaded: false,
        loading: false,
    }),
    getters: {
        getUser: (state) => {
            return state.user
        },
        getLoaded: (state) => {
            return state.loaded
        },
        getLoading: (state) => {
            return state.loading
        },
    },
    actions: {
        async login(objeto: any) {
            this.loading = true;
            let response = await axios.post('http://localhost/auth', objeto);
            this.token = response.data.token;
            response = await axios.get('http://localhost/api/user_auth', {
                headers: {
                    'Authorization': `Bearer ${this.token}`,
                }
            });
            this.user = response.data;
            sessionStorage.setItem('token', this.token);
            sessionStorage.setItem('user', JSON.stringify(this.user));
            this.loading = false;
            this.loaded = true;
        },
        async registration(objeto: any) {
            this.loading = true;
            let response = await axios.post('http://localhost/api/registration', objeto);
            this.loading = false;
        },
        logout() {
            sessionStorage.clear();
            this.loading = false;
            this.loaded = false;
            this.user = {};
            this.token = "";
        },
        setUser(user: any) {
            this.user = user
        },
        setToken(token: string) {
            this.token = token
        },
        setLoading(valor: any) {
            this.loading = valor
        },
        setLoaded(valor: any) {
            this.loaded = valor
        },
    }
});
