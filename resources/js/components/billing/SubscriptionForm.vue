<template>
  <div>

  </div>
</template>

<script>
import Plans from "~/components/billing/Plans";
import Form from "vform";

export default {
  name: "SubscriptionForm",

  components: {
    Plans,
  },

  props: {
    tenant: {
      type: Object,
      default: () => ({}),
    },
  },

  created() {
    this.name = this.tenant.name;
  },

  data: () => ({
    loading: false,
    plans: [],
    paymentMethods: [],
    currentSubscriptions: [],
    form: new Form({
      plan_id: null,
      quantity: 1,
      transaction_id: null,
      document_path: null,
    }),
  }),

  methods: {
    // get settings
    async refreshTenant() {
      await this.$store.dispatch("operations/fetchTenant");
    },

    changePlan(plan_id) {
      this.form.plan_id = plan_id;
    },

    submitForm() {
      this.form.post("/api/subscription-requests").then(() => {
        this.refreshTenant();
        this.form.reset();
        this.loading = false;
        // this.$store.dispatch("operations/setShowPlan", false);
        toast.fire({
          type: "success",
          title: this.$t("Subscription request sent."),
        });
      });
    },
  },
};
</script>

<style scoped>
.pricing-wrap h2 {
  font-size: 15px;
  color: #4c4e51;
}
</style>
