<template>
  <main>
    <h1>{{ this.uuid }}</h1>
  </main>
</template>
<script>
import { mapState, mapActions } from 'pinia';
import { userStore } from '/src/stores/user';
export default {
  data: () => ({
    switch: {},
  }),
  props: {
    // Definir 'id' como una propiedad que espera recibir del enrutador
    uuid: {
      type: String,
      required: true
    }
  },
  computed: {
    ...mapState(userStore, {
      user: store => store.user,
      user_loaded: store => store.loaded,
      user_loading: store => store.loading,
    }),
  },
  mounted() {
    /*if (!this.user_loaded)
      this.$router.push('/login')*/
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
