<template>
    <div>
        <v-card class="mx-auto pa-12 pb-8 m-4" elevation="8" max-width="448" rounded="lg">
            <div class="text-subtitle-1 text-medium-emphasis">Usuario</div>

            <v-text-field density="compact" placeholder="usuario" prepend-inner-icon="mdi-email-outline"
                variant="outlined"></v-text-field>

            <div class="text-subtitle-1 text-medium-emphasis">Email</div>

            <v-text-field density="compact" placeholder="email" prepend-inner-icon="mdi-email-outline"
                variant="outlined"></v-text-field>

            <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
                Contraseña
            </div>

            <v-text-field :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'" :type="visible ? 'text' : 'password'"
                density="compact" placeholder="Introduce la contraseña" prepend-inner-icon="mdi-lock-outline"
                variant="outlined" @click:append-inner="visible = !visible"></v-text-field>

            <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
                Repetir Contraseña
            </div>

            <v-text-field :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'" :type="visible ? 'text' : 'password'"
                density="compact" placeholder="Enter your password" prepend-inner-icon="mdi-lock-outline" variant="outlined"
                @click:append-inner="visible = !visible"></v-text-field>

            <v-card class="mb-12" color="surface-variant" variant="tonal">
            </v-card>


            <v-btn block class="mb-8" color="blue" size="large" variant="tonal">
                Registrar
            </v-btn>


            <v-card-text class="text-center">
                <router-link to="/login" class="text-blue text-decoration-none">
                    Entrar
                    <v-icon icon="mdi-chevron-right"></v-icon>
                </router-link>
            </v-card-text>
        </v-card>
    </div>
</template>
<script>
export default {
    data: () => ({
        visible: false,
    }),
}
</script>