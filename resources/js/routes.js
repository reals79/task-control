import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

let routes = [
    {
        path: "/",
        name: "home",
        component: require("./components/Home.vue").default,
        meta: { middlewareAuth: true }
    },
    {
        path: "/register",
        name: "register",
        component: require("./components/RegisterForm.vue").default,
        meta: {}
    },
    {
        path: "/email/verify",
        name: "email-verify",
        component: require("./components/EmailVerify.vue").default,
        meta: {}
    },
    {
        path: "/login",
        name: "login",
        component: require("./components/LoginForm.vue").default,
        meta: {}
    },
    {
        path: "/password/reset/:token?",
        name: "password.request",
        component: require("./components/PasswordResetForm.vue").default,
        props: route => ({ _email: route.query.email }),
        meta: {}
    },
    {
        path: "/companies",
        name: "companies",
        component: require("./components/Companies.vue").default,
        meta: { middlewareAuth: true }
    },
    {
        path: "/users",
        name: "users",
        component: require("./components/Users.vue").default,
        meta: { middlewareAuth: true }
    },
    {
        path: "/departments",
        name: "departments",
        component: require("./components/Departments.vue").default,
        meta: { middlewareAuth: true }
    },
    {
        path: "/objects",
        name: "objects",
        component: require("./components/Objects.vue").default,
        meta: { middlewareAuth: true }
    },
    {
        path: "/profile",
        name: "profile",
        component: require("./components/Profile.vue").default,
        meta: { middlewareAuth: true }
    }
];

const router = new VueRouter({
    mode: "history",
    routes
});

export default router;
