<template>
  <v-container>
    <ValidationObserver ref="observer" v-slot="{ validate, invalid, handleSubmit }">
      <v-form @submit.prevent="handleSubmit(save)">
        <v-card class="mx-auto" max-width="400" outlined>
          <div class="overline ma-4">Личные данные</div>
          <v-card-text>
            <ValidationProvider v-slot="{ errors }" name="Имя" rules="required">
              <v-text-field
                v-model="user.name"
                label="Имя"
                :error-messages="errors"
                required
                outlined
                dense
              ></v-text-field>
            </ValidationProvider>
            <ValidationProvider v-slot="{ errors }" name="E-mail" rules="required|email">
              <v-text-field
                v-model="user.email"
                label="E-mail"
                :error-messages="errors"
                required
                outlined
                dense
              ></v-text-field>
            </ValidationProvider>
            <v-text-field v-model="user.phone" label="Телефон" outlined dense></v-text-field>
            <v-text-field v-model="user.position" label="Должность" outlined dense></v-text-field>
            <v-text-field
              v-model="user.department_name"
              label="Отдел"
              hide-details
              outlined
              disabled
              dense
            ></v-text-field>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn text color="darken-1" @click="reset">Сброс</v-btn>
            <v-btn text type="submit" color="primary darken-1">Сохранить</v-btn>
          </v-card-actions>
        </v-card>
      </v-form>
    </ValidationObserver>
    <br />
    <ValidationObserver ref="observer_password" v-slot="{ validate, reset, invalid, handleSubmit }">
      <v-form @submit.prevent="handleSubmit(changePassword)">
        <v-card class="mx-auto" max-width="400" outlined>
          <div class="overline ma-4">Смена пароля</div>
          <v-card-text>
            <ValidationProvider v-slot="{ errors }" vid="confirm" name="Пароль" rules="required">
              <v-text-field
                v-model="user.password"
                label="Пароль"
                :error-messages="errors"
                :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                :type="showPassword ? 'text' : 'password'"
                @click:append="showPassword = !showPassword"
                required
                outlined
                dense
              ></v-text-field>
            </ValidationProvider>
            <ValidationProvider
              v-slot="{ errors }"
              :rules="`${(user.password) ? 'required|password:@confirm' : ''}`"
              name="Подтверждение пароля"
            >
              <v-text-field
                v-model="user.password_confirmation"
                label="Подтвердите пароль"
                :error-messages="errors"
                :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                :type="showPassword ? 'text' : 'password'"
                @click:append="showPassword = !showPassword"
                required
                outlined
                dense
              ></v-text-field>
            </ValidationProvider>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn text type="submit" color="primary darken-1">Изменить пароль</v-btn>
          </v-card-actions>
        </v-card>
      </v-form>
    </ValidationObserver>
  </v-container>
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
    user: Object.assign({}, auth.user),
    showPassword: false
  }),
  mounted() {
    this.$root.title = "Профайл";
  },
  methods: {
    reset() {
      this.user = Object.assign({}, auth.user);
    },
    save() {
      let data = {
        name: this.user.name,
        email: this.user.email,
        phone: this.user.phone,
        position: this.user.position
      };
      axios
        .put("/users/" + this.user.id, data)
        .then(result => {
          if (result.data) {
            auth.getUser();
          }
        })
        .catch(result => {});
    },
    changePassword() {
      let data = {
        password: this.user.password,
        password_confirmation: this.user.password_confirmation
      };
      axios
        .put("/users/" + this.user.id, data)
        .then(result => {
          this.user.password = this.user.password_confirmation = "";
          this.$refs.observer_password.reset();
        })
        .catch(result => {});
    }
  }
};
</script>
