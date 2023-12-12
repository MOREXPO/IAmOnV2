<template>
  <div class="container my-5">
    <div class="row">
      <div class="col-12">
        <h2 class="pb-4 border-bottom text-white">Detalle del Switch</h2>
      </div>
    </div>

    <div class="row my-5">
      <div class="col-md-12">
        <div v-if="switchAux" class="card h-100">
          <div class="card-header bg-primary text-white">
            <h5 class="card-title">{{ switchAux.name }}</h5>
          </div>
          <div class="card-body">
            <p class="card-text">{{ switchAux.description }}</p>
            <div :class="!isPublic || switchAux.owner == '/api/users/' + user.id ? 'form-check form-switch' : ''"
              class="mb-3">
              <div v-if="!isPublic || switchAux.owner == '/api/users/' + user.id">
                <a v-if="!switchAux.state" href="#" data-bs-toggle="modal"
                  :data-bs-target="'#switchModal' + switchAux.id">
                  <input class="form-check-input" type="checkbox" role="switch" :id="'flexSwitchCheck' + switchAux.id">
                </a>
                <input v-else @click="checkSwitch(switchAux.id, onTime, !switchAux.state)" class="form-check-input"
                  type="checkbox" role="switch" :id="'flexSwitchCheck' + switchAux.id" checked>
              </div>
              <label :class="!isPublic || switchAux.owner == '/api/users/' + user.id ? 'form-check-label' : ''"
                for="estadoSwitch">
                <span v-if="switchAux.state" class="badge bg-success rounded-pill">Encendido</span>
                <span v-else class="badge bg-danger rounded-pill">Apagado</span>
              </label>
            </div>
            <div class="mb-3">
              <strong v-if="switchAux.state">
                <p id="minutosEncendido" class="card-text">Tiempo encendido: {{ tiemposSwitches.find(x => x.id ==
                  switchAux.id).contador }} segundos</p>
              </strong>
              <strong v-else>
                <p id="fechaUltimoEncendido" class="card-text">Fecha Último Encendido: {{ new
                  Date(switchAux.clickDateEnd).toLocaleString('es-ES', { timeZoneName: 'short' }) }}</p>
              </strong>

            </div>
            <div class="mb-3">
              <strong>Propietario:</strong>
              {{ app_users.find(x => x['@id'] == switchAux.owner).username }}
            </div>
          </div>
          <div class="modal fade" :id="'switchModal' + switchAux.id" tabindex="-1"
            :aria-labelledby="'switchModalLabel' + switchAux.id" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" :id="'switchModalLabel' + switchAux.id">Configurar interruptor
                  </h5>
                  <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form :id="'switchForm' + switchAux.id">
                    <div class="form-group">
                      <v-text-field label="Tiempo de Encendido (minutos):" v-model="onTime" type="number"
                        :rules="numericRules"></v-text-field>
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                  <v-btn @click="checkSwitch(switchAux.id, onTime, !switchAux.state)" class="btn btn-primary"
                    :disabled="onTime < 1 || onTime > 120" data-bs-dismiss="modal">Encender</v-btn>
                </div>
              </div>
            </div>
          </div>
          <div v-if="user_loaded && switchAux.owner != '/api/users/' + user.id" class="card-footer">
            <v-btn @click="changeFollowerSwitch(switchAux.id)"
              v-if="user_loaded && user_switches.some(y => y.switch == '/api/switchess/' + switchAux.id && y.user == '/api/users/' + user.id)"
              class="btn btn-danger">Dejar de seguir</v-btn>

            <v-btn v-else @click="changeFollowerSwitch(switchAux.id)" class="btn btn-primary">Seguir</v-btn>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { mapState, mapActions } from 'pinia';
import { userStore } from '/src/stores/user';
import { switchStore } from '/src/stores/switch';
import { userSwitchStore } from '/src/stores/userSwitch';
export default {
  data: () => ({
    isPublic: false,
    onTime: 60,
  }),
  props: {
    // Definir 'id' como una propiedad que espera recibir del enrutador
    uuid: {
      type: String,
      required: true
    }
  },
  computed: {
    numericRules() {
      return [
        value => !!value || 'Requerido',  // Check if the value is not empty
        value => (value >= 1) || 'Debe ser mas grande que 1',  // Minimum value rule
        value => (value <= 120) || 'Debe ser mas pequeño que 120', // Maximum value rule
      ];
    },
    ...mapState(userStore, {
      user: store => store.user,
      user_loaded: store => store.loaded,
      user_loading: store => store.loading,
      app_users: store => store.app_users,
    }),
    ...mapState(switchStore, {
      switches: store => store.switches,
      tiemposSwitches: store => store.tiemposSwitches,
    }),
    ...mapState(userSwitchStore, {
      user_switches: store => store.userSwitches,
    }),
    switchAux() {
      return this.switches.find(x => {
        if (x.publicUri == this.uuid) {
          this.isPublic = true;
          return true;
        }
        if (x.privateUri == this.uuid) {
          this.isPublic = false;
          return true;
        }
        return false
      });
    }
  },
  mounted() {
    /*if (!this.user_loaded)
      this.$router.push('/login')*/
  },
  methods: {
    ...mapActions(switchStore, ["checkSwitch"]),
    ...mapActions(userSwitchStore, ["changeFollowerSwitch"]),
  },
  watch: {

  }
}
</script>
