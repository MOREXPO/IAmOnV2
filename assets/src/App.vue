<template>
  <v-app>
    <v-app-bar app class="text-bg-dark">
      <v-toolbar-title>
        <a class="navbar-brand" href="/">
          <img src="./assets/logo.svg" class="me-3" :width="60" :height="60" alt="naranja" loading="lazy" />
        </a>
      </v-toolbar-title>

      <div class="flex-grow-1"></div>

      <div class="d-flex align-center">
        <div v-if="user_loaded" class="me-3">
          <i class="fas fa-user"></i>
          <span class="fw-bold text-bg-white">{{ user.username }}</span>

          <v-btn class="btn me-3 text-bg-white" @click="logout">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            Salir
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
import Loader from '/src/components/Loader.vue';
export default {
  components: {
    RouterLink,
    RouterView,
    Loader
  },
  mounted() {
    if (sessionStorage.getItem('user')) {
      this.setLoaded(true);
      this.setUser(JSON.parse(sessionStorage.getItem('user')));
    }
    if (sessionStorage.getItem('token')) {
      this.setToken(sessionStorage.getItem('token'));
    }
  },
  computed: {
    ...mapState(userStore, {
      user: store => store.user,
      user_loaded: store => store.loaded,
      user_loading: store => store.loading,
    }),
  },
  methods: {
    ...mapActions(userStore, ["setUser", "setToken", "setLoaded", "logout"]),
  }
};
</script>

<style scoped>
#particles-js {
  background: #003298;
}

@media (max-width: 768px) {
  #particles-js {
    position: relative;
    width: 100%;
    height: 100vh;
    background-color: #003298;
  }
}
</style>
