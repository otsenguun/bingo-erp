<template>
  <!-- PICING AREA START -->
  <section class="section" id="pricing-area">
    <div class="sec-heading">
      <span v-if="appInfo && appInfo.pricing_plan_section_tagline">{{
        appInfo && appInfo.pricing_plan_section_tagline
      }}</span>
      <h2 v-if="appInfo && appInfo.pricing_plan_section_title">{{ appInfo && appInfo.pricing_plan_section_title }}</h2>
    </div>
    <div class="container">
      <div class="form-group mx-auto">
          <div class="form-group col-md-12 col-xl-12 text-center">
            <ul class="nav nav-tabs justify-content-center">
              <!-- Monthly Tab -->
              <li class="nav-item">
                <a
                  class="nav-link"
                  :class="{ active: selectedPlanType === 'month' }"
                  @click="selectedPlanType = 'month'"
                  :style="selectedPlanType === 'month' ? activeTabStyle : ''"
                  role="button"
                >Monthly</a>
              </li>
              <!-- Yearly Tab with Discount -->
              <li class="nav-item">
                <a
                  class="nav-link"
                  :class="{ active: selectedPlanType === 'year' }"
                  @click="selectedPlanType = 'year'"
                  :style="selectedPlanType === 'year' ? activeTabStyle : ''"
                  role="button"
                >Yearly <span v-if="appInfo.plan_discount > 0" class="small-text">{{ appInfo.plan_discount }}% OFF</span>
                </a>
              </li>
            </ul>
          </div>
      </div>
      <div class="row">
        <div v-for="(plan, i) in appInfo && appInfo.plans" :key="i" class="col-md-4">
          <div class="pricing-single">
            <div class="pricing-head">
              <h2>{{ plan.name }}</h2>
              <p v-if="plan.description">{{ plan.description }}</p>
            </div>
            <div class="price">
              <h4 v-if="selectedPlanType === 'year'"><span class="plan-amount-strike-through">{{ isCurrencySelected ? plan.symbol : appInfo.currency.symbol}}{{(plan.amount * 12)}}</span>{{ isCurrencySelected ? plan.symbol : appInfo.currency.symbol}}{{ getDiscountedPrice(plan.amount, appInfo.plan_discount) }}</h4>
              <h4 v-else>{{ isCurrencySelected ? plan.symbol : appInfo.currency.symbol}}{{ plan.amount }}</h4>
              <p class="price-text"><span>What’s Included</span></p>
            </div>
            <div class="price-item">
              <ul>
                <li>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-success" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>Client limit: {{ plan.limit_clients | limitFormat }}</span>
                </li>
                <li>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-success" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>Domain limit: {{ plan.limit_domains | limitFormat }}</span>
                </li>
                <li>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-success" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>Employees limit: {{ plan.limit_employees | limitFormat }}</span>
                </li>
                <li>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-success" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>Suppliers limit: {{ plan.limit_suppliers | limitFormat }}</span>
                </li>
                <li>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-success" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>Purchase limit: {{ plan.limit_purchases | limitFormat }}</span>
                </li>
                <li>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-success" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>Invoice limit: {{ plan.limit_invoices | limitFormat }}</span>
                </li>

                <li v-for="feature in plan.features" :key="feature.id">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-success" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>{{ feature.name }}</span>
                </li>

                <li v-for="all_feature in plan.all_features" :key="all_feature.id">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-danger" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>{{ all_feature.name }}</span>
                </li>
              </ul>
              <router-link :to="{ name: 'register' }" class="nav-link">Buy Now</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- SCREENSHOT AREA END -->
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'Pricing',

  data: () => ({
    selectedCurrencyForPriceShow: null,
    isCurrencySelected: false,
    selectedPlanType: 'month',
    activeTabStyle: {
      backgroundColor: '#6184f5',
      color: '#fff'
    }
  }),

  methods: {
    getDiscountedPrice(planAmount, discount) {
      const yearlyPrice = planAmount * 12;
      const discountAmount = (yearlyPrice * discount) / 100;
      return (yearlyPrice - discountAmount).toFixed(2);
    },

    parseJson(data) {
      return JSON.parse(data)
    }
  },

  // Map Getters
  computed: {
    ...mapGetters('operations', ['appInfo']),
  },
}
</script>

<style lang="scss" scoped>
.plan-amount-strike-through{
  text-decoration: line-through; 
  font-size: 14px;
}
  .small-text {
    font-size: 10px;
  }
</style>
