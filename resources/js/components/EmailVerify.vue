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
                <v-toolbar-title>Подтверждение E-mail адреса</v-toolbar-title>
              </v-toolbar>
              <v-card-text>
                <p
                  v-if="!sent"
                >Прежде чем продолжить, проверьте свою электронную почту на наличие ссылки для подтверждения аккаунта.</p>
                <p
                  v-else
                  class="info--text"
                >На ваш адрес электронной почты была отправлена новая ссылка для подтверждения.</p>
                <p>Если вы не получили E-mail, нажмите кнопку "Отправить повторно".</p>
              </v-card-text>
              <v-card-actions>
                <v-btn to="/login" color="primary" outlined>Аутентификация</v-btn>
                <v-spacer />
                <v-btn @click="resend()" color="primary">Отправить повторно</v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-content>
  </div>
</template>

<script>
export default {
  data: () => ({
    sent: false
  }),
  methods: {
    resend() {
      axios
        .post("/email/resend")
        .then(response => {
          this.sent = true;
        })
        .catch(error => {});
    }
  }
};
</script>
