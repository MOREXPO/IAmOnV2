import { defineStore } from 'pinia';
import axios from 'axios';
export const userStore = defineStore({
    id: "user",
    state: () => ({
        user: {},
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
              .then(function (response) {
                console.log(response);
              })
              .catch(function (error) {
                console.log(error);
              }).finally(()=>{
                this.loading=false;
              });
            
        },
        setUser(user: any) {
            this.user = user
        },
        setLoading(valor: any) {
            this.loading = valor
        },
    }
});
