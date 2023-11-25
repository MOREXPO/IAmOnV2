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
          <span class="fw-bold text-bg-white">{{user.username}}</span>
        </div>
        <a v-else class="btn me-3" href="/logout">
          <i class="fas fa-fw fa-sign-out-alt text-bg-white"></i>
          Salir
        </a>
      </div>
    </v-app-bar>
    <v-main>
      <v-container fluid>
        <router-view />
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
export default {
  components: {
    RouterLink,
    RouterView,
  },
  computed: {
    ...mapState(userStore, {
      user: store => store.user,
      user_loaded: store => store.loaded,
      user_loading: store => store.loading,
    }),
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
