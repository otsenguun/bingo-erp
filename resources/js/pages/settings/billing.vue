<template>
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">{{ $t("Billing Settings") }}</h3>
    </div>
    <div class="card-body">
      <!-- active-subscription start -->
      <div>
        <!-- subscribed block -->
        <div v-if="tenant && !tenant.on_trial && tenant.is_subscribed">
          <div class="subscribe-plan">
            <img :src="tenant.plan.image_url" alt="plan-name" class="w-16 h-16 mr-3" />
            <div>
              <p>
                {{ $t("Subscribed to") }} {{ tenant && tenant.plan.name }}
                {{ $t("Plan until") }}
                {{ tenant && tenant.plan_ends_at | moment("Do MMM, YYYY") }}
              </p>
              <span class="text-xs text-gray-700 block">
                {{ tenant && tenant.plan.description }}
              </span>
            </div>

            <!-- manually change plan from one to another -->
            <div>
              <button class="btn btn-primary btn-block" type="button" @click="setShowPlan">
                {{ $t("Change plan") }}
              </button>
            </div>
          </div>
        </div>

        <!-- subscription plans ended block -->
        <div v-else-if="tenant.plan_ends_at &&
        !tenant.is_subscribed
        " class="mt-3">
          <div class="resume-alert">
            {{ $t("Subscription expired") }}
          </div>
        </div>

        <!-- trial end -->
        <div v-else-if="!tenant.is_subscribed && !tenant.on_trial" class="trial-block">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <div>
            <h4>
              {{ $t("Your trial period ended") }}
              {{ tenant && tenant.trial_ends_at | moment("from", "now") }}
            </h4>
          </div>
        </div>

        <!-- trial block -->
        <div v-else class="trial-block">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <div>
            <h4>
              {{ $t("You are on Trial Period for the") }}
              {{ tenant && tenant.plan.name }} {{ $t("plan") }}
            </h4>
            <p>
              {{ $t("You have") }}
              {{ tenant && tenant.trial_ends_at | moment("from", "now") }}
              {{ $t("days left on your trial") }}
            </p>
          </div>
        </div>
      </div>
      <!-- active-subscription end -->

      <!-- subscription-form-start -->
      <form class="form-horizontal" @submit.prevent="submitForm">
        <!-- plans -->
        <div class="pricing-wrap" v-if="this.$store.state.operations.showPlan">
          <h6 class="my-3">{{ $t("Subscribe to a plan below") }}</h6>
          <div class="form-group col-md-12 col-xl-12">
              <ul class="nav nav-tabs justify-content-center">
                <!-- Monthly Tab -->
                <li class="nav-item">
                  <a
                    class="nav-link"
                    :class="{ active: form.selectedPlanType === 'month' }"
                    @click="form.selectedPlanType = 'month'"
                    role="button"
                  >
                    {{ $t('Monthly') }}
                  </a>
                </li>
                <!-- Yearly Tab -->
                <li class="nav-item">
                  <a
                    class="nav-link"
                    :class="{ active: form.selectedPlanType === 'year' }"
                    @click="form.selectedPlanType = 'year'"
                    role="button"
                  >
                    {{ $t('Yearly') }}
                    <span v-if="centralPlanDiscount > 0" class="small-text">{{ centralPlanDiscount }}% OFF</span>
                  </a>
                </li>
              </ul>
            </div>
          <plans @changePlan="changePlan" :planIdError="form.errors.has('plan_id')" :hasError="form"
            :selectedPlan="form.plan_id" :selectedPlanType="form.selectedPlanType" />
        </div>

        <div :class="{ 'd-none': form.plan_id == null }">
          <div>
            <div class="bg-success p-3 rounded my-3" v-if="showExchangeRate">
              <strong class="text-capitalize">
                <i class="icon fas fa-info"></i> Dollar exchange rate is: {{ centralCurrency.symbol }} {{
        centralCurrencyRate }} {{ centralCurrencyRateBasedOn }} <br>
                  {{ $t("You will be charged") }}: $
                  <span v-if="form.selectedPlanType == 'month'">
                    {{ totalAmount }}
                  </span>
                  <span v-else>
                    {{ getDiscountedPrice(totalAmount, centralPlanDiscount) }}
                  </span>
              </strong>
            </div>
            <div class="form-group row">
              <label for="quantity" class="col-sm-3 col-form-label">
                <p>
                  {{ form.selectedPlanType === 'month' ? $t("Number of Months") : $t("Number of Years") }} 
                  <span class="required">*</span>
                </p>
              </label>
              <div class="col-sm-9">
                <input type="number" min="1" max="9999" v-model="form.quantity" @input="updateQuantity"
                  class="form-control" :class="{ 'is-invalid': form.errors.has('quantity') }" id="quantity" />
                <has-error :form="form" field="quantity" />
              </div>
            </div>

            <div class="form-group row">
              <label for="payment_method" class="col-sm-3 col-form-label">
                {{ $t("Payment Method") }} <span class="required">*</span>
              </label>
              <div class="col-sm-9">
                <select id="payment_method" v-model="form.payment_method" name="payment_method" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('payment_method') }"
                  @change="changeSelectedPaymentMethod(form.quantity, form.plan_id)">
                  <option :value="null" disabled>
                    {{ $t('Select a payment method') }}
                  </option>
                  <option v-for="paymentMethod in paymentMethods" :key="paymentMethod.identifier"
                    :value="paymentMethod.identifier">
                    {{ paymentMethod.name }}
                  </option>
                </select>
                <has-error :form="form" field="payment_method" />
              </div>
            </div>

            <div v-if="selectedPaymentMethod && selectedPaymentMethod.identifier === 'manual'"
              class="alert alert-warning">
              {{ selectedPaymentMethod.note }}
            </div>

            <div v-if="form.payment_method === 'manual'" class="form-group row">
              <label for="transaction_id" class="col-sm-3 col-form-label">
                {{ $t("Transaction ID") + ' (' + $t("If have any") + ')' }}
              </label>
              <div class="col-sm-9">
                <input type="text" v-model="form.transaction_id" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('transaction_id') }" id="transaction_id"
                  :placeholder="$t('123456XYZ789')" />
                <has-error :form="form" field="transaction_id" />
              </div>
            </div>

            <div v-if="form.payment_method === 'manual'" class="form-group row">
              <label for="document_path" class="col-sm-3 col-form-label">
                {{ $t("Document") + ' (' + $t("If have any") + ')' }}
              </label>
              <div class="col-sm-9">
                <input type="file" v-on:change="(e) => {
        form.document_path = e.target.files[0]
      }" class="form-control-file" :class="{ 'is-invalid': form.errors.has('document_path') }" id="document_path" />
                <has-error :form="form" field="document_path" />
              </div>
            </div>

            <div class="form-group">
              <!-- subscribe button -->
              <button class="btn btn-block btn-success" type="submit" :disabled="loading" :class="{
        'btn-loading': loading,
      }">
                {{ $t("Subscribe") }}
              </button>
            </div>
          </div>
        </div>
      </form>
      <!-- subscription-form-end -->
    </div>
  </div>
