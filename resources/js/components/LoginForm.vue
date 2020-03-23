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
                <v-toolbar-title>Авторизация</v-toolbar-title>
              </v-toolbar>
              <ValidationObserver ref="observer" v-slot="{ validate, invalid, handleSubmit }">
                <v-form @submit.prevent="handleSubmit(login)">
                  <v-card-text>
                    <ValidationProvider v-slot="{ errors }" name="E-mail" rules="required">
                      <v-text-field
                        v-model="email"
                        label="E-mail"
                        prepend-icon="mdi-email-outline"
                        :error-messages="errors"
                        required
                      ></v-text-field>
                    </ValidationProvider>
                    <ValidationProvider v-slot="{ errors }" name="Пароль" :rules="`required`">
                      <v-text-field
                        v-model="password"
                        label="Пароль"
                        prepend-icon="mdi-lock-outline"
                        :error-messages="errors"
                        :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                        :type="showPassword ? 'text' : 'password'"
                        @click:append="showPassword = !showPassword"
                        required
                      ></v-text-field>
                    </ValidationProvider>
                  </v-card-text>
                  <v-card-actions>
                    <v-btn to="/register" color="primary" outlined>Регистрация</v-btn>
                    <v-spacer />
                    <v-btn @click="resetPassword()" x-small text color="primary">Забыли пароль?</v-btn>
                    <v-btn type="submit" :disabled="invalid" color="primary">Вход</v-btn>
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

export default {
  components: { ValidationProvider, ValidationObserver },
  data: () => ({
    email: "",
    password: "",
    showPassword: false
  }),
  methods: {
    login() {
      auth.login(this.email, this.password);
    },
    resetPassword() {
      this.$router.push("password/reset");
    }
  }
};
</script>
