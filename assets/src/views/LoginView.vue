<template>
    <div>
        <v-card class="mx-auto pa-12 pb-8 m-4" elevation="8" max-width="448" rounded="lg">
            <div class="text-subtitle-1 text-medium-emphasis">{{ words['usuario'][language] }}</div>

            <v-text-field v-model="username" density="compact" :placeholder="words['usuario'][language].toLowerCase()"
                prepend-inner-icon="mdi-account-outline" variant="outlined"
                v-on:keyup.enter="login({ 'username': username, 'password': password })"></v-text-field>

            <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
                {{ words['contrasena'][language] }}
            </div>

            <v-text-field v-model="password" :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                :type="visible ? 'text' : 'password'" density="compact"
                :placeholder="words['introduzca la contrasena'][language]" prepend-inner-icon="mdi-account-key-outline"
                variant="outlined" @click:append-inner="visible = !visible"
                v-on:keyup.enter="login({ 'username': username, 'password': password })"></v-text-field>

            <v-card class="mb-12" color="surface-variant" variant="tonal">
            </v-card>


            <v-btn @click="login({ 'username': username, 'password': password })" block class="mb-8" color="blue"
                size="large" variant="tonal">
                {{ words['entrar'][language] }}
            </v-btn>
            <v-card-text class="text-center">
                <router-link to="/register" class="text-blue text-decoration-none">
                    {{ words['registrarse ahora'][language] }}
                    <v-icon icon="mdi-chevron-right"></v-icon>
                </router-link>
            </v-card-text>
        </v-card>
    </div>
</template>
<script>
import { mapState, mapActions } from 'pinia';
import { userStore } from '/src/stores/user';
import { translateStore } from '/src/stores/translate';
export default {
    data: () => ({
        username: null,
        password: null,
        visible: false,
    }),
    computed: {
        ...mapState(translateStore, {
            words: store => store.words,
            language: store => store.language,
        }),
        ...mapState(userStore, {
            user_loaded: store => store.loaded,
        }),
    },
    methods: {
        ...mapActions(userStore, ["login"])
    },
    mounted() {
        if (this.user_loaded)
            this.$router.push('/')
    }
}
</script>