<template>
  <div class="container my-5">
    <div class="row">
      <div class="col-12 col-md-4">
        <v-card class="mx-auto" color="grey-lighten-3" max-width="400">
          <v-card-text>
            <v-text-field :loading="loadingSearch" density="compact" variant="solo" label="Buscar interruptores"
              append-inner-icon="mdi-magnify" single-line hide-details @click:append-inner="onClick"></v-text-field>
          </v-card-text>
        </v-card>
      </div>
    </div>

    <div class="row my-5">
      <div class="col-12">
        <h2 class="pb-2 border-bottom d-flex justify-content-between">Mis interruptores
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
            <h1 class="fw-bold mb-0 fs-2">Crear Nuevo Switch</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body p-5 pt-0">
            <div class="form-floating mb-3">
              <v-text-field v-model="nombre_create" label="Nombre" hide-details="auto"></v-text-field>
              <div v-if="error" class="alert alert-danger" role="alert">
                El nombre es obligatorio
              </div>
            </div>
            <div class="form-floating mb-3">
              <v-textarea v-model="description_create" name="input-7-1" variant="filled" label="Descripción (Opcional)"
                auto-grow></v-textarea>
            </div>
            <v-btn class="w-100 mb-2 btn btn-lg rounded-3 btn-primary" @click="crearSwitch">Crear</v-btn>
          </div>
        </div>
      </div>
    </div>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <div v-for="mi_switch in mis_switches" class="col my-4">
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
              <input v-else class="form-check-input" type="checkbox" role="switch" :id="'flexSwitchCheck' + mi_switch.id"
                checked>
            </div>
            </p>
          </div>
          <div class="modal fade" :id="'switchModal' + mi_switch.id" tabindex="-1"
            :aria-labelledby="'switchModalLabel' + mi_switch.id" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" :id="'switchModalLabel' + mi_switch.id">Configurar interruptor
                  </h5>
                  <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form :id="'switchForm' + mi_switch.id">
                    <div class="form-group">
                      <label for="onTime">Tiempo de Encendido (minutos):</label>
                      <input type="number" class="form-control" :id="'onTime' + mi_switch.id" name="onTime" min="1"
                        max="120" value="60">
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                  <button type="button" class="btn btn-primary">Encender</button>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <div class="d-flex justify-content-between">
              <a href="#" class="fs-4" data-bs-toggle="modal" :data-bs-target="'#modalUri' + mi_switch.id">
                <v-icon icon="mdi-share-variant"></v-icon>
              </a>
              <div class="fs-4">{{ mi_switch.followers.length }}&nbsp<v-icon icon="mdi-account"></v-icon>
              </div>
              <div>
                <!-- Agrega enlaces o botones según tus necesidades -->
                <a :href="'http://localhost/api/remove-switches/' + mi_switch.id"
                  onclick="return confirm('¿Estás seguro de que deseas eliminar este Switch?')" class="btn btn-danger">
                  <v-icon icon="mdi-delete"></v-icon>
                </a>
              </div>
            </div>
          </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" :id="'modalUri' + mi_switch.id" tabindex="-1" aria-labelledby="modalUriLabel"
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
                    <span class="input-group-text" id="inputGroup-sizing-sm">URI publico</span>
                    <input type="text" class="form-control" :id="'floatingInputPublic' + mi_switch.id"
                      :value="mi_switch.publicUri" readonly>
                    <button :id="'copyButtonPublic' + mi_switch.id" class="btn btn-outline-secondary">
                      <v-icon icon="mdi-clipboard"></v-icon>
                      Copiar
                    </button>
                  </div>
                </div>
                <div class="form-floating mb-3">
                  <div class="input-group">
                    <span class="input-group-text" id="inputGroup-sizing-sm">URI privado</span>
                    <input type="text" class="form-control" :id="'floatingInputPrivate' + mi_switch.id"
                      :value="mi_switch.privateUri" readonly>
                    <button :id="'copyButtonPrivate' + mi_switch.id" class="btn btn-outline-secondary">
                      <v-icon icon="mdi-clipboard"></v-icon>
                      Copiar
                    </button>
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
        <h2 class="pb-2 border-bottom d-flex justify-content-between">Mis interruptores suscritos
        </h2>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content rounded-4 shadow">
          <div class="modal-header p-5 pb-4 border-bottom-0">
            <h1 class="fw-bold mb-0 fs-2">Crear Nuevo Switch</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body p-5 pt-0">
            <div class="form-floating mb-3">
              <v-text-field v-model="nombre_create" label="Nombre" hide-details="auto"></v-text-field>
              <div v-if="error" class="alert alert-danger" role="alert">
                El nombre es obligatorio
              </div>
            </div>
            <div class="form-floating mb-3">
              <v-textarea v-model="description_create" name="input-7-1" variant="filled" label="Descripción (Opcional)"
                auto-grow></v-textarea>
            </div>
            <v-btn class="w-100 mb-2 btn btn-lg rounded-3 btn-primary" @click="crearSwitch">Crear</v-btn>
          </div>
        </div>
      </div>
    </div>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <div v-for="switch_suscrito in switches_suscritos" class="col my-4">
        <div class="card h-100 bg-light">
          <div class="card-body" :class="switch_suscrito.state ? 'bg-success-subtle' : 'bg-danger-subtle'">
            <h5 class="card-title">
              {{ switch_suscrito.name }}
            </h5>
            <p class="card-text">Usuario propietario:
              {{ app_users.find(x => x['@id'] == switch_suscrito.owner).username }}
            </p>

            <p class="card-text">Fecha Último Encendido:
              {{ switch_suscrito.clickDateEnd }}
            </p>

            <!-- Agrega otros detalles de tus switches suscritos según tus datos -->
          </div>
          <div class="card-footer">
            <!-- Agrega enlaces o botones según tus necesidades -->
            <div class="d-flex justify-content-between">
              <div>
                <!-- Agrega detalles del estado o la fecha de encendido según tus datos -->
              </div>
              <div>
                <v-btn class="btn btn-danger">Dejar de seguir</v-btn>
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
export default {
  data: () => ({
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
      user_loading: store => store.loading,
      app_users: store => store.app_users,
      app_users_loaded: store => store.app_users_loaded,
      app_users_loading: store => store.app_users_loading,
    }),
    ...mapState(switchStore, {
      switches: store => store.switches,
      switches_loaded: store => store.loaded,
      switches_loading: store => store.loading,
    }),
    mis_switches() {
      return this.switches.filter(x => x.owner == '/api/users/' + this.user.id);
    },
    switches_suscritos() {
      return this.switches;
    }
  },
  mounted() {
    if (!this.user_loaded)
      this.$router.push('/login')
    if (!this.switches_loaded) {
      this.getSwitchess();
    }
    if (!this.app_users_loaded) {
      this.getAppUsers();
    }
  },
  methods: {
    ...mapActions(switchStore, ["getSwitchess", "createSwitch"]),
    ...mapActions(userStore, ["getAppUsers"]),
    crearSwitch() {
      if (this.nombre_create.trim().length > 0) {
        this.createSwitch(this.nombre_create, this.description_create);
        this.error = false;
      }
      else this.error = true;
    },
    onClick() {
      this.loadingSearch = true
      setTimeout(() => {
        this.loadingSearch = false
        this.loadedSearch = true
      }, 2000)
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
