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
                        <v-col cols="12">
                          <ValidationProvider
                            v-slot="{ errors }"
                            name="Наименование"
                            rules="required"
                          >
                            <v-text-field
                              v-model="editedItem.name"
                              label="Наименование"
                              :error-messages="errors"
                              required
                              outlined
                              dense
                            ></v-text-field>
                          </ValidationProvider>
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
      <template v-slot:item.created_at="{ item }">
        {{
        item.created_at | moment("DD.MM.YYYY HH:mm")
        }}
      </template>
      <template v-slot:item.activated="{ item }">
        <v-switch v-model="item.activated" @change="setActivated(item)" hide-details class="mt-0"></v-switch>
      </template>
      <template v-slot:item.action="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
        <v-icon small @click="deleteItem(item)">mdi-delete</v-icon>
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
import { required } from "vee-validate/dist/rules";
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

export default {
  components: { ValidationProvider, ValidationObserver },
  data: () => ({
    dialog: false,
    search: "",
    headers: [
      { text: "Наименование", value: "name" },
      {
        text: "Дата регистрации",
        value: "created_at",
        align: "center",
        width: 160
      },
      { text: "Актив.", value: "activated", align: "center", width: 100 },
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
      name: "",
      activated: true
    },
    defaultItem: {
      id: null,
      name: "",
      activated: true
    }
  }),
  computed: {
    formTitle() {
      return !this.editedItem.id ? "Новая Организация" : "Редактирование";
    }
  },

  mounted() {
    this.$root.title = "Организации";
  },
  created() {
    this.getItems();
  },
  watch: {
    dialog: function(newValue, oldValue) {
      if (!newValue) this.$refs.observer.reset();
    }
  },

  methods: {
    getItems() {
      this.loadItems = true;
      axios.get("/companies").then(result => {
        if (result.data) this.items = result.data;
        this.loadItems = false;
      });
    },

    editItem(payload) {
      this.editedItem = Object.assign({}, payload);
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
        axios.delete("/companies/" + payload.id).then(result => {
          this.getItems();
        });
      }
    },

    save() {
      if (this.editedItem.id) {
        axios
          .put("/companies/" + this.editedItem.id, this.editedItem)
          .then(result => {
            if (result.data) {
              this.getItems();
              this.close();
            }
          })
          .catch(result => {
            this.$dialog.message.error(result.statusText, {
              position: "top"
            });
          });
      } else {
        axios
          .post("/companies", this.editedItem)
          .then(result => {
            if (result.data) {
              this.getItems();
              this.close();
            }
          })
          .catch(result => {
            this.$dialog.message.error(result.statusText, {
              position: "top"
            });
          });
      }
    },
    setActivated(payload) {
      axios
        .put("/companies/" + payload.id, { activated: payload.activated })
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
