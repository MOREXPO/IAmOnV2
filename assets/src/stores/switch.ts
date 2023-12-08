import { defineStore } from 'pinia';
import axios from 'axios';
export const switchStore = defineStore({
    id: "switch",
    state: () => ({
        switches: [],
        loaded: false,
        loading: false,
    }),
    getters: {
        getSwitches: (state) => {
            return state.switches
        },
        getLoaded: (state) => {
            return state.loaded
        },
        getLoading: (state) => {
            return state.loading
        },
    },
    actions: {
        getSwitchess() {
            this.loading = true;

            axios.get('http://localhost/api/switchess', {
                headers: {
                    'Authorization': `Bearer ${sessionStorage.getItem('token')}`,
                }
            })
                .then(response => {
                    this.switches = response.data['hydra:member'];
                    this.loading = false;
                    this.loaded = true;
                })
                .catch(error => {
                    // Manejar el error aquí
                    console.error('Error:', error);
                    this.loading = false;
                });
        },
        createSwitch(nombre: any, description: any) {
            this.loading = true;

            axios.post('http://localhost/api/create-switch', {
                nombre: nombre,
                description: description
            }, {
                headers: {
                    'Authorization': `Bearer ${sessionStorage.getItem('token')}`,
                }
            })
                .then(response => {
                    this.switches.push(response.data);
                    console.log(this.switches);
                    this.loading = false;
                })
                .catch(error => {
                    // Manejar el error aquí
                    console.error('Error:', error);
                    this.loading = false;
                });
        },
        checkSwitch(id: any, onTime: any, isChecked: any) {
            this.loading = true;

            axios.post('http://localhost/api/check-switch/' + id, {
                onTime: onTime,
                isChecked: isChecked
            }, {
                headers: {
                    'Authorization': `Bearer ${sessionStorage.getItem('token')}`,
                }
            })
                .then(response => {
                    this.switches.find(x => x.id == id).state = response.data['state'];
                    this.loading = false;
                })
                .catch(error => {
                    // Manejar el error aquí
                    console.error('Error:', error);
                    this.loading = false;
                });
        },
        deleteSwitch(id: any) {
            this.loading = true;

            axios.delete('http://localhost/api/switchess/' + id, {
                headers: {
                    'Authorization': `Bearer ${sessionStorage.getItem('token')}`,
                }
            })
                .then(response => {
                    this.switches = this.switches.filter(x => x.id != id);
                    console.log(this.switches);
                    this.loading = false;
                })
                .catch(error => {
                    // Manejar el error aquí
                    console.error('Error:', error);
                    this.loading = false;
                });
        },
        getSwitch(uuid: any) {
            this.loading = true;

            axios.get('http://localhost/api/switch/' + uuid, {
                headers: {
                    'Authorization': `Bearer ${sessionStorage.getItem('token')}`,
                }
            })
                .then(response => {
                    console.log(response.data);
                    this.loading = false;
                })
                .catch(error => {
                    // Manejar el error aquí
                    console.error('Error:', error);
                    this.loading = false;
                });
        },
        setSwitches(switches: any) {
            this.switches = switches
        },
        setLoading(valor: any) {
            this.loading = valor
        },
        setLoaded(valor: any) {
            this.loaded = valor
        },
    }
});
