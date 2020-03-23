<template>
  <v-container>
    <v-toolbar flat color="white">
      <v-spacer></v-spacer>
      <v-dialog v-model="dialog" max-width="500px">
        <template v-slot:activator="{ on }">
          <v-btn color="primary" dark class="mb-2" v-on="on">Новая запись</v-btn>
        </template>
        <v-card>
          <v-card-title>
            <span class="headline">{{ formTitle }}</span>
          </v-card-title>

          <ValidationObserver ref="observer" v-slot="{ validate, reset, invalid, handleSubmit }">
            <v-form @submit.prevent="handleSubmit(save)">
              <v-card-text>
                <v-container>
                  <ValidationProvider
                    v-slot="{ errors }"
                    name="Наименование отдела"
                    rules="required"
                  >
                    <v-text-field
                      v-model="editedItem.name"
                      label="Наименование отдела"
                      :error-messages="errors"
                      required
                      outlined
                      dense
                    ></v-text-field>
                  </ValidationProvider>
                  <v-input>
                    <treeselect
                      :options="items"
                      :normalizer="treeNormalizer"
                      v-model="editedItem.parent_id"
                      :show-count="true"
                      :default-expand-level="1"
                      placeholder="Выберите иерархию"
                    ></treeselect>
                  </v-input>
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
    <v-card class="mx-auto">
      <v-card-text>
        <v-treeview ref="treeView" hoverable dense open-all :items="items">
          <template v-slot:append="{ item }">
            <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
            <v-icon small @click="deleteItem(item)">mdi-delete</v-icon>
          </template>
        </v-treeview>
        <template v-if="!items.length">
          <div v-if="!loadItems" class="text-center">
            <v-icon color="orange" large>mdi-information</v-icon>
            <p>Нет данных</p>
          </div>
        </template>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script>
import Treeselect from "@riophae/vue-treeselect";
import "@riophae/vue-treeselect/dist/vue-treeselect.css";

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
  components: { Treeselect, ValidationProvider, ValidationObserver },
  data: () => ({
    dialog: false,
    items: [],
    loadItems: true,
    treeNormalizer(node) {
      return {
        id: node.id,
        label: node.name,
        children: node.children
      };
    },
    editedItem: {
      id: null,
      name: "",
      parent_id: null
    },
    defaultItem: {
      id: null,
      name: "",
      parent_id: null
    }
  }),
  computed: {
    formTitle() {
      return !this.editedItem.id ? "Новый отдел" : "Редактирование";
    }
  },
  mounted() {
    this.$root.title = "Отделы";
    Event.$on("changeCompany", () => {
      if (this.$router.currentRoute.name == "departments") this.getItems();
    });
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
      axios.get("/departments").then(result => {
        if (result.data) this.items = result.data;
        this.loadItems = false;
        setTimeout(() => {
          this.$refs.treeView.updateAll(true);
        }, 300);
      });
    },

    editItem(item) {
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },
    async deleteItem(item) {
      const res = await this.$dialog.confirm({
        text: "Вы уверены что хотите удалить?",
        title: "Warning"
      });
      if (res) {
        axios.delete("/departments/" + item.id).then(result => {
          this.getItems();
        });
      }
    },
    save() {
      if (this.editedItem.id) {
        axios
          .put("/departments/" + this.editedItem.id, this.editedItem)
          .then(result => {
            if (result.data) {
              this.getItems();
              this.close();
            }
          });
      } else {
        axios.post("/departments", this.editedItem).then(result => {
          if (result.data) {
            this.getItems();
            this.close();
          }
        });
      }
    },

    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
      }, 100);
    }
  }
};
</script>
