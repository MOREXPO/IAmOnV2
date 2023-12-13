<template>
    <div>
        <v-card class="mx-auto pa-12 pb-8 m-4" elevation="8" max-width="448" rounded="lg">
            <div class="text-subtitle-1 text-medium-emphasis">{{ words['usuario'][language] }}</div>

            <v-text-field v-on:keyup.enter="registrar" v-model="username" density="compact"
                :placeholder="words['usuario'][language].toLowerCase()" prepend-inner-icon="mdi-account-outline"
                variant="outlined"></v-text-field>

            <div class="text-subtitle-1 text-medium-emphasis">Email</div>

            <v-text-field v-on:keyup.enter="registrar" v-model="email" density="compact" placeholder="email"
                prepend-inner-icon="mdi-email-outline" variant="outlined"></v-text-field>

            <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
                {{ words['contrasena'][language] }}
            </div>

            <v-text-field v-on:keyup.enter="registrar" v-model="password"
                :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'" :type="visible ? 'text' : 'password'"
                density="compact" :placeholder="words['introduzca la contrasena'][language]"
                prepend-inner-icon="mdi-account-key-outline" variant="outlined"
                @click:append-inner="visible = !visible"></v-text-field>

            <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
                {{ words['repetir contrasena'][language] }}
            </div>

            <v-text-field v-on:keyup.enter="registrar" v-model="passwordRepeat"
                :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'" :type="visible ? 'text' : 'password'"
                density="compact" :placeholder="words['introduce de nuevo la contrasena'][language]"
                prepend-inner-icon="mdi-account-key-outline" variant="outlined"
                @click:append-inner="visible = !visible"></v-text-field>

            <v-card class="mb-12" color="surface-variant" variant="tonal">
            </v-card>


            <v-btn @click="registrar" block class="mb-8" color="blue" size="large" variant="tonal">
                {{ words['registrar'][language] }}
            </v-btn>


            <v-card-text class="text-center">
                <router-link to="/login" class="text-blue text-decoration-none">
                    {{ words['entrar'][language] }}
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
        email: null,
        password: null,
        passwordRepeat: null,
        visible: false,
    }),
    computed: {
        ...mapState(userStore, {
            user: store => store.user,
            user_loaded: store => store.loaded,
            user_loading: store => store.loading,
        }),
        ...mapState(translateStore, {
            words: store => store.words,
            language: store => store.language,
        }),
    },
    methods: {
        ...mapActions(userStore, ["registration"]),
        registrar() {
            let username_comp = this.username ? this.username.trim() || null : null;
            let email_comp = this.email ? this.email.trim() || null : null;
            let password_comp = this.password ? this.password.trim() || null : null;
            let passwordRepeat_comp = this.passwordRepeat ? this.passwordRepeat.trim() || null : null;
            if (!username_comp) {
                alert("El campo de usuario está vacío");
                return;
            }
            if (!password_comp) {
                alert("El campo de contraseña está vacío");
                return;
            }

            if (!passwordRepeat_comp) {
                alert("El campo de repetir contraseña está vacío");
                return;
            }

            if (password_comp !== passwordRepeat_comp) {
                alert("Las contraseñas no coinciden");
                return;
            }
            if (!email_comp) {
                this.registration({ 'username': username_comp, 'password': password_comp });
                return;
            }

            this.registration({ 'username': username_comp, 'email': email_comp, 'password': password_comp });
        }

    },
    mounted() {
        if (this.user_loaded)
            this.$router.push('/')
    }
}
</script>