<template>
  <div class="container-fluid">
    <div class="row no-gutter">
      <!-- The image half -->
      <div class="col-md-6 d-none d-md-flex bg-image"></div>
      <!-- The content half -->
      <div class="col-md-6 bg-light">
        <div class="auth-wrapper d-flex align-items-center py-5">
          <div class="container">
            <div class="row">
              <div class="col-lg-10 col-xl-7 mx-auto">
                <div class="text-center">
                  <router-link to="/">
                    <img v-if="appInfo" :src="appInfo.blackLogo" :alt="appInfo.companyName"
                      class="lg-logo img-fluid logo-width" />
                  </router-link>
                  <p class="text-22 mb-4 mt-2">{{ $t("login_txt") }}</p>
                </div>

                <form @submit.prevent="login" @keydown="form.onKeydown($event)">
                  <div class="form-group mb-3">
                    <input id="email" v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }"
                      class="form-control rounded-pill border-0 shadow-sm px-4 text-primary" type="email" name="email"
                      :placeholder="$t('email_placeholder')" />
                    <has-error :form="form" field="email" />
                  </div>
                  <div class="form-group mb-3">
                    <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }"
                      class="form-control rounded-pill border-0 shadow-sm px-4 text-primary" type="password"
                      name="password" :placeholder="$t('password_placeholder')" />
                    <has-error :form="form" field="password" />
                  </div>
                  <div class="row mb-5">
                    <div class="col-md-6">
                      <checkbox v-model="remember" name="remember">
                        {{ $t("remember_me") }}
                      </checkbox>
                    </div>
                    <div class="col-md-6 text-right">
                      <router-link :to="{ name: 'password.request' }" class="ml-auto my-auto">
                        {{ $t("forgot_password") }}
                      </router-link>
                    </div>
                  </div>
                  <!-- Submit Button -->
                  <v-button :loading="form.busy"
                    class="btn btn-primary btn-block text-uppercase mb-2 rounded-pill shadow-sm">
                    <i class="fas fa-sign-in-alt" />
                    <strong>{{ $t("login") }}</strong>
                  </v-button>
                </form>
              </div>
              <!-- Login  Credentials For Demo -->
              <div class="col-12 mt-4" v-if="isDemoMode">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-12">
                        <h3 class="text-center font-bold font-up danger-text">
                          Login Credentials
                        </h3>
                      </div>
                    </div>
                    <div class="table-responsive">
                      <table class="table table-bordered red-border text-center">
                        <thead>
                          <tr>
                            <th>Type</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody v-if="!isSubdomain">
                          <tr>
                            <th>Owner</th>
                            <th>Central Admin</th>
                            <td>superadmin@acculance.top</td>
                            <td>acculance2024</td>
                            <td scope="row">
                              <button v-tooltip="$t('Central Admin')" class="btn" @click="
                      loginCredential(
                        'superadmin@acculance.top',
                        'acculance2024'
                      )
                      ">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                  stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                              </button>
                            </td>
                          </tr>
                        </tbody>
                        <tbody v-else>
                          <tr v-if="checkSubdomain('john')">
                            <th scope="row">Tenant(Subscriber)</th>
                            <th scope="row">Admin</th>
                            <td>john@acculance.top</td>
                            <td>acculance2024</td>
                            <td scope="row">
                              <button v-tooltip="$t('Central Admin')" class="btn" @click="
                      loginCredential(
                        'john@acculance.top',
                        'acculance2024'
                      )
                      ">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                  stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                              </button>
                            </td>
                          </tr>
                          <tr v-if="checkSubdomain('jane')">
                            <th scope="row">Tenant(Subscriber)</th>
                            <th scope="row">Admin</th>
                            <td>jane@acculance.top</td>
                            <td>acculance2024</td>
                            <td scope="row">
                              <button v-tooltip="$t('Central Admin')" class="btn" @click="
                      loginCredential(
                        'jane@acculance.top',
                        'acculance2024'
                      )
                      ">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                  stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <th scope="row">Tenant Employee</th>
                            <th scope="row">Manager</th>
                            <td>manager@acculance.top</td>
                            <td>acculance2024</td>
                            <td scope="row">
                              <button v-tooltip="$t('Login as super manager')" class="btn" @click="
                      loginCredential(
                        'manager@acculance.top',
                        'acculance2024'
                      )
                      ">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                  stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <th scope="row">Tenant Employee</th>
                            <th scope="row">Salesman</th>
                            <td>sales@acculance.top</td>
                            <td>acculance2024</td>
                            <td scope="row">
                              <button v-tooltip="$t('Login as super salesman')" class="btn" @click="
                      loginCredential(
                        'sales@acculance.top',
                        'acculance2024'
                      )
                      ">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                  stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                              </button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
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
import Cookies from "js-cookie";
import { mapGetters } from "vuex";

export default {
  layout: "basic",
  middleware: "guest",
  metaInfo() {
    return { title: this.$t("login") };
  },
  data: () => ({
    form: new Form({
      email: "",
      password: "",
    }),
    remember: false,
    appName: window.config.appName,
    isSubdomain: false,
    isDemoMode: window.config.isDemoMode
  }),

  // Map Getters
  computed: {
    ...mapGetters("operations", ["appInfo"]),
  },

  created() {
    this.isSubdomain = this.checkDomain(window.location.hostname);
    this.getSubdomain();
    console.log(this.getSubdomain());
  },

  methods: {
    async login() {
      // Submit the form.
      const loginRequest = await this.form.post("/api/login");
      if (loginRequest.status !== 200) {
        toast.fire({
          type: "error",
          title: this.$t("Something went wrong, please try again!"),
        });
        Object.keys(Cookies.get()).forEach(function (cookieName) {
          var neededAttributes = {
            // Here you pass the same attributes that were used when the cookie was created
            // and are required when removing the cookie
          };
          Cookies.remove(cookieName, neededAttributes);
        });
        return;
      }
      const { data } = loginRequest;
      // Save the token.
      this.$store.dispatch("auth/saveToken", {
        token: data.token,
        remember: this.remember,
      });
      // Fetch the user.
      await this.$store.dispatch("auth/fetchUser");
      // Redirect home.
      this.redirect();
    },
    redirect() {
      const intendedUrl = Cookies.get("intended_url");
      if (intendedUrl) {
        Cookies.remove("intended_url");
        this.$router.push({ path: intendedUrl });
        window.location.reload();
      } else {
        this.$router.push({ name: "home" });
        // window reload
        window.location.reload();
      }
    },
    loginCredential(email, pass) {
      this.form.email = email;
      this.form.password = pass;
      this.login();
    },

    checkDomain(url) {
      url = url || "http://www.test-domain.com"; // just for the example
      var regex = new RegExp(/^([a-z]+\:\/{2})?([\w-]+\.[\w-]+\.\w+)$/);
      return !!url.match(regex); // make sure it returns boolean
    },

    getSubdomain() {
      return window.location.hostname;
    },

    checkSubdomain(subdomainName) {
      const subdomain = this.getSubdomain();
      return subdomain.includes(subdomainName);
    }
  },
};
</script>
