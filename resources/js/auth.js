import router from "./routes";

class Auth {
    constructor() {
        this.token = window.localStorage.getItem("token");

        let userData = window.localStorage.getItem("user");
        this.user =
            userData && userData != "undefined" ? JSON.parse(userData) : null;

        if (this.token) {
            axios.defaults.headers.common["Authorization"] =
                "Bearer " + this.token;
            this.getUser();
        }
    }

    login(username, password) {
        axios
            .post("/login", { email: username, password: password })
            .then(({ data }) => {
                window.localStorage.setItem("token", data.token);
                window.localStorage.setItem("user", JSON.stringify(data.user));

                axios.defaults.headers.common["Authorization"] =
                    "Bearer " + data.token;

                this.token = data.token;
                this.user = data.user;
                this.getPermissions();

                Event.$emit("userLogin");
                router.push("/");
            })
            .catch(error => {});
    }

    logout() {
        axios
            .post("/logout")
            .then(({ data }) => {
                window.localStorage.removeItem("token");
                window.localStorage.removeItem("user");

                this.token = null;
                this.user = null;

                Event.$emit("userLogout");
            })
            .catch(error => {});
    }

    check() {
        return !!(this.token && this.user);
    }

    getUser() {
        axios.get("/api/user").then(({ data }) => {
            this.user = data;
            window.localStorage.setItem("user", JSON.stringify(data));
            Event.$emit("userUpdateInfo");
            this.getPermissions();
        });
    }

    async getPermissions() {
        const { data: permissions } = await axios.get("/api/permissions");
        const { data: roles } = await axios.get("/api/roles");
        Vue.prototype.$laravel.setPermissions(permissions);
        Vue.prototype.$laravel.setRoles(roles);
    }
}

export default Auth;
