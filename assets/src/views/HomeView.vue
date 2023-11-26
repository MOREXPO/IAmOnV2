<template>
  <main>
    <h1>HOMEVIEW</h1>
  </main>
</template>
<script>
import { mapState, mapActions } from 'pinia';
import { userStore } from '/src/stores/user';
export default {
  data: () => ({

  }),
  computed: {
    ...mapState(userStore, {
      user: store => store.user,
      user_loaded: store => store.loaded,
      user_loading: store => store.loading,
    }),
  },
  mounted() {
    if (!this.user_loaded)
      this.$router.push('/login')
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