</template>

<script>
import Form from "vform";
import axios from "axios";
import { mapGetters } from "vuex";
import Plans from "~/components/billing/Plans.vue";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("Billing Settings") };
  },
  created() {
    this.centralAppInfo()
    this.getCentralPlanDiscount();
    this.getPaymentMethods();
    this.passedPlanEndsAt();
    this.toggleShowPlan();
  },
  computed: {
    ...mapGetters("operations", ["tenant"]),
  },
  components: {
    Plans,
  },
  data: () => ({
    breadcrumbsCurrent: "Billing Settings",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "home",
      },
      {
        name: "Billing Settings",
        url: "",
      },
    ],
    subscription: false,
    showPlan: false,
    showResumePlanLink: false,
    isLoading: false,
    showExchangeRate: false,
    loading: false,
    plans: [],
    paymentMethods: [],
    currentSubscriptions: [],
    centralCurrency: null,
    centralCurrencyRate: null,
    centralCurrencyRateBasedOn: "",
    totalAmount: null,
    selectedPaymentMethod: null,
    centralPlanDiscount: null,
    form: new Form({
      plan_id: null,
      selectedPlanType: 'month',
      quantity: 1,
      payment_method: null,
      transaction_id: null,
      document_path: null,
    }),
  }),
  methods: {
    getCentralPlanDiscount() {
      this.$axios
        .get("/api/central-plan-discount")
        .then((response) => {
          this.centralPlanDiscount = response.data.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },

    getDiscountedPrice(planAmount, discount) {
      const yearlyPrice = planAmount * 12;
      const discountAmount = (yearlyPrice * discount) / 100;
      return yearlyPrice - discountAmount;
    },

    changeSelectedPaymentMethod(quantity, plan_id) {
      this.selectedPaymentMethod = this.paymentMethods.find(el => el.identifier === this.form.payment_method)
      this.exchangeRate(this.selectedPaymentMethod, quantity, plan_id);
    },

    updateQuantity() {
      this.exchangeRate(this.selectedPaymentMethod, this.form.quantity, this.form.plan_id);
    },

    exchangeRate(paymentMethod, quantity, plan_id) {
      if (paymentMethod && paymentMethod.name !== "Manual Payment") {
        this.$axios.get('/api/central-currency-exchange-rate-info', {
          params: {
            quantity: quantity,
            plan_id: plan_id
          }
        })
          .then((response) => {
            this.centralCurrencyRate = response.data.rate;
            this.centralCurrencyRateBasedOn = response.data.basedOn;
            this.totalAmount = response.data.total;
          }).catch((error) => {
            console.log(error)
          })
        this.showExchangeRate = true;
      } else {
        this.showExchangeRate = false;
      }
    },

    async getPaymentMethods() {
      this.isLoading = true
      await axios.get(window.location.origin + "/api/subscriptions/payment-methods")
        .then(({ data: { data } }) => {
          this.paymentMethods = data
        })
        .catch((err) => console.log(err))
        .finally(() => {
          this.isLoading = false
        });
    },

    centralAppInfo() {
      this.$axios.get('/api/central-currency')
        .then((response) => {
          this.centralCurrency = response.data.data;
        }).catch((error) => {
          console.log(error)
        })
    },

    passedPlanEndsAt() {
      if (new Date(this.tenant.plan_ends_at) < new Date()) {
        this.$store.dispatch("operations/setShowPlan", true);
        this.showResumePlanLink = false;
        return true;
      }

      this.showResumePlanLink = true;
      return false;
    },

    // get settings
    async refreshTenant() {
      await this.$store.dispatch("operations/fetchTenant");
    },

    changePlan(plan_id) {
      this.form.plan_id = plan_id;
      this.exchangeRate(this.selectedPaymentMethod, this.form.quantity, this.form.plan_id);
    },

    submitForm() {
      this.loading = true;
      this.form.post("/api/subscription-requests")
        .then(({ data }) => {
          if (data.url) {
            window.location.href = data.url;
            return;
          }
          if (this.form.payment_method === 'manual') {
            this.$router.push({ name: 'settings.billing.subscription-requests' });
            toast.fire({
            type: "success",
            title: this.$t("Subscription request sent."),
          });
            return;
          }
          this.refreshTenant();
          this.form.reset();
          // this.$store.dispatch("operations/setShowPlan", false);
        })
        .finally(() => {
          this.loading = false;
        });
    },

    toggleShowPlan() {
      if (this.tenant && !this.tenant.plan_id) this.setShowPlan();
    },
    setShowPlan() {
      this.$store.dispatch("operations/setShowPlan", true);
    },
  },
};
</script>

<style scoped>
.pricing-wrap h2 {
  font-size: 15px;
  color: #4c4e51;
}

.subscribe-plan {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.subscribe-plan img {
  max-width: 100px;
}

.subscribe-plan>div p {
  margin-bottom: 0px;
  font-size: 18px;
  font-weight: 400;
}

.switch-plan {
  justify-content: space-between;
  margin-top: 30px;
}

.resume-alert {
  background: #dc3545;
  padding: 20px;
  border-radius: 8px;
  color: #fff;
  font-size: 18px;
}

.resume-subs-block p {
  margin-right: 9px;
  margin-bottom: 0;
}

.resume-subs-block {
  display: flex;
  justify-content: flex-end;
  margin-top: 11px;
}

.small-text {
  font-size: 10px;
}
</style>
