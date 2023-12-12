import { defineStore } from 'pinia';
import axios from 'axios';
export const switchStore = defineStore({
    id: "switch",
    state: () => ({
        switches: [],
        tiemposSwitches: [],
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

            axios.get('http://localhost/api/switchess')
                .then(response => {
                    this.switches = response.data['hydra:member'];
                    this.tiemposSwitches = this.switches.map(item => ({
                        id: item.id,
                        contador: 0,
                        temporizador: null
                    }));
                    console.log(this.tiemposSwitches);
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

            axios.post('http://localhost/api/switchess', {
                name: nombre,
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

            axios.put('http://localhost/api/switch/' + id + '/check', {
                onTime: onTime,
                isChecked: isChecked
            })
                .then(response => {
                    console.log(this.switches);
                    this.switches.find(x => x.id == id).state = response.data['state'];
                    if (response.data['state']) {
                        let tiempoEnSegundos = onTime * 60;
                        console.log(this.tiemposSwitches);
                        this.tiemposSwitches.find(x => x.id == id).temporizador = setInterval(() => {
                            if (this.tiemposSwitches.find(x => x.id == id).contador >= tiempoEnSegundos) {
                                clearInterval(this.tiemposSwitches.find(x => x.id == id).temporizador); // Detiene el intervalo cuando el tiempo llega a cero
                                this.tiemposSwitches.find(x => x.id == id).contador = 0;
                                this.switches.find(x => x.id == id).state = false;
                            } else {
                                this.tiemposSwitches.find(x => x.id == id).contador++;
                            }
                        }, 1000);
                    } else {
                        clearInterval(this.tiemposSwitches.find(x => x.id == id).temporizador);
                        this.tiemposSwitches.find(x => x.id == id).contador = 0;
                    }

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
                    this.tiemposSwitches = this.tiemposSwitches.filter(x => x.id != id);
                    console.log(this.switches);
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
