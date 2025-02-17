<template>
  <div>
    <div v-for="(plan, i) in plans" :key="i">

      <input @change="$emit('changePlan', plan.id)" v-model="selectedPlanId" :value="plan.id" :id="plan.id" type="radio"
        name="plan_id" class="radio-plan d-none" :class="{ 'is-invalid': planIdError }">
      <has-error :form="hasError" field="plan_id" />
      <label :for="plan.id" class="price-single" :class="{ 'selected': plan.id === selectedPlanId && !currentPlan(plan.id), 'current-plan': currentPlan(plan.id) }">
        <img :src="plan.image" :alt="plan.name" class="w-16 h-16 mr-3" />
        <div>
          <span>{{ plan.name }} <span v-if="currentPlan(plan.id)" class="activePlanText text-capitalize">
            {{ $t('Current Plan') }}
          </span></span>
          <span class="text-xs text-gray-700 d-block">{{ plan.description }}</span>
          <ul>
            <li>{{ $t('Client Limit') }}: <span>{{ plan.limit_clients | limitFormat }}</span></li>
            <li>{{ $t('Domains Limit') }}: <span>{{ plan.limit_domains | limitFormat }}</span></li>
            <li>{{ $t('Employee Limit') }}: <span>{{ plan.limit_employees | limitFormat }}</span></li>
            <li>{{ $t('Supplier Limit') }}: <span>{{ plan.limit_suppliers | limitFormat }}</span></li>
            <li>{{ $t('Purchase Limit') }}: <span>{{ plan.limit_purchases | limitFormat }}</span></li>
            <li>{{ $t('Invoice Limit') }}: <span>{{ plan.limit_invoices | limitFormat }}</span></li>
          </ul>
          <span class="price-badge">
            <template v-if="centralPlanDiscount > 0 && selectedPlanType === 'year'">
              <span class="line-through small-text">
                {{ centralCurrency.symbol }}{{ plan.amount * 12 }} 
              </span>
              <strong class="text-strong text-capitalize">
                {{ centralCurrency.symbol
                }}{{ getDiscountedPrice(plan.amount, centralPlanDiscount) }}
              </strong>
              / {{ selectedPlanType === "year" ? "Year" : "Month" }}
            </template>

            <template v-else>
              <span v-if="centralCurrency">
                {{ centralCurrency.symbol }}{{ getAdjustedPrice(plan.amount) }} /
                {{ selectedPlanType === "year" ? "Year" : "Month" }}
              </span>
            </template>
          </span>
        </div>
      </label>
    </div>
  </div>
</template>

<script>
import {mapGetters} from "vuex";

export default {
  props: {
    planIdError: {
      type: [String, Boolean],
    },
    hasError: {
      type: Object,
    },
    selectedPlan: {
      type: Number,
    },
    selectedPlanType: {
      type: String,
      required: true,
    },
  },
  data: () => ({
    plans: [],
    selectedPlanId: null,
    centralCurrency: null,
    centralPlanDiscount: null,
  }),
  created() {
    this.centralAppInfo();
    this.getCentralPlanDiscount();
    this.getPlans();
    this.selectedPlanId = this.selectedPlan;
  },
  computed: {
    ...mapGetters('operations', ['tenant']),
  },
  methods: {
    currentPlan(planId){
      if( this.tenant.plan_id == planId){
        return true;
      }
      return false;
    },

    getAdjustedPrice(planAmount) {
      return this.selectedPlanType === "year" ? planAmount * 12 : planAmount;
    },

    getDiscountedPrice(planAmount, discount) {
      const yearlyPrice = planAmount * 12;
      const discountAmount = (yearlyPrice * discount) / 100;
      return (yearlyPrice - discountAmount).toFixed(2);
    },

    getPlans() {
      this.$axios.get('/api/plans')
        .then((response) => {
          this.plans = response.data.data
        }).catch((error) => {
          console.log(error)
        })
    },

    centralAppInfo() {
      this.$axios.get('/api/central-currency')
        .then((response) => {
          this.centralCurrency = response.data.data;
        }).catch((error) => {
          console.log(error)
        })
    },

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

    getImg(data) {
      return data
    }
  },
}
</script>

<style lang="scss" scoped>
.price-single {
  border: 2px solid #c9cafa5c;
  width: 100%;
  padding: 15px 14px !important;
  cursor: pointer;
  position: relative;
  margin: 0 !important;
  display: flex;
  align-items: flex-start;
  margin-bottom: 10px !important;
  border-radius: 5px;
}

.price-single img {
  max-width: 80px;
}

.price-single ul {
  display: grid;
  grid-template-columns: 1fr 1fr;
  margin: 8px 0 0 0;
  padding: 0;
  list-style: none;
}

.price-single ul li {
  font-size: 12px;
  margin-right: 12px;
}

.price-single ul li {
  font-size: 12px;
  color: #8b8f95;
}

.price-single ul li span {
  color: #6366f1;
}

.price-single .price-badge {
  position: absolute;
  right: 0px;
  bottom: 0px;
  margin: 0;
  background: #6366f1;
  color: #fff;
  padding: 3px 8px;
  font-size: 13px;
  border-top-left-radius: 12px;
}

.activePlanText {
  right: 0px;
  top: 12px;
  margin: 0;
  background: green;
  color: #fff;
  padding: 3px 8px;
  font-size: 13px;
  border-radius: 3px;
}

label.price-single.selected {
  border-color: #6366f1;
}

.current-plan {
  background: #ddd;
  border: 2px solid green;
  opacity: 1;
  cursor: no-drop !important;
}

.line-through {
  text-decoration: line-through;
}
.small-text {
  font-size: 11px;
}
</style>
