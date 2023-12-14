import { defineStore } from 'pinia';
import axios from 'axios';
export const translateStore = defineStore({
    id: "translate",
    state: () => ({
        language:"es",
        words: {
            usuario: {
                es: "Usuario",
                en: "User"
            },
            contrasena: {
                es: "Contraseña",
                en: "Password"
            },
            "introduzca la contrasena": {
                es: "Introduzca la contraseña",
                en: "Enter the password"
            },
            entrar: {
                es: "Entrar",
                en: "Log in"
            },
            salir: {
                es: "Salir",
                en: "Exit"
            },
            espanol: {
                es: "Español",
                en: "Spanish"
            },
            ingles: {
                es: "Ingles",
                en: "English"
            },
            registrar: {
                es: "Registrar",
                en: "Register"
            },
            "registrarse ahora": {
                es: "Registrarse ahora",
                en: "Register now"
            },
            "repetir contrasena": {
                es: "Repetir Contraseña",
                en: "Repeat Password"
            },
            "introduce de nuevo la contrasena": {
                es: "Introduce de nuevo la contraseña",
                en: "Re-enter the password"
            },
            "mis interruptores": {
                es: "Mis interruptores",
                en: "My switches"
            },
            "mis interruptores suscritos": {
                es: "Mis interruptores suscritos",
                en: "My subscribed switches"
            },
            "fecha ultimo encendido": {
                es: "Fecha Último Encendido",
                en: "Date Last Ignition"
            },
            "tiempo de encendido": {
                es: "Tiempo de encendido",
                en: "Time on"
            },
            "buscar interruptores": {
                es: "Buscar interruptores",
                en: "Search for switches"
            },
            propietario: {
                es: "Propietario",
                en: "Owner"
            },
            seguir: {
                es: "Seguir",
                en: "Follow"
            },
            "dejar de seguir": {
                es: "Dejar de seguir",
                en: "Stop following"
            },
            "crear nuevo switch": {
                es: "Crear Nuevo Switch",
                en: "Create New Switch"
            },
            nombre: {
                es: "Nombre",
                en: "Name"
            },
            "descripcion (opcional)": {
                es: "Descripción (Opcional)",
                en: "Description (Optional)"
            },
            crear: {
                es: "Crear",
                en: "Create"
            },
            "detalle del switch": {
                es: "Detalle del Switch",
                en: "Switch detail"
            },
            "uri publico": {
                es: "URI publico",
                en: "Public URI"
            },
            "uri privado": {
                es: "URI privado",
                en: "Private URI"
            },
            copiar: {
                es: "Copiar",
                en: "Copy"
            },
            cerrar: {
                es: "Cerrar",
                en: "Close"
            },
            encender: {
                es: "Encender",
                en: "Turn on"
            },
            encendido: {
                es: "Encendido",
                en: "Power on"
            },
            apagado: {
                es: "Apagado",
                en: "Power off"
            },
            segundos: {
                es: "segundos",
                en: "seconds"
            },
            minutos: {
                es: "minutos",
                en: "minutes"
            },
            "el nombre es obligatorio": {
                es: "El nombre es obligatorio",
                en: "The name is required"
            },
            "configurar interruptor": {
                es: "Configurar interruptor",
                en: "Configure switch"
            },
            "elegir el idioma": {
                es: "Elegir el idioma",
                en: "Choose language"
            },
        },
        loaded: false,
        loading: false,
    }),
    getters: {
        getWords: (state) => {
            return state.words
        },
        getLoaded: (state) => {
            return state.loaded
        },
        getLoading: (state) => {
            return state.loading
        },
    },
    actions: {
        setLanguage(language: any) {
            this.language = language
        },
        setWords(words: any) {
            this.words = words
        },
        setLoading(valor: any) {
            this.loading = valor
        },
        setLoaded(valor: any) {
            this.loaded = valor
        },
    }
});
