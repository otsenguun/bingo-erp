<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-12 col-xl-3">
        <SettingsSidebar />
      </div>
      <div class="col-12 col-xl-9">
        <form
          role="form"
          @submit.prevent="updateSettings"
          @keydown="form.onKeydown($event)"
        >
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                {{ $t("Payment Methods") }}
              </h3>
              <router-link
                :to="{ name: 'setup.index' }"
                class="btn btn-dark float-right"
              >
                <i class="fas fa-long-arrow-alt-left" />
                {{ $t("Back") }}
              </router-link>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="card inner-card">
                <div class="card-header">
                  {{ $t("Manual") }}
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="form-group col-md-6">
                      <checkbox
                        v-model="form.MANUAL_PAYMENT_IS_ACTIVE"
                        name="MANUAL_PAYMENT_IS_ACTIVE"
                      >
                        {{ $t("Enable") }}
                      </checkbox>
                    </div>

                    <div class="form-group col-12">
                      <label for="MANUAL_PAYMENT_NOTE">
                        {{ $t("Manual Payment Note") }}
                        <span class="required">*</span>
                      </label>
                      <textarea
                        id="MANUAL_PAYMENT_NOTE"
                        v-model="form.MANUAL_PAYMENT_NOTE"
                        class="form-control"
                        :class="{
                          'is-invalid': form.errors.has('MANUAL_PAYMENT_NOTE'),
                        }"
                        name="MANUAL_PAYMENT_NOTE"
                        :placeholder="$t('Manual Payment Note')"
                      />
                      <has-error :form="form" field="MANUAL_PAYMENT_NOTE" />
                    </div>
                  </div>
                </div>
              </div>

              <div class="card inner-card">
                <div class="card-header">
                  {{ $t("Stripe") }}
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="form-group col-12">
                      <checkbox
                        v-model="form.STRIPE_IS_ACTIVE"
                        name="STRIPE_IS_ACTIVE"
                      >
                        {{ $t("Enable") }}
                      </checkbox>

                      <has-error :form="form" field="STRIPE_IS_ACTIVE" />
                    </div>

                    <div class="form-group col-12">
                      <label for="STRIPE_SECRET">
                        {{ $t("Stripe Secret") }}
                        <span class="required">*</span>
                      </label>
                      <input
                        id="STRIPE_SECRET"
                        v-model="form.STRIPE_SECRET"
                        type="password"
                        class="form-control"
                        :class="{
                          'is-invalid': form.errors.has('STRIPE_SECRET'),
                        }"
                        name="STRIPE_SECRET"
                        :placeholder="$t('Stripe Secret')"
                      />
                      <has-error :form="form" field="STRIPE_SECRET" />
                    </div>
                  </div>
                </div>
              </div>

              <div class="card inner-card">
                <div class="card-header">
                  {{ $t("Paypal") }}
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="form-group col-12">
                      <checkbox
                        v-model="form.PAYPAL_IS_ACTIVE"
                        name="PAYPAL_IS_ACTIVE"
                      >
                        {{ $t("Enable") }}
                      </checkbox>

                      <has-error :form="form" field="PAYPAL_IS_ACTIVE" />
                    </div>

                    <div class="form-group col-12">
                      <label for="PAYPAL_MODE">
                        {{ $t("Paypal Mode") }}
                        <span class="required">*</span>
                      </label>
                      <select
                        id="PAYPAL_MODE"
                        v-model="form.PAYPAL_MODE"
                        type="password"
                        class="form-control"
                        :class="{
                          'is-invalid': form.errors.has('PAYPAL_MODE'),
                        }"
                        name="PAYPAL_MODE"
                      >
                        <option value="sandbox">
                          {{ $t("Sandbox") }}
                        </option>
                        <option value="live">
                          {{ $t("Live") }}
                        </option>
                      </select>
                      <has-error :form="form" field="PAYPAL_MODE" />
                    </div>

                    <div class="form-group col-12">
                      <label for="PAYPAL_CLIENT_ID">
                        {{ $t("Paypal Client ID") }}
                        <span class="required">*</span>
                      </label>
                      <input
                        id="PAYPAL_CLIENT_ID"
                        v-model="form.PAYPAL_CLIENT_ID"
                        type="text"
                        class="form-control"
                        :class="{
                          'is-invalid': form.errors.has('PAYPAL_CLIENT_ID'),
                        }"
                        name="PAYPAL_CLIENT_ID"
                        :placeholder="$t('Paypal Client ID')"
                      />
                      <has-error :form="form" field="PAYPAL_CLIENT_ID" />
                    </div>

                    <div class="form-group col-12">
                      <label for="PAYPAL_CLIENT_SECRET">
                        {{ $t("Paypal Client Secret") }}
                        <span class="required">*</span>
                      </label>
                      <input
                        id="PAYPAL_CLIENT_SECRET"
                        v-model="form.PAYPAL_CLIENT_SECRET"
                        type="password"
                        class="form-control"
                        :class="{
                          'is-invalid': form.errors.has('PAYPAL_CLIENT_SECRET'),
                        }"
                        name="PAYPAL_CLIENT_SECRET"
                        :placeholder="$t('Paypal Client Secret')"
                      />
                      <has-error :form="form" field="PAYPAL_CLIENT_SECRET" />
                    </div>
                  </div>
                </div>
              </div>

              <div class="card inner-card">
                <div class="card-header">
                  {{ $t("Paystack") }}
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="form-group col-12">
                      <checkbox
                        v-model="form.PAYSTACK_IS_ACTIVE"
                        name="PAYSTACK_IS_ACTIVE"
                      >
                        {{ $t("Enable") }}
                      </checkbox>
                      <has-error :form="form" field="PAYSTACK_IS_ACTIVE" />
                    </div>

                    <div class="form-group col-12">
                      <label for="MERCHANT_EMAIL">
                        {{ $t("Merchant Email") }}
                        <span class="required">*</span>
                      </label>
                      <input
                        id="MERCHANT_EMAIL"
                        v-model="form.MERCHANT_EMAIL"
                        type="text"
                        class="form-control"
                        :class="{
                          'is-invalid': form.errors.has('MERCHANT_EMAIL'),
                        }"
                        name="MERCHANT_EMAIL"
                        :placeholder="$t('Merchant Email')"
                      />
                      <has-error :form="form" field="MERCHANT_EMAIL" />
                    </div>

                    <div class="form-group col-12">
                      <label for="PAYSTACK_PUBLIC_KEY">
                        {{ $t("Paystack Public Key") }}
                        <span class="required">*</span>
                      </label>
                      <input
                        id="PAYSTACK_PUBLIC_KEY"
                        v-model="form.PAYSTACK_PUBLIC_KEY"
                        type="text"
                        class="form-control"
                        :class="{
                          'is-invalid': form.errors.has('PAYSTACK_PUBLIC_KEY'),
                        }"
                        name="PAYSTACK_PUBLIC_KEY"
                        :placeholder="$t('Paystack Public Key')"
                      />
                      <has-error :form="form" field="PAYSTACK_PUBLIC_KEY" />
                    </div>

                    <div class="form-group col-12">
                      <label for="PAYSTACK_SECRET_KEY">
                        {{ $t("Paystack Secret Key") }}
                        <span class="required">*</span>
                      </label>
                      <input
                        id="PAYSTACK_SECRET_KEY"
                        v-model="form.PAYSTACK_SECRET_KEY"
                        type="password"
                        class="form-control"
                        :class="{
                          'is-invalid': form.errors.has('PAYSTACK_SECRET_KEY'),
                        }"
                        name="PAYSTACK_SECRET_KEY"
                        :placeholder="$t('Paystack Secret Key')"
                      />
                      <has-error :form="form" field="PAYSTACK_SECRET_KEY" />
                    </div>
                  </div>
                </div>
              </div>

              <div class="card inner-card">
                <div class="card-header">
                  {{ $t("Razorpay") }}
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="form-group col-12">
                      <checkbox
                        v-model="form.RAZORPAY_IS_ACTIVE"
                        name="RAZORPAY_IS_ACTIVE"
                      >
                        {{ $t("Enable") }}
                      </checkbox>
                      <has-error :form="form" field="RAZORPAY_IS_ACTIVE" />
                    </div>

                    <div class="form-group col-12">
                      <label for="RAZORPAY_KEY_ID">
                        {{ $t("Razopay Key ID") }}
                        <span class="required">*</span>
                      </label>
                      <input
                        id="RAZORPAY_KEY_ID"
                        v-model="form.RAZORPAY_KEY_ID"
                        type="text"
                        class="form-control"
                        :class="{
                          'is-invalid': form.errors.has('RAZORPAY_KEY_ID'),
                        }"
                        name="RAZORPAY_KEY_ID"
                        :placeholder="$t('Razopay Key ID')"
                      />
                      <has-error :form="form" field="RAZORPAY_KEY_ID" />
                    </div>

                    <div class="form-group col-12">
                      <label for="RAZORPAY_KEY_SECRET">
                        {{ $t("Razopay Secret Key") }}
                        <span class="required">*</span>
                      </label>
                      <input
                        id="RAZORPAY_KEY_SECRET"
                        v-model="form.RAZORPAY_KEY_SECRET"
                        type="password"
                        class="form-control"
                        :class="{
                          'is-invalid': form.errors.has('RAZORPAY_KEY_SECRET'),
                        }"
                        name="RAZORPAY_KEY_SECRET"
                        :placeholder="$t('Razopay Secret Key')"
                      />
                      <has-error :form="form" field="RAZORPAY_KEY_SECRET" />
                    </div>
                  </div>
                </div>
              </div>

              <div class="card inner-card">
                <div class="card-header">
                  {{ $t("Live Currency Exchange") }}
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="form-group col-12">
                      <checkbox
                        v-model="form.EXCHANGE_LIVE_CURRENCY"
                        name="EXCHANGE_LIVE_CURRENCY"
                      >
                        {{ $t("Enable") }}
                      </checkbox>

                      <has-error :form="form" field="EXCHANGE_LIVE_CURRENCY" />
                    </div>

                    <div class="form-group col-12">
                      <label for="EXCHANGE_RATES_API_KEY">
                        {{ $t("Exchange API Key") }}
                        <span class="required">*</span>
                      </label>
                      <input
                        id="EXCHANGE_RATES_API_KEY"
                        v-model="form.EXCHANGE_RATES_API_KEY"
                        type="password"
                        class="form-control"
                        :class="{
                          'is-invalid': form.errors.has(
                            'EXCHANGE_RATES_API_KEY'
                          ),
                        }"
                        name="EXCHANGE_RATES_API_KEY"
                        :placeholder="$t('Exchange API Key')"
                      />
                      <small id="emailHelp" class="form-text text-muted"
                        >
                        {{ $t("Get your API key on") }}
                        <a
                          href="https://exchangerate.host/documentation"
                          target="__blank"
                          >Exchangerate</a
                        ></small
                      >
                      <has-error :form="form" field="EXCHANGE_RATES_API_KEY" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <v-button :loading="form.busy" class="btn btn-primary">
                <i class="fas fa-edit" />
                {{ $t("Save changes") }}
              </v-button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import Form from "vform";
