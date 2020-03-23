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
                <v-toolbar-title>Регистрация</v-toolbar-title>
              </v-toolbar>

              <ValidationObserver ref="observer" v-slot="{ validate, invalid, handleSubmit }">
                <v-form @submit.prevent="handleSubmit(register)">
                  <v-card-text>
                    <v-container>
                      <v-row dense>
                        <v-col cols="12">
                          <ValidationProvider
                            v-slot="{ errors }"
                            name="Наименование организации"
                            rules="required"
                          >
                            <v-text-field
                              v-model="company_name"
                              label="Наименование организации"
                              prepend-icon="mdi-domain"
                              :error-messages="errors"
                              required
                              dense
                            ></v-text-field>
                          </ValidationProvider>
                        </v-col>
                        <v-col cols="12">
                          <ValidationProvider
                            v-slot="{ errors }"
                            name="E-mail"
                            rules="required|email"
                          >
                            <v-text-field
                              v-model="email"
                              label="E-mail"
                              prepend-icon="mdi-email-outline"
                              :error-messages="errors"
                              dense
                              required
                            ></v-text-field>
                          </ValidationProvider>
                        </v-col>
                        <v-col cols="12" sm="6">
                          <ValidationProvider
                            v-slot="{ errors }"
                            vid="confirm"
                            name="Пароль"
                            :rules="`required`"
                          >
                            <v-text-field
                              v-model="password"
                              label="Пароль"
                              prepend-icon="mdi-lock-outline"
                              :error-messages="errors"
                              :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                              :type="showPassword ? 'text' : 'password'"
                              @click:append="showPassword = !showPassword"
                              dense
                            ></v-text-field>
                          </ValidationProvider>
                        </v-col>
                        <v-col cols="12" sm="6">
                          <ValidationProvider
                            v-slot="{ errors }"
                            :rules="`${ password ? 'required|password:@confirm' : '' }`"
                            name="Подтверждение пароля"
                          >
                            <v-text-field
                              v-model="password_confirmation"
                              label="Подтвердите пароль"
                              :error-messages="errors"
                              :type="showPassword ? 'text' : 'password'"
                              dense
                            ></v-text-field>
                          </ValidationProvider>
                        </v-col>
                        <v-col cols="12">
                          <v-text-field
                            v-model="phone"
                            label="Телефон"
                            prepend-icon="mdi-phone-outline"
                            dense
                          ></v-text-field>
                        </v-col>
                      </v-row>
                    </v-container>
                  </v-card-text>

                  <v-card-actions>
                    <v-btn to="/login" color="primary" outlined>Аутентификация</v-btn>
                    <v-spacer />
                    <v-btn type="submit" :disabled="invalid" color="primary">Зарегистрироваться</v-btn>
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
  data: () => ({
    company_name: "",
    email: "",
    password: "",
    password_confirmation: "",
    phone: "",
    showPassword: false
  }),
  methods: {
    register() {
      let data = {
        company_name: this.company_name,
        email: this.email,
        password: this.password,
        password_confirmation: this.password_confirmation,
        phone: this.phone
      };

      axios
        .post("/register", data)
        .then(response => {
          this.$router.push("/login");
        })
        .catch(error => {});
    }
  }
};
</script>
