<template>
  <v-container>
    <v-data-table
      :headers="headers"
      :items="items"
      :search="search"
      sort-by="name"
      class="elevation-1"
    >
      <template v-slot:top>
        <v-toolbar flat color="white">
          <v-text-field
            v-model="search"
            append-icon="mdi-magnify"
            label="Поиск"
            single-line
            hide-details
          ></v-text-field>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ on }">
              <v-btn color="primary" dark class="mb-2" v-on="on">Новая запись</v-btn>
            </template>
            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>

              <ValidationObserver
                ref="observer"
                v-slot="{ validate, reset, invalid, handleSubmit }"
              >
                <v-form @submit.prevent="handleSubmit(save)">
                  <v-card-text>
                    <v-container>
                      <v-row>
                        <v-col cols="12" class="py-0">
                          <ValidationProvider v-slot="{ errors }" name="Имя" rules="required">
                            <v-text-field
                              v-model="editedItem.name"
                              label="Имя"
                              :error-messages="errors"
                              required
                              outlined
                              dense
                            ></v-text-field>
                          </ValidationProvider>
                        </v-col>
                        <v-col cols="12" class="py-0">
                          <ValidationProvider
                            v-slot="{ errors }"
                            name="E-mail"
                            rules="required|email"
                          >
                            <v-text-field
                              v-model="editedItem.email"
                              label="E-mail"
                              :error-messages="errors"
                              required
                              outlined
                              dense
                            ></v-text-field>
                          </ValidationProvider>
                        </v-col>
                        <v-col cols="12" sm="6" class="py-0">
                          <ValidationProvider
                            v-slot="{ errors }"
                            vid="confirm"
                            name="Пароль"
                            :rules="`${(!editedItem.id) ? 'required' : ''}`"
                          >
                            <v-text-field
                              v-model="editedItem.password"
                              label="Пароль"
                              :error-messages="errors"
                              :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                              :type="showPassword ? 'text' : 'password'"
                              @click:append="showPassword = !showPassword"
                              outlined
                              dense
                            ></v-text-field>
                          </ValidationProvider>
                        </v-col>
                        <v-col cols="12" sm="6" class="py-0">
                          <ValidationProvider
                            v-slot="{ errors }"
                            :rules="`${(editedItem.password) ? 'required|password:@confirm' : ''}`"
                            name="Подтверждение пароля"
                          >
                            <v-text-field
                              v-model="editedItem.password_confirmation"
                              label="Подтв. пароль"
                              :error-messages="errors"
                              :type="showPassword ? 'text' : 'password'"
                              outlined
                              dense
                            ></v-text-field>
                          </ValidationProvider>
                        </v-col>
                        <v-col cols="12" class="py-0">
                          <ValidationProvider v-slot="{ errors }" name="Отдел" rules="required">
                            <v-input :error-messages="errors" outlined dense>
                              <treeselect
                                :options="departments"
                                :normalizer="treeNormalizer"
                                v-model="editedItem.department_id"
                                :show-count="true"
                                :default-expand-level="1"
                                placeholder="Выберите отдел"
                                :required="true"
                              ></treeselect>
                            </v-input>
                          </ValidationProvider>
                        </v-col>
                        <v-col cols="12" sm="6" class="py-0">
                          <v-text-field
                            v-model="editedItem.position"
                            label="Должность"
                            outlined
                            dense
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" class="py-0">
                          <v-text-field v-model="editedItem.phone" label="Телефон" outlined dense></v-text-field>
                        </v-col>
                        <v-col cols="12" class="pt-0">
                          <v-textarea
                            v-model="editedItem.comment"
                            label="Коментарий"
                            dense
                            rows="3"
                            outlined
                            hide-details
                          ></v-textarea>
                        </v-col>
                        <v-col cols="12" class="py-0">
                          <v-select
                            v-model="editedItem.roles"
                            :items="roles"
                            item-text="title"
                            item-value="name"
                            chips
                            label="Роль"
                            dense
                            outlined
                          ></v-select>
                        </v-col>
                        <v-col cols="12">
                          <v-switch
                            v-model="editedItem.activated"
                            label="Актив."
                            hide-details
                            class="mt-0"
                          ></v-switch>
                        </v-col>
                      </v-row>
                    </v-container>
                  </v-card-text>

                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text color="darken-1" @click="close">Отмена</v-btn>
                    <v-btn text type="submit" color="primary darken-1">Сохранить</v-btn>
                  </v-card-actions>
                </v-form>
              </ValidationObserver>
            </v-card>
          </v-dialog>
        </v-toolbar>
      </template>
      <template v-slot:item.roles="{ item }">
        <v-chip v-for="role in item.roles" :key="role.id" small color="blue" dark>{{ role.title }}</v-chip>
      </template>
      <template v-slot:item.created_at="{ item }">
        {{
        item.created_at | moment("DD.MM.YYYY HH:mm")
        }}
      </template>
      <template v-slot:item.activated="{ item }">
        <v-switch
          v-model="item.activated"
          @change="setActivated(item)"
          hide-details
          class="mt-0"
          :disabled="$root.currentUser.id == item.id"
        ></v-switch>
      </template>
      <template v-slot:item.email_verified_at="{ item }">
        <v-icon v-if="item.email_verified_at" color="success">mdi-account-check-outline</v-icon>
        <v-icon v-else color="red">mdi-minus</v-icon>
      </template>
      <template v-slot:item.action="{ item }">
        <span v-if="$root.currentUser.id != item.id">
          <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
          <v-icon small @click="deleteItem(item)">mdi-delete</v-icon>
        </span>
      </template>
      <template v-slot:no-data>
        <div v-if="!loadItems">
          <v-icon color="orange" large>mdi-information</v-icon>
          <p>Нет данных</p>
        </div>
        <div v-else>&nbsp;</div>
      </template>
    </v-data-table>
  </v-container>