import axios from "axios";
import { mapGetters } from "vuex";
import SettingsSidebar from "~/components/central/SettingsSidebar";

export default {
  layout: "central",
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("Payment Methods") };
  },
  components: {
    SettingsSidebar,
  },
  data: () => ({
    breadcrumbsCurrent: "Payment Settings",
    breadcrumbs: [
      {
        name: "Setup",
        url: "setup.index",
      },
      {
        name: "Payment Settings",
        url: "",
      },
    ],
    form: new Form({
      MANUAL_PAYMENT_IS_ACTIVE: false,
      MANUAL_PAYMENT_NOTE: false,
      STRIPE_IS_ACTIVE: false,
      STRIPE_KEY: "",
      STRIPE_SECRET: "",
      STRIPE_WEBHOOK_SECRET: "",
      PAYPAL_IS_ACTIVE: false,
      PAYPAL_MODE: "sandbox",
      PAYPAL_CLIENT_ID: "",
      PAYPAL_CLIENT_SECRET: "",

      PAYSTACK_IS_ACTIVE: false,
      PAYSTACK_PUBLIC_KEY: "",
      PAYSTACK_SECRET_KEY: "",
      MERCHANT_EMAIL: "",

      RAZORPAY_IS_ACTIVE: false,
      RAZORPAY_KEY_ID: "",
      RAZORPAY_KEY_SECRET: "",

      EXCHANGE_RATES_API_KEY: "",
      EXCHANGE_LIVE_CURRENCY: false,
    }),
    logo: "",
    blackLogo: "",
    smallLogo: "",
    favicon: "",
    isDemoMode: window.config.isDemoMode,
  }),
  computed: mapGetters({
    appInfo: "operations/appInfo",
    items: "operations/items",
  }),

  created() {
    this.getSettings();
  },

  methods: {
    // get settings
    async getSettings() {
      const {
        data: { data },
      } = await axios.get(window.location.origin + "/api/payment-methods");
      this.form.MANUAL_PAYMENT_IS_ACTIVE =
        data.MANUAL_PAYMENT_IS_ACTIVE.value == 1;
      this.form.MANUAL_PAYMENT_NOTE = data.MANUAL_PAYMENT_NOTE.value;
      this.form.STRIPE_IS_ACTIVE = data.STRIPE_IS_ACTIVE.value == 1;
      this.form.STRIPE_KEY = data.STRIPE_KEY.value;
      this.form.STRIPE_SECRET = data.STRIPE_SECRET.value;
      this.form.STRIPE_WEBHOOK_SECRET = data.STRIPE_WEBHOOK_SECRET.value;

      this.form.PAYPAL_IS_ACTIVE = data.PAYPAL_IS_ACTIVE.value == 1;
      this.form.PAYPAL_MODE = data.PAYPAL_MODE.value;
      this.form.PAYPAL_CLIENT_ID = data.PAYPAL_CLIENT_ID.value;
      this.form.PAYPAL_CLIENT_SECRET = data.PAYPAL_CLIENT_SECRET.value;

      this.form.PAYSTACK_IS_ACTIVE = data.PAYSTACK_IS_ACTIVE.value == 1;
      this.form.PAYSTACK_PUBLIC_KEY = data.PAYSTACK_PUBLIC_KEY.value;
      this.form.PAYSTACK_SECRET_KEY = data.PAYSTACK_SECRET_KEY.value;
      this.form.MERCHANT_EMAIL = data.MERCHANT_EMAIL.value;

      this.form.RAZORPAY_IS_ACTIVE = data.RAZORPAY_IS_ACTIVE.value == 1;
      this.form.RAZORPAY_KEY_ID = data.RAZORPAY_KEY_ID.value;
      this.form.RAZORPAY_KEY_SECRET = data.RAZORPAY_KEY_SECRET.value;

      this.form.EXCHANGE_LIVE_CURRENCY = data.EXCHANGE_LIVE_CURRENCY.value == 1;
      this.form.EXCHANGE_RATES_API_KEY = data.EXCHANGE_RATES_API_KEY.value;
    },

    // update settings
    async updateSettings() {
      if (this.isDemoMode) {
        return toast.fire({
          type: "warning",
          title: this.$t("You are not allowed to do this in demo version."),
        });
      }
      await this.form
        .post(window.location.origin + "/api/payment-methods")
        .then(() => {
          toast.fire({
            type: "success",
            title: this.$t("Created successfully!"),
          });
          this.getSettings();
        })
        .catch(() => {
          toast.fire({
            type: "error",
            title: this.$t("Opps...something went wrong"),
          });
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.inner-card {
  box-shadow: 0 0 1px rgba(0, 0, 0, 0.125), 0 1px 3px rgba(0, 0, 0, 0.2);
}

.inner-card .card-header {
  background-color: rgba(0, 0, 0, 0.03);
}
</style>
