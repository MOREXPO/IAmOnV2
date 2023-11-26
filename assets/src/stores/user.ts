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
        login(objeto: any) {
            this.loading = true;
        
            axios.post('http://localhost/auth', objeto)
                .then(response => {
                    this.token = response.data.token;
                    return axios.get('http://localhost/api/user_auth', {
                        headers: {
                            'Authorization': `Bearer ${this.token}`,
                        }
                    });
                })
                .then(response => {
                    this.user = response.data;
                    sessionStorage.setItem('token', this.token);
                    sessionStorage.setItem('user', JSON.stringify(this.user));
                    this.loading = false;
                    this.loaded = true;
                })
                .catch(error => {
                    // Manejar el error aquí
                    console.error('Error en la función login:', error);
                    this.loading = false;
                });
        },
        registration(objeto: any) {
            this.loading = true;
        
            axios.post('http://localhost/api/registration', objeto)
                .then(response => {
                    this.loading = false;
                })
                .catch(error => {
                    // Manejar el error aquí
                    console.error('Error en la función registration:', error);
                    this.loading = false;
                });
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
