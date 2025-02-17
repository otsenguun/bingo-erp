<template>
  <div class="container-fluid">
    <div class="row no-gutter">
      <!-- The image half -->
      <div class="col-md-6 d-none d-md-flex bg-image"></div>
      <!-- The content half -->
      <div class="col-md-6 bg-light">
        <div class="auth-wrapper d-flex align-items-center py-5">
          <!-- Demo content-->
          <div class="container">
            <div class="row">
              <div class="col-md-12 col-lg-10 col-xl-8 mx-auto">
                <div class="text-center mb-4">
                  <router-link to="/">
                    <img v-if="appInfo" :src="appInfo.blackLogo" :alt="appInfo.companyName"
                      class="lg-logo img-fluid logo-width" />
                  </router-link>
                </div>

                <form @submit.prevent="resendVerification" @keydown="verificationForm.onKeydown($event)">
                  <!-- domain -->
                  <div class="form-group mb-3">
                    <input v-model="verificationForm.email" id="email" name="email" :class="{
                      'is-invalid': verificationForm.errors.has('email'),
                    }" class="form-control rounded-pill border-0 shadow-sm px-4 text-primary" type="text"
                      :placeholder="$t('Email')" />
                    <has-error :form="verificationForm" :style="[
                      verificationForm.errors.has('email') ? 'block' : 'none',
                    ]" field="email" />
                  </div>
                  <!-- Submit Button -->
                  <v-button :loading="verificationForm.busy"
                    class="btn btn-primary btn-block text-uppercase mb-2 rounded-pill shadow-sm">
                    <i class="fas fa-sign-in-alt" />
                    <strong>{{ $t("Send") }}</strong>
                  </v-button>
                </form>

                <div class="mt-5" v-if="showSentMessage">
                  <div v-if="message" class="alert" :class="type == 'success' ? 'alert-success' : 'alert-danger'
                    ">
                    {{ message }}
                  </div>

                  <h3>One more step 👍</h3>
                  <p class="text-22 mb-4 mt-2">
                    {{ $t("We've sent an email to") }}
                    <span class="text-indigo">{{ verificationForm.email }}</span>.
                    {{
                      $t(
                        "Please click the confirmation link in it to finalize your account"
                      )
                    }}
                  </p>
                  <p>
                    {{
                      $t(
                        "Didn't get the email? Please check your spam folder or"
                      )
                    }}
                    <button @click="resendVerification" class="btn p-0 text-indigo">
                      {{ $t("Resend Verification") }}
                    </button>
                  </p>
                </div>

                <div class="row text-center">
                  <router-link :to="{ name: 'register' }" class="ml-auto my-auto">
                    {{ $t("Don't have any account? Click here!") }}
                  </router-link>
                </div>
              </div>
            </div>
          </div>
          <!-- End -->
        </div>
      </div>
      <!-- End -->
    </div>
  </div>
</template>
<script>
import Form from "vform";
import { mapGetters } from "vuex";

export default {
  layout: "basic",
  middleware: "guest",
  metaInfo() {
    return { title: this.$t("find_domain") };
  },
  data: () => ({
    isDemoMode: window.config.isDemoMode,
    message: "",
    type: null,
    verificationForm: new Form({
      email: "",
    }),
    showSentMessage: false,
    appName: window.config.appName,
    host: location.host,
  }),
  // Map Getters
  computed: {
    ...mapGetters("operations", ["appInfo"]),
  },
  methods: {
    async resendVerification() {
      if(this.isDemoMode){
        return toast.fire({
          type: "warning",
          title: this.$t("You are not allowed to do this in demo version."),
        });
      }
      await this.verificationForm
        .post("/api/email/resend")
        .then(({ data }) => {
          this.message = data.message;
          this.type = "success";
          this.showSentMessage = true;
        })
        .catch((e) => {
          this.message = e.response.data.message;
          this.type = "danger";
        })
        .finally(() => { });
    },
  },
};
</script>
