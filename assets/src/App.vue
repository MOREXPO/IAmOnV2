<template>
  <v-app>
    <v-app-bar app class="text-bg-dark">
      <v-toolbar-title>
        <router-link to="/" class="navbar-brand">
          <img src="./assets/logo.svg" class="me-3" :width="60" :height="60" alt="naranja" loading="lazy" />
        </router-link>
      </v-toolbar-title>

      <div class="flex-grow-1"></div>
      <div class="flex-grow-1"></div>
      <div class="flex-grow-1"></div>
      <div class="flex-grow-1"></div>
      <div>
        <form class="tabber">
          <label for="t1">{{ language != 'es' ? words['espanol'][language] : '' }}</label>
          <input @click="setLanguage('es')" id="t1" name="food" type="radio" checked>
          <label for="t2">{{ language != 'en' ? words['ingles'][language] : '' }}</label>
          <input @click="setLanguage('en')" id="t2" name="food" type="radio">
          <div class="blob"></div>
        </form>
      </div>
      <div class="flex-grow-1"></div>
      <div class="d-flex align-center">
        <div v-if="user_loaded" class="me-3">
          <i class="fas fa-user"></i>
          <span class="fw-bold text-bg-white">{{ user.username }}</span>

          <v-btn class="btn me-3 text-bg-white" @click="logout">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            {{ words['salir'][language] }}
          </v-btn>
        </div>
      </div>
    </v-app-bar>
    <v-main>
      <v-container fluid>
        <loader v-if="user_loading" />
        <router-view v-else />
      </v-container>
    </v-main>
    <v-footer app class="text-bg-dark">
      <h6>
        <span class="mb-3 ms-3 mb-md-0 text-body-white">© 2023 Company, Inc</span>
      </h6>
    </v-footer>
  </v-app>
</template>


<script>
import { RouterLink, RouterView } from 'vue-router';
import { mapState, mapActions } from 'pinia';
import { userStore } from '/src/stores/user';
import { switchStore } from '/src/stores/switch';
import { userSwitchStore } from '/src/stores/userSwitch';
import { translateStore } from '/src/stores/translate';
import Loader from '/src/components/Loader.vue';
export default {
  components: {
    RouterLink,
    RouterView,
    Loader
  },
  mounted() {
    this.setLanguage((navigator.language || navigator.userLanguage).split('-')[0]);
    if (sessionStorage.getItem('user')) {
      this.setLoaded(true);
      this.setUser(JSON.parse(sessionStorage.getItem('user')));
    }
    if (sessionStorage.getItem('token')) {
      this.setToken(sessionStorage.getItem('token'));
    }
    if (!this.switches_loaded) {
      this.getSwitchess();
    }
    if (!this.user_switches_loaded) {
      this.getUserSwitchess();
    }
    if (!this.app_users_loaded) {
      this.getAppUsers();
    }
  },
  watch: {
    user_loaded(val, oldVal) {
      if (val) {
        if (!this.switches_loaded) {
          this.getSwitchess();
        }
        if (!this.user_switches_loaded) {
          this.getUserSwitchess();
        }
        if (!this.app_users_loaded) {
          this.getAppUsers();
        }
      }
    }
  },
  computed: {
    ...mapState(userStore, {
      user: store => store.user,
      user_loaded: store => store.loaded,
      user_loading: store => store.loading,
      app_users_loaded: store => store.app_users_loaded,
    }),
    ...mapState(switchStore, {
      switches_loaded: store => store.loaded,
    }),
    ...mapState(userSwitchStore, {
      user_switches_loaded: store => store.loaded,
    }),
    ...mapState(translateStore, {
      words: store => store.words,
      language: store => store.language,
    }),
  },
  methods: {
    ...mapActions(switchStore, ["getSwitchess"]),
    ...mapActions(userSwitchStore, ["getUserSwitchess"]),
    ...mapActions(userStore, ["setUser", "setToken", "setLoaded", "logout", "getAppUsers"]),
    ...mapActions(translateStore, ["setLanguage"]),
    changeLanguage(language) {
      // Puedes agregar lógica aquí para cambiar el idioma en tu aplicación
      this.setLanguage(language);
    },
    getFlagIcon(language) {
      // Mapea el código del idioma al nombre de la clase de la bandera de FontAwesome
      switch (language) {
        case 'es':
          return 'flag-icon flag-icon-es';
        case 'en':
          return 'flag-icon flag-icon-gb';
        // Agrega más casos según sea necesario
        default:
          return '';
      }
    }
  }
}
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css?family=Concert+One&display=swap");

