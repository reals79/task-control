<template>
  <div>
    <v-content>
      <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
          <v-col cols="12" class="m-logo">
            <v-row justify="center">
              <img src="/images/logo-csn-100.png" />
            </v-row>
          </v-col>
          <v-col cols="12" sm="8" md="4">
            <v-card class="elevation-12">
              <v-toolbar color="primary" dark flat>
                <v-toolbar-title>Сброс пароля</v-toolbar-title>
              </v-toolbar>
              <ValidationObserver ref="observer" v-slot="{ validate, invalid, handleSubmit }">
                <v-form @submit.prevent="handleSubmit(reset)">
                  <v-card-text>
                    <ValidationProvider v-slot="{ errors }" name="E-mail" rules="required|email">
                      <v-text-field
                        v-model="email"
                        label="E-mail"
                        prepend-icon="mdi-email-outline"
                        :error-messages="errors"
                        required
                      ></v-text-field>
                    </ValidationProvider>
                    <template v-if="$route.params.token">
                      <ValidationProvider
                        v-slot="{ errors }"
                        vid="confirm"
                        name="Новый Пароль"
                        :rules="`required`"
                      >
                        <v-text-field
                          v-model="password"
                          label="Новый Пароль"
                          prepend-icon="mdi-lock-outline"
                          :error-messages="errors"
                          :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                          :type="showPassword ? 'text' : 'password'"
                          @click:append="showPassword = !showPassword"
                        ></v-text-field>
                      </ValidationProvider>

                      <ValidationProvider
                        v-slot="{ errors }"
                        :rules="`${ password ? 'required|password:@confirm' : '' }`"
                        name="Подтверждение пароля"
                      >
                        <v-text-field
                          v-model="password_confirmation"
                          label="Подтвердите пароль"
                          prepend-icon="mdi-lock"
                          :error-messages="errors"
                          :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                          :type="showPassword ? 'text' : 'password'"
                          @click:append="showPassword = !showPassword"
                        ></v-text-field>
                      </ValidationProvider>
                    </template>
                  </v-card-text>
                  <v-card-actions>
                    <v-btn to="/login" color="primary" outlined>Аутентификация</v-btn>
                    <v-spacer />
                    <v-btn
                      v-if="$route.params.token"
                      type="submit"
                      :disabled="invalid"
                      color="primary"
                    >Сбросить пароль</v-btn>
                    <v-btn v-else type="submit" :disabled="invalid" color="primary">Отправить ссылку</v-btn>
                  </v-card-actions>
                </v-form>
              </ValidationObserver>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-content>
  </div>
</template>

<script>
import { required, email } from "vee-validate/dist/rules";
import {
  extend,
  ValidationObserver,
  ValidationProvider,
  setInteractionMode
} from "vee-validate";

setInteractionMode("eager");

extend("required", {
  ...required,
  message: '"{_field_}" не может быть пустым'
});

extend("email", {
  ...email,
  message: '"{_field_}" не корректный'
});

extend("password", {
  params: ["target"],
  validate(value, { target }) {
    return value === target;
  },
  message: "Пароли не совпадают"
});

export default {
  components: { ValidationProvider, ValidationObserver },
  props: ["_email"],
  data: () => ({
    email: "",
    password: "",
    password_confirmation: "",
    showPassword: false
  }),
  mounted() {
    this.email = this._email;
  },
  methods: {
    reset() {
      let data = {
        email: this.email,
        password: this.password,
        password_confirmation: this.password_confirmation,
        token: this.$route.params.token
      };

      if (this.$route.params.token) {
        axios
          .post("/password/reset", data)
          .then(response => {
            this.$router.push("/login");
          })
          .catch(error => {});
      } else {
        axios
          .post("/password/email", data)
          .then(response => {
            this.$router.push("/login");
          })
          .catch(error => {});
      }
    }
  }
};
</script>
