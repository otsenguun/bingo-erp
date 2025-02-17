<template>
  <div class="card settings-card no-print">
    <div class="card-header">{{ $t("Settings") }}</div>
    <div class="card-body">
      <ul class="nav flex-column nav-pills m-1">
        <li class="nav-item">
          <router-link :to="{ name: 'setup.general' }" class="nav-link thumb">
            <i class="fas fa-cog" />
            {{ $t("General") }}
          </router-link>
        </li>
        <li class="nav-item">
          <router-link :to="{ name: 'setup.mailConfiguration' }" class="nav-link thumb">
            <i class="fas fa-envelope" />
            {{ $t("Mail Configurations") }}
          </router-link>
        </li>
        <li class="nav-item">
          <router-link :to="{ name: 'setup.smsConfiguration' }" class="nav-link thumb">
            <i class="fas fa-sms" />
            {{ $t("SMS Configurations") }}
          </router-link>
        </li>
        <li class="nav-item" v-if="developer">
          <router-link :to="{ name: 'permissions.index' }" class="nav-link thumb">
            <i class="fas fa-braille" />
            {{ $t("User Permissions") }}
          </router-link>
        </li>
        <li v-if="$can('user-role')" class="nav-item">
          <router-link :to="{ name: 'roles.index' }" class="nav-link thumb">
            <i class="fas fa-user-lock" />
            {{ $t("Role & Permissions") }}
          </router-link>
        </li>
        <li v-if="$can('currencies-management')" class="nav-item">
          <router-link :to="{ name: 'currencies.index' }" class="nav-link thumb">
            <i class="fas fa-money-check-alt" />
            {{ $t("Currencies") }}
          </router-link>
        </li>
        <li v-if="$can('units-management')" class="nav-item">
          <router-link :to="{ name: 'units.index' }" class="nav-link thumb">
            <i class="fas fa-balance-scale" />
            {{ $t("Units") }}
          </router-link>
        </li>
        <li v-if="$can('vat-rate-management')" class="nav-item">
          <router-link :to="{ name: 'vatRates.index' }" class="nav-link thumb">
            <i class="fas fa-percentage" />
            {{ $t("Tax Rates") }}
          </router-link>
        </li>
        <li v-if="$can('brands-management')" class="nav-item">
          <router-link :to="{ name: 'brands.index' }" class="nav-link thumb">
            <i class="fas fa-bold" />
            {{ $t("Brands") }}
          </router-link>
        </li>
        <li v-if="$can('payment-method-management')" class="nav-item">
          <router-link :to="{ name: 'paymentMethods.index' }" class="nav-link thumb">
            <i class="fas fa-wallet" />
            {{ $t("Payment Methods") }}
          </router-link>
        </li>
      </ul>
    </div>
  </div>
</template>


<script>
import { mapGetters } from "vuex";

export default {
  name: "SettingsSidebar",
  data: () => ({
    developer: "",
  }),
  // Map Getters
  computed: {
    ...mapGetters("auth", ["user"]),
  },
  created() {
    this.developer = this.user.roles.includes("developer");
  },
  methods: {},
};
</script>
