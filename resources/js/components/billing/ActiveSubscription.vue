<template>
  <div>
    <!-- subscribed block -->
    <div v-if="tenant && !tenant.on_trial && tenant.is_subscribed">
      <div class="subscribe-plan">
        <img
          :src="'/' + tenant.plan.image"
          alt="plan-name"
          class="w-16 h-16 mr-3"
        />
        <div>
          <p>
            {{ $t("Subscribed to") }} {{ tenant && tenant.plan.name }}
            {{ $t("Plan") }}
          </p>
          <span class="text-xs text-gray-700 block">
            {{ tenant && tenant.plan.description }}
          </span>
        </div>
      </div>

      <!--  if tenant brought the subscription using stripe -->
      <div
        v-if="!tenant.plan_ends_at && !tenant.is_manual_subscription"
        class="d-flex justify-between align-items-center switch-plan"
      >
        <button @click="setShowPlan" class="btn btn-secondary">
          {{ $t("Switch My Plan") }}
        </button>
        <button
          @click="$emit('cancelSubscription', tenant.plan_id)"
          class="btn btn-danger"
        >
          {{ $t("Cancel Subscription") }}
        </button>
      </div>

      <!--  if tenant brought the subscription manually -->
      <div
        v-else-if="!tenant.plan_ends_at && tenant.is_manual_subscription"
        class="d-flex justify-content-between align-items-center switch-plan"
      >
        <div class="text-left">
          <h6>{{ $t("Manual subscription by Admin is active") }}</h6>
          <h6>{{ $t("You can cancel it from the admin") }}</h6>
        </div>

        <div class="text-right">
          <h6>
            {{ $t("Manually subscribed by") }}
            {{ tenant.manually_subscribed_by }}
          </h6>
          <h6>
            {{ $t("Manually subscribed at") }}
            {{ tenant.manually_subscribed_at | moment("Do MMM, YYYY") }}
          </h6>
        </div>
      </div>

      <!-- subscription plans will end but still subscribed and is from stripe block -->
      <div
        v-else-if="tenant.plan_ends_at && !tenant.is_manual_subscription"
        class="mt-3"
      >
        <div class="resume-alert">
          {{
            $t(
              "You have cancelled your subscription and your account is still active until"
            )
          }}
          {{ tenant && tenant.plan_ends_at | moment("Do MMM, YYYY") }}
        </div>
        <div v-show="showResumePlanLink" class="resume-subs-block">
          <p>{{ $t("Or, You Can") }}</p>
          <a @click="$emit('resumeSubscription', tenant.plan_id)" href="">
            {{ $t("Resume Subscription") }}
          </a>
        </div>
      </div>
    </div>

    <!-- subscription plans ended and is from stripe block -->
    <div
      v-else-if="
        tenant.plan_ends_at &&
        !tenant.is_subscribed &&
        !tenant.is_manual_subscription
      "
      class="mt-3"
    >
      <div class="resume-alert">
        {{
          $t(
            "You have cancelled your subscription and your account is still active until"
          )
        }}
        {{ tenant && tenant.plan_ends_at | moment("Do MMM, YYYY") }}
      </div>
      <div v-show="showResumePlanLink" class="resume-subs-block">
        <p>{{ $t("Or, You Can") }}</p>
        <a @click="$emit('resumeSubscription', tenant.plan_id)" href="">
          {{ $t("Resume Subscription") }}
        </a>
      </div>
    </div>

    <!-- subscription plans ended and is manual subscription block -->
    <div
      v-else-if="
        tenant.plan_ends_at &&
        !tenant.is_subscribed &&
        tenant.is_manual_subscription
      "
      class="mt-3"
    >
      <div class="resume-alert">
        {{ $t("Admin have cancelled your subscription") }}
        {{ $t("Contact to Admin for re enable the subscription") }}
      </div>
    </div>

    <!-- trial end -->
    <div
      v-else-if="!tenant.is_subscribed && !tenant.on_trial"
      class="trial-block"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        stroke-width="2"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
        />
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
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        stroke-width="2"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
        />
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
</template>

<script>
import { mapGetters } from "vuex";

export default {
  props: {
    showResumePlanLink: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    ...mapGetters("operations", ["tenant"]),
  },
  data: () => ({
    loading: false,
  }),
  created() {
    this.toggleShowPlan();
  },
  methods: {
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

</style>
