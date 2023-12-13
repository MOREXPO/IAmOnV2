<template>
  <div class="container my-5">
    <div class="row">
      <div class="col-12 col-md-4">
        <v-card class="mx-auto" color="grey-lighten-3" max-width="400">
          <v-card-text>
            <v-text-field v-model="search" :loading="loadingSearch" density="compact" variant="solo"
              :label="words['buscar interruptores'][language]" append-inner-icon="mdi-magnify" single-line
              hide-details></v-text-field>
          </v-card-text>
        </v-card>
      </div>
    </div>

    <div class="row my-5">
      <div class="col-12">
        <h2 class="pb-2 border-bottom d-flex justify-content-between">{{ words['mis interruptores'][language] }}
          <a class="link-dark" href="#" data-bs-toggle="modal" data-bs-target="#addModal">
            <v-icon icon="mdi-plus-box"></v-icon>
          </a>
        </h2>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content rounded-4 shadow">
          <div class="modal-header p-5 pb-4 border-bottom-0">
            <h1 class="fw-bold mb-0 fs-2">{{ words['crear nuevo switch'][language] }}</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body p-5 pt-0">
            <div class="form-floating mb-3">
              <v-text-field v-model="nombre_create" :label="words['nombre'][language]" hide-details="auto"></v-text-field>
              <div v-if="error" class="alert alert-danger" role="alert">
                {{ words['el nombre es obligatorio'][language] }}
              </div>
            </div>
            <div class="form-floating mb-3">
              <v-textarea v-model="description_create" name="input-7-1" variant="filled"
                :label="words['descripcion (opcional)'][language]" auto-grow></v-textarea>
            </div>
            <v-btn class="w-100 mb-2 btn btn-lg rounded-3 btn-primary" @click="crearSwitch" data-bs-dismiss="modal">{{
              words['crear'][language] }}</v-btn>
          </div>
        </div>
      </div>
    </div>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <div v-for="mi_switch in       mis_switches      " class="col my-4">
        <div class="card h-100 bg-light">
          <div class="card-body" :class="mi_switch.state ? ' bg-success-subtle' : 'bg-danger-subtle'">
            <h5 class="card-title">
              {{ mi_switch.name }}
            </h5>
            <p class="card-text">
            <div class="form-check form-switch fs-5">

              <a v-if="!mi_switch.state" href="#" data-bs-toggle="modal" :data-bs-target="'#switchModal' + mi_switch.id">
                <input class="form-check-input" type="checkbox" role="switch" :id="'flexSwitchCheck' + mi_switch.id">
              </a>
              <input v-else @click="checkSwitch(mi_switch.id, onTime, !mi_switch.state)" class="form-check-input"
                type="checkbox" role="switch" :id="'flexSwitchCheck' + mi_switch.id" checked>
            </div>
            </p>
            <div class="mb-3">
              <strong v-if="mi_switch.state">
                <p id="minutosEncendido" class="card-text">{{ words['tiempo encendido'][language] }}: {{ tiemposSwitches.find(x=> x.id ==mi_switch.id).contador }} {{ words['segundos'][language] }}</p>
              </strong>
              <strong v-else>
                <p id="fechaUltimoEncendido" class="card-text">{{ words['fecha ultimo encendido'][language] }}: {{ new
                  Date(mi_switch.clickDateEnd).toLocaleString('es-ES', { timeZoneName: 'short' }) }}</p>
              </strong>
            </div>
          </div>
          <div class="modal fade" :id="'switchModal' + mi_switch.id" tabindex="-1"
            :aria-labelledby="'switchModalLabel' + mi_switch.id" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" :id="'switchModalLabel' + mi_switch.id">{{ words['configurar interruptor'][language]
                  }}
                  </h5>
                  <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form :id="'switchForm' + mi_switch.id">
                    <div class="form-group">
                      <v-text-field :label="words['tiempo de encendido'][language]+' '+(words['minutos'][language])+':'" v-model=" onTime " type="number" :rules=" numericRules "></v-text-field>
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">{{words['cerrar'][language]}}</button>
                  <v-btn @click="checkSwitch(mi_switch.id, onTime, !mi_switch.state)" class="btn btn-primary"
                    :disabled=" onTime < 1 || onTime > 120 " data-bs-dismiss="modal">{{words['encender'][language]}}</v-btn>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <div class="d-flex justify-content-between">
              <a href="#" class="fs-4" data-bs-toggle="modal" :data-bs-target=" '#modalUri' + mi_switch.id ">
                <v-icon icon="mdi-share-variant"></v-icon>
              </a>
              <div class="fs-4">{{ mi_switch.followers.length }}&nbsp<v-icon icon="mdi-account"></v-icon>
              </div>
              <div>
                <!-- Agrega enlaces o botones según tus necesidades -->
                <v-btn class="btn btn-danger" @click="borrarSwitch(mi_switch.id)"><v-icon
                    icon="mdi-delete"></v-icon></v-btn>
              </div>
            </div>
          </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" :id=" 'modalUri' + mi_switch.id " tabindex="-1" aria-labelledby="modalUriLabel"
          aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalUriLabel">URIs</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="form-floating mb-3">
                  <div class="input-group">
                    <span class="input-group-text" id="inputGroup-sizing-sm">{{words['uri publico'][language]}}</span>
                    <input type="text" class="form-control" :id=" 'floatingInputPublic' + mi_switch.id "
                      :value=" mi_switch.publicUri " readonly>
                    <CopyToClipboard :copy=" mi_switch.publicUri " class="btn btn-outline-secondary"><v-icon
                        icon="mdi-clipboard"></v-icon>
                      {{words['copiar'][language]}}</CopyToClipboard>
                  </div>
                </div>
                <div class="form-floating mb-3">
                  <div class="input-group">
                    <span class="input-group-text" id="inputGroup-sizing-sm">{{words['uri privado'][language]}}</span>
                    <input type="text" class="form-control" :id=" 'floatingInputPrivate' + mi_switch.id "
                      :value=" mi_switch.privateUri " readonly>
                    <CopyToClipboard :copy=" mi_switch.privateUri " class="btn btn-outline-secondary"><v-icon
                        icon="mdi-clipboard"></v-icon>
                      {{words['copiar'][language]}}</CopyToClipboard>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row my-5">
      <div class="col-12">
        <h2 class="pb-2 border-bottom d-flex justify-content-between">{{words['mis interruptores suscritos'][language]}}
        </h2>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content rounded-4 shadow">
          <div class="modal-header p-5 pb-4 border-bottom-0">
            <h1 class="fw-bold mb-0 fs-2">{{words['crear nuevo switch'][language]}}</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body p-5 pt-0">
            <div class="form-floating mb-3">
              <v-text-field v-model=" nombre_create " :label="words['nombre'][language]" hide-details="auto"></v-text-field>
              <div v-if=" error " class="alert alert-danger" role="alert">
                {{words['el nombre es obligatorio'][language]}}
              </div>
            </div>
            <div class="form-floating mb-3">
              <v-textarea v-model=" description_create " name="input-7-1" variant="filled" :label="words['descripcion (opcional)'][language]"
                auto-grow></v-textarea>
            </div>
            <v-btn class="w-100 mb-2 btn btn-lg rounded-3 btn-primary" @click=" crearSwitch ">{{words['crear'][language]}}</v-btn>
          </div>
        </div>
      </div>
    </div>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <div v-for="      switch_suscrito       in       switches_suscritos      " class="col my-4">
        <div class="card h-100 bg-light">
          <div class="card-body" :class=" switch_suscrito.state ? 'bg-success-subtle' : 'bg-danger-subtle' ">
            <h5 class="card-title">
              {{ switch_suscrito.name }}
            </h5>
            <p class="card-text">{{words['propietario'][language]}}:
              {{ app_users.find(x => x['@id'] == switch_suscrito.owner).username }}
            </p>

            <div class="mb-3">
              <strong v-if=" switch_suscrito.state ">
                <p id="minutosEncendido" class="card-text">{{words['tiempo de encendido'][language]}}: {{ tiemposSwitches.find(x => x.id ==
                  switch_suscrito.id).contador }} {{words['segundos'][language]}}</p>
              </strong>
              <strong v-else>
                <p id="fechaUltimoEncendido" class="card-text">{{words['fecha ultimo encendido'][language]}}: {{ new
                  Date(switch_suscrito.clickDateEnd).toLocaleString('es-ES', { timeZoneName: 'short' }) }}</p>
              </strong>
            </div>

            <!-- Agrega otros detalles de tus switches suscritos según tus datos -->
          </div>
          <div class="card-footer">
            <!-- Agrega enlaces o botones según tus necesidades -->
            <div class="d-flex justify-content-between">
              <div>
                <!-- Agrega detalles del estado o la fecha de encendido según tus datos -->
              </div>
              <div>
                <v-btn @click="changeFollowerSwitch(switch_suscrito.id)" class="btn btn-danger">{{words['dejar de seguir'][language]}}</v-btn>
              </div>
            </div>
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
import { translateStore } from '/src/stores/translate';
export default {
  data: () => ({
    onTime: 60,
    search: "",
    error: false,
    nombre_create: "",
    description_create: "",
    loadedSearch: false,
    loadingSearch: false,
  }),
  computed: {
    ...mapState(userStore, {
      user: store => store.user,
      user_loaded: store => store.loaded,
      app_users: store => store.app_users,
    }),
    ...mapState(switchStore, {
      switches: store => store.switches,
      tiemposSwitches: store => store.tiemposSwitches,
    }),
    ...mapState(userSwitchStore, {
      user_switches: store => store.userSwitches,
    }),
    ...mapState(translateStore, {
      words: store => store.words,
      language: store => store.language,
    }),
    mis_switches() {
      const searchLower = this.search.toLowerCase().trim();
      return this.switches.filter(x => {
        if (x.owner == '/api/users/' + this.user.id) {
          if (x.name.toLowerCase().includes(searchLower)) return true;
        }
        return false;
      });
    },
    switches_suscritos() {
      const searchLower = this.search ? this.search.toLowerCase().trim() : "";
      return this.switches.filter(y => this.user_switches.some(x => x.user == '/api/users/' + this.user.id && x.switch == y['@id']));
    },
    numericRules() {
      return [
        value => !!value || 'Requerido',  // Check if the value is not empty
        value => (value >= 1) || 'Debe ser mas grande que 1',  // Minimum value rule
        value => (value <= 120) || 'Debe ser mas pequeño que 120', // Maximum value rule
      ];
    },
  },
  mounted() {
    console.log(this.user_loaded);
    if (!this.user_loaded)
      this.$router.push('/login')
  },
  methods: {
    ...mapActions(switchStore, ["createSwitch", "deleteSwitch", "checkSwitch"]),
    ...mapActions(userSwitchStore, ["changeFollowerSwitch"]),
    crearSwitch() {
      if (this.nombre_create.trim().length > 0) {
        this.createSwitch(this.nombre_create, this.description_create);
        this.error = false;
      }
      else this.error = true;
    },
    borrarSwitch(id) {
      if (confirm('¿Estás seguro de que deseas eliminar este Switch?')) {
        this.deleteSwitch(id);
      }
    },
  },
  watch: {
    user_loaded(val, oldVal) {
      if (!val) {
        this.$router.push('/login')
      }
    }
  }
}
</script>
