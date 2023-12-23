import { defineStore } from 'pinia';
import axios from 'axios';
export const userSwitchStore = defineStore({
    id: "userSwitch",
    state: () => ({
        userSwitches: [],
        loaded: false,
        loading: false,
    }),
    getters: {
        getUserSwitches: (state) => {
            return state.userSwitches
        },
        getLoaded: (state) => {
            return state.loaded
        },
        getLoading: (state) => {
            return state.loading
        },
    },
    actions: {
        getUserSwitchess() {
            this.loading = true;

            axios.get('http://localhost/api/user_switches', {
                headers: {
                    'Authorization': `Bearer ${sessionStorage.getItem('token')}`,
                }
            })
                .then(response => {
                    this.userSwitches = response.data['hydra:member'];
                    this.loading = false;
                    this.loaded = true;
                })
                .catch(error => {
                    // Manejar el error aquí
                    console.error('Error:', error);
                    this.loading = false;
                });
        },
        changeFollowerSwitch(id: any) {
            this.loading = true;

            axios.put('http://localhost/api/switch/' + id + '/follower', {

            }, {
                headers: {
                    'Authorization': `Bearer ${sessionStorage.getItem('token')}`,
                }
            })
                .then(response => {
                    this.getUserSwitchess();
                    this.loading = false;
                })
                .catch(error => {
                    // Manejar el error aquí
                    console.error('Error:', error);
                    this.loading = false;
                });
        },
        setUserSwitches(userSwitches: any) {
            this.userSwitches = userSwitches
        },
        setLoading(valor: any) {
            this.loading = valor
        },
        setLoaded(valor: any) {
            this.loaded = valor
        },
    }
});
