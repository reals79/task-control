/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

import router from "./routes";

import "./plugins/moment";
import vuetify from "./plugins/vuetify";
import "./plugins/vuetify-dialog";
import "./plugins/laravel-permissions";

import Auth from "./auth.js";
window.auth = new Auth();

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component(
    "app-layout",
    require("./components/_partials/Layout.vue").default
);
Vue.component("toolbar", require("./components/_partials/Toolbar.vue").default);
Vue.component("login-form", require("./components/LoginForm.vue").default);
Vue.component("tasks", require("./components/Tasks.vue").default);

// Add a request interceptor
axios.interceptors.request.use(
    function(config) {
        // Do something before request is sent
        Event.$emit("loader", true);
        return config;
    },
    function(error) {
        // Do something with request error
        console.log("req-error", error);
        Event.$emit("loader", false);
        return Promise.reject(error);
    }
);

// Add a response interceptor
axios.interceptors.response.use(
    function(response) {
        // Any status code that lie within the range of 2xx cause this function to trigger
        // Do something with response data
        Event.$emit("loader", false);
        if (response.data) {
            if (response.data.message)
                Vue.prototype.$dialog.message.info(response.data.message, {
                    position: "top"
                });
        }
        return response;
    },
    function(error) {
        // Any status codes that falls outside the range of 2xx cause this function to trigger
        // Do something with response error
        Event.$emit("loader", false);
        if (error.response) {
            console.log(error.response);

            let message = "";
            if (error.response.data.errors) {
                _.forEach(error.response.data.errors, value => {
                    message += value[0] + "<br>";
                });
            } else
                message = error.response.data.message || "Что-то пошло не так!";

            Vue.prototype.$dialog.message.error(message, {
                position: "top"
            });

            if (error.response.status === 401) {
                auth.logout();
            }
            if (error.response.status === 403) {
                //auth.logout();
            }
            if (error.response.data.redirect) {
                router.push(error.response.data.redirect);
            }
        }
        return Promise.reject(error);
    }
);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: "#app",
    data: {
        title: "Задачи",
        currentUser: auth.user
    },
    methods: {},
    vuetify,
    router
});

export default app;