</template>

<script>
import Treeselect from "@riophae/vue-treeselect";
import "@riophae/vue-treeselect/dist/vue-treeselect.css";

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
  components: { Treeselect, ValidationProvider, ValidationObserver },
  data: () => ({
    dialog: false,
    search: "",
    headers: [
      { text: "Имя пользователя", value: "name" },
      { text: "E-mail (Логин)", value: "email" },
      { text: "Отдел", value: "department.name" },
      { text: "Должность", value: "position" },
      { text: "Роль", value: "roles" },
      {
        text: "Дата регистрации",
        value: "created_at",
        align: "center",
        width: 160
      },
      { text: "Актив.", value: "activated", align: "center", width: 100 },
      {
        text: "Подтв.",
        value: "email_verified_at",
        align: "center",
        width: 100,
        sort: (a, b) => {
          return Boolean(a) ? 1 : Boolean(b) ? -1 : 0;
        }
      },
      {
        text: "",
        value: "action",
        align: "center",
        width: 100,
        sortable: false
      }
    ],
    items: [],
    loadItems: true,
    editedItem: {
      id: null,
      email: "",
      password: "",
      password_confirmation: "",
      name: "",
      department_id: null,
      phone: "",
      position: "",
      comment: "",
      roles: null,
      permissions: null,
      activated: true
    },
    defaultItem: {
      id: null,
      email: "",
      password: "",
      password_confirmation: "",
      name: "",
      department_id: null,
      phone: "",
      position: "",
      comment: "",
      roles: null,
      permissions: null,
      activated: true
    },
    departments: [],
    treeNormalizer(node) {
      return {
        id: node.id,
        label: node.name,
        children: node.children
      };
    },
    roles: [],
    permissions: [],
    showPassword: false
  }),
  computed: {
    formTitle() {
      return !this.editedItem.id ? "Новый пользователь" : "Редактирование";
    }
  },

  mounted() {
    this.$root.title = "Пользователи";
    Event.$on("changeCompany", () => {
      if (this.$router.currentRoute.name == "users") {
        this.getItems();
        this.getDepartments();
      }
    });
  },
  created() {
    this.getItems();
    this.getDepartments();
    this.getRoles();
    //this.getPermissions();
  },
  watch: {
    dialog: function(newValue, oldValue) {
      if (!newValue) this.$refs.observer.reset();
    }
  },

  methods: {
    getDepartments() {
      axios.get("/departments").then(result => {
        if (result.data) this.departments = result.data;
      });
    },
    getRoles() {
      axios.get("/api/roles/get").then(result => {
        if (result.data) this.roles = result.data;
      });
    },
    getPermissions() {
      axios.get("/api/permissions/get").then(result => {
        if (result.data) this.permissions = result.data;
      });
    },
    getItems() {
      this.loadItems = true;
      axios.get("/users").then(result => {
        if (result.data) this.items = result.data;
        this.loadItems = false;
      });
    },

    editItem(payload) {
      let item = Object.assign({}, payload);
      delete item.company;
      delete item.department;
      let roles = item.roles;
      if (_.isArray(roles) && roles.length > 0) item.roles = roles[0].name;
      /* item.roles = _.map(roles, function(v) {
        return { title: v.title, name: v.name };
      }); */
      this.editedItem = item;
      this.dialog = true;
    },
    async deleteItem(payload) {
      const res = await this.$dialog.warning({
        text: "Вы уверены что хотите удалить?",
        title: "Внимание!",
        actions: {
          false: "Нет",
          true: { text: "Да", color: "primary" }
        },
        icon: false
      });
      if (res) {
        axios.delete("/users/" + payload.id).then(result => {
          this.getItems();
        });
      }
    },

    save() {
      if (this.editedItem.id) {
        axios
          .put("/users/" + this.editedItem.id, this.editedItem)
          .then(result => {
            if (result.data) {
              this.getItems();
              this.close();
            }
          })
          .catch(result => {});
      } else {
        axios
          .post("/users", this.editedItem)
          .then(result => {
            if (result.data) {
              this.getItems();
              this.close();
            }
          })
          .catch(result => {});
      }
    },
    setActivated(payload) {
      axios
        .put("/users/" + payload.id, { activated: payload.activated })
        .then(result => {
          this.$dialog.message.success("Статус изменён!", {
            position: "top"
          });
        });
    },
    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
      }, 300);
    }
  }
};
</script>