svg {
  display: none;
}

.tabber {
  position: relative;
  display: flex;
  align-items: stretch;
  justify-content: stretch;
}

.tabber label {
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  padding: 3rem;
  cursor: pointer;
  will-change: transform;
  transform: translateZ(0px);
  transition: transform 125ms ease-in-out, filter 125ms ease-in-out;
}

.tabber label:hover {
  transform: scale(1.15);
}

.tabber input[type=radio] {
  display: none;
}

.tabber input[type=radio]#t1~.blob {
  transform-origin: right center;
}

.tabber input[type=radio]#t2~.blob {
  transform-origin: left center;
}

.tabber input[type=radio]#t1:checked~.blob {
  background: url('https://upload.wikimedia.org/wikipedia/commons/thumb/3/32/Flag_of_Spain_%28Civil%29.svg/220px-Flag_of_Spain_%28Civil%29.svg.png');
  background-size: cover;
  background-size: contain;
  background-position: center;
  -webkit-animation-name: stretchyRev;
  animation-name: stretchyRev;
}

.tabber input[type=radio]#t2:checked~.blob {
  background: url('https://media.istockphoto.com/id/479199262/es/foto/encuadre-completo-imagen-de-inglaterra-bandera.jpg?s=612x612&w=0&k=20&c=SwMP7VR64pbeG-fGCQSbP3e1jYLV0w-79bNyDG5z5Cc=');
  background-size: cover;
  background-size: contain;
  background-position: center;
  -webkit-animation-name: stretchy;
  animation-name: stretchy;
}

.tabber .blob {
  top: 0;
  left: 0;
  width: 50%;
  height: 100%;
  position: absolute;
  z-index: -1;
  border-radius: 4rem;
  -webkit-animation-duration: 0.5s;
  animation-duration: 0.5s;
  -webkit-animation-direction: forwards;
  animation-direction: forwards;
  -webkit-animation-iteration-count: 1;
  animation-iteration-count: 1;
  -webkit-animation-fill-mode: forwards;
  animation-fill-mode: forwards;
  transition: transform 150ms ease, background 150ms ease;
  filter: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" version="1.1"><defs><filter id="goo"><feGaussianBlur in="SourceGraphic" stdDeviation="10" result="blur" /><feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9" result="goo" /><feComposite in="SourceGraphic" in2="goo" operator="atop"/></filter></defs></svg>#goo');
}

.tabber .blob:before,
.tabber .blob:after {
  display: block;
  content: "";
  position: absolute;
  top: 0;
  background-color: inherit;
  height: 100%;
  width: 50%;
  border-radius: 100%;
  transform: scale(1.15);
  transition: transform 150ms ease;
  -webkit-animation-name: pulse;
  animation-name: pulse;
  -webkit-animation-duration: 0.5s;
  animation-duration: 0.5s;
  -webkit-animation-iteration-count: infinite;
  animation-iteration-count: infinite;
  -webkit-animation-direction: alternate;
  animation-direction: alternate;
}

.tabber .blob:before {
  left: 0;
  -webkit-animation-delay: 0.15s;
  animation-delay: 0.15s;
}

.tabber .blob:after {
  right: 0;
}

@-webkit-keyframes stretchy {
  0% {
    transform: translateX(0) scaleX(1);
  }

  50% {
    transform: translateX(0) scaleX(2);
  }

  100% {
    transform: translateX(100%) scaleX(1);
  }
}

@keyframes stretchy {
  0% {
    transform: translateX(0) scaleX(1);
  }

  50% {
    transform: translateX(0) scaleX(2);
  }

  100% {
    transform: translateX(100%) scaleX(1);
  }
}

@-webkit-keyframes stretchyRev {
  0% {
    transform: translateX(100%) scaleX(1);
  }

  50% {
    transform: translateX(0) scaleX(2);
  }

  100% {
    transform: translateX(0) scaleX(1);
  }
}

@keyframes stretchyRev {
  0% {
    transform: translateX(100%) scaleX(1);
  }

  50% {
    transform: translateX(0) scaleX(2);
  }

  100% {
    transform: translateX(0) scaleX(1);
  }
}

@-webkit-keyframes pulse {

  0%,
  50% {
    transform: scaleX(1);
  }

  25%,
  75% {
    transform: scaleX(1.5);
  }
}

@keyframes pulse {

  0%,
  50% {
    transform: scaleX(1);
  }

  25%,
  75% {
    transform: scaleX(1.5);
  }
}
</style>
