<template>
  <v-app id="inspire">
    <toolbar v-if="authenticated && user"></toolbar>
    <v-sheet id="main-content" class="overflow-y-auto" :max-height="getWindowWHeight()">
      <v-container fluid class="main-content">
        <router-view></router-view>
      </v-container>
    </v-sheet>
    <v-footer dark app color="blue-grey darken-1" class="caption font-weight-light">
      <span>{{ title }} {{ version }}</span>
      <v-spacer></v-spacer>
      <span>{{ currentDateTime }}</span>
    </v-footer>
    <v-overlay :value="loader">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
  </v-app>
</template>

<script>
export default {
  props: ["title"],
  data: () => ({
    version: "v2.0",
    currentDateTime: "",
    authenticated: auth.check(),
    user: auth.user,
    loader: false
  }),
  mounted() {
    Event.$on("loader", value => {
      this.loader = value;
    });
    Event.$on("userLogin", () => {
      this.authenticated = true;
      this.user = auth.user;
    });
    Event.$on("userLogout", () => {
      this.authenticated = auth.check();
      this.user = auth.user;
      this.$router.push("/login");
    });

    if (!this.authenticated) {
      if (this.$router.currentRoute.meta.middlewareAuth) {
        this.$router.push("/login").catch(err => {});
      }
    }
  },
  methods: {
    getWindowWHeight() {
      return window.innerHeight;
    }
  },
  created() {
    setInterval(() => {
      this.currentDateTime = this.$moment().format(
        "dddd, Do MMM YYYY, H:mm:ss"
      );
    }, 1000);
  }
};
</script>
