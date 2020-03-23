<template>
  <v-app-bar
    absolute
    dark
    color="blue-grey"
    shrink-on-scroll
    prominent
    scroll-target="#main-content"
    src="https://picsum.photos/1920/1080?random"
  >
    <template v-slot:img="{ props }">
      <v-img v-bind="props" gradient="to top right, rgba(19,84,122,.5), rgba(128,208,199,.8)"></v-img>
    </template>
    <v-btn to="/" icon>
      <v-icon>mdi-home</v-icon>
    </v-btn>
    <v-toolbar-title>{{ title }}</v-toolbar-title>
    <v-divider class="mx-4" inset vertical></v-divider>
    <v-card color="transparent" flat>
      <div class="title">{{ user.name }}</div>
      <v-select
        v-model="user.company_id"
        :items="companies"
        dense
        hide-details
        hide-selected
        item-text="name"
        item-value="id"
        @change="selCompany"
        v-role="'super-admin'"
      >
        <div slot="prepend" class="mt-1">Организация:</div>
      </v-select>
      <div v-role:unless="'super-admin'" class="subtitle-1">
        Организация:
        <strong>{{ user.company_name }}</strong>
      </div>
      <div class="caption">
        <span v-if="user.position">
          <strong>{{ user.position }}</strong>,
        </span>
        <span v-if="user.department_name">
          отдел:
          <strong>{{ user.department_name }}</strong>
        </span>
        <br />права:
        <strong>{{ user.role_titles }}</strong>
      </div>
    </v-card>
    <v-spacer></v-spacer>

    <v-menu v-role:any="'super-admin|admin'" open-on-hover offset-y>
      <template v-slot:activator="{ on }">
        <v-btn v-on="on" icon>
          <v-icon>mdi-apps</v-icon>
        </v-btn>
      </template>

      <v-list>
        <v-list-item-group v-role="'super-admin'">
          <v-list-item to="/companies">
            <v-list-item-icon>
              <v-icon>mdi-domain</v-icon>
            </v-list-item-icon>
            <v-list-item-title class="font-weight-bold">Организации</v-list-item-title>
          </v-list-item>
          <v-divider></v-divider>
        </v-list-item-group>
        <v-list-item-group v-if="user.company_id" v-role:any="'super-admin|admin'">
          <v-list-item to="/departments">
            <v-list-item-icon>
              <v-icon>mdi-account-supervisor-outline</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Управление отделами</v-list-item-title>
          </v-list-item>
          <v-list-item to="/objects">
            <v-list-item-icon>
              <v-icon>mdi-home-city-outline</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Объекты</v-list-item-title>
          </v-list-item>
          <v-divider></v-divider>
          <v-list-item to="/users">
            <v-list-item-icon>
              <v-icon>mdi-account-multiple-outline</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Пользователи</v-list-item-title>
          </v-list-item>
        </v-list-item-group>
      </v-list>
    </v-menu>

    <v-btn to="/profile" icon>
      <v-icon>mdi-account-cog</v-icon>
    </v-btn>

    <v-btn @click="logout" icon>
      <v-icon>mdi-logout-variant</v-icon>
    </v-btn>
  </v-app-bar>
</template>

<script>
export default {
  data: () => ({
    user: auth.user,
    companies: []
  }),
  computed: {
    title() {
      return this.$root.title;
    }
  },
  created() {
    if (this.$laravel.hasRole("super-admin")) this.getCompanies();
  },
  mounted() {
    Event.$on("userUpdateInfo", value => {
      this.user = auth.user;
    });
  },
  methods: {
    logout() {
      auth.logout();
    },
    getCompanies() {
      axios.get("/companies").then(result => {
        if (result.data) this.companies = result.data;
      });
    },
    selCompany() {
      auth.user.company_id = this.user.company_id;
      window.localStorage.setItem("user", JSON.stringify(auth.user));
      axios
        .put("/users/" + auth.user.id, { company_id: this.user.company_id })
        .then(() => {
          Event.$emit("changeCompany");
        });
    }
  }
};
</script>
