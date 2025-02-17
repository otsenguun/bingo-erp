<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="col-lg-12">
      <div class="row set-up-page">
        <div v-if="$can('general-settings')" class="col-xl-4 col-sm-12 col-md-6">
          <div class="card card-large-icons">
            <div class="card-icon text-white bg-success">
              <i class="fas fa-cog" />
            </div>
            <div class="card-body">
              <h4>{{ $t('General') }}</h4>
              <p>
                {{ $t('General settings such as, site title, site description, address and so on.') }}
              </p>
              <router-link :to="{ name: 'setup.general' }" href="#" class="card-cta">
                {{ $t('Change Setting') }}
                <i class="fas fa-long-arrow-alt-right" />
              </router-link>
            </div>
          </div>
        </div>
        <div v-if="$can('general-settings')" class="col-xl-4 col-sm-12 col-md-6">
          <div class="card card-large-icons">
            <div class="card-icon text-white bg-success">
              <i class="fas fa-envelope" />
            </div>
            <div class="card-body">
              <h4>{{ $t('Mail Configurations') }}</h4>
              <p>
                {{ $t('Manage your mail configuration such as HOST, USERNAME, PASSWORD etc.') }}
              </p>
              <router-link :to="{ name: 'setup.mailConfiguration' }" href="#" class="card-cta">
                {{ $t('Mail Configurations') }}
                <i class="fas fa-long-arrow-alt-right" />
              </router-link>
            </div>
          </div>
        </div>
        <div v-if="$can('general-settings')" class="col-xl-4 col-sm-12 col-md-6">
          <div class="card card-large-icons">
            <div class="card-icon text-white bg-success">
              <i class="fas fa-sms" />
            </div>
            <div class="card-body">
              <h4>{{ $t('SMS Configurations') }}</h4>
              <p>
                {{ $t('Manage your sms configuration such as TWILIO ACCOUNT SID, FROM etc.') }}
              </p>
              <router-link :to="{ name: 'setup.smsConfiguration' }" href="#" class="card-cta">
                {{ $t('SMS Configurations') }}
                <i class="fas fa-long-arrow-alt-right" />
              </router-link>
            </div>
          </div>
        </div>

        <div v-if="developer" class="col-xl-4 col-sm-12 col-md-6">
          <div class="card card-large-icons">
            <div class="card-icon text-white bg-success">
              <i class="fas fa-braille" />
            </div>
            <div class="card-body">
              <h4>{{ $t('User Permissions') }}</h4>
              <p>
                {{ $t('Manage permission that you are going to assign in a role.') }}
              </p>
              <router-link :to="{ name: 'permissions.index' }" class="card-cta">
                {{ $t('User Permissions') }}
                <i class="fas fa-long-arrow-alt-right" />
              </router-link>
            </div>
          </div>
        </div>
        <div v-if="$can('user-role')" class="col-xl-4 col-sm-12 col-md-6">
          <div class="card card-large-icons">
            <div class="card-icon text-white bg-success">
              <i class="fas fa-user-lock" />
            </div>
            <div class="card-body">
              <h4>{{ $t('Role & Permissions') }}</h4>
              <p>
                {{ $t('Manage Roles & Permissions for users who are going to use the system.') }}
              </p>
              <router-link :to="{ name: 'roles.index' }" class="card-cta">
                {{ $t('Roles & Permissions') }}
                <i class="fas fa-long-arrow-alt-right" />
              </router-link>
            </div>
          </div>
        </div>
        <div v-if="$can('currencies-management')" class="col-xl-4 col-sm-12 col-md-6">
          <div class="card card-large-icons">
            <div class="card-icon text-white bg-success">
              <i class="fas fa-money-check-alt" />
            </div>
            <div class="card-body">
              <h4>{{ $t('Currencies') }}</h4>
              <p>
                {{ $t('Manage various types of currencies that you are going to use in the system.') }}
              </p>
              <router-link :to="{ name: 'currencies.index' }" class="card-cta">
                {{ $t('Currencies') }}
                <i class="fas fa-long-arrow-alt-right" />
              </router-link>
            </div>
          </div>
        </div>
        <div v-if="$can('units-management')" class="col-xl-4 col-sm-12 col-md-6">
          <div class="card card-large-icons">
            <div class="card-icon text-white bg-success">
              <i class="fas fa-balance-scale" />
            </div>
            <div class="card-body">
              <h4>{{ $t('Units') }}</h4>
              <p>
                {{ $t('Manage unit types for measurement that you are going to use in the system.') }}
              </p>
              <router-link :to="{ name: 'units.index' }" class="card-cta">
                {{ $t('Units') }}
                <i class="fas fa-long-arrow-alt-right" />
              </router-link>
            </div>
          </div>
        </div>
        <div v-if="$can('vat-rate-management')" class="col-xl-4 col-sm-12 col-md-6">
          <div class="card card-large-icons">
            <div class="card-icon text-white bg-success">
              <i class="fas fa-percentage" />
            </div>
            <div class="card-body">
              <h4>{{ $t('Tax Rates') }}</h4>
              <p>
                {{ $t('Manage Tax rates for Tax management that you are going to use in the system.') }}
              </p>
              <router-link :to="{ name: 'vatRates.index' }" class="card-cta">
                {{ $t('Tax Rates') }}
                <i class="fas fa-long-arrow-alt-right" />
              </router-link>
            </div>
          </div>
        </div>
        <div v-if="$can('brands-management')" class="col-xl-4 col-sm-12 col-md-6">
          <div class="card card-large-icons">
            <div class="card-icon text-white bg-success">
              <i class="fas fa-bold" />
            </div>
            <div class="card-body">
              <h4>{{ $t('Brands') }}</h4>
              <p>{{ $t('Manage brands that you are going to use in the system.') }}</p>
              <router-link :to="{ name: 'brands.index' }" class="card-cta">
                {{ $t('Brands') }}
                <i class="fas fa-long-arrow-alt-right" />
              </router-link>
            </div>
          </div>
        </div>
        <div v-if="$can('payment-method-management')" class="col-xl-4 col-sm-12 col-md-6">
          <div class="card card-large-icons">
            <div class="card-icon text-white bg-success">
              <i class="fas fa-wallet" />
            </div>
            <div class="card-body">
              <h4>{{ $t('Payment Methods') }}</h4>
              <p>
                {{ $t('Manage payment methods that you are going to use in the system.') }}
              </p>
              <router-link :to="{ name: 'paymentMethods.index' }" class="card-cta">
                {{ $t('Payment Methods') }}
                <i class="fas fa-long-arrow-alt-right" />
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('Setup') }
  },

  data: () => ({
    breadcrumbsCurrent: 'Setup',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Setup',
        url: '',
      },
    ],
    developer: '',
  }),
  // Map Getters
  computed: {
    ...mapGetters('auth', ['user']),
  },
  created() {
    this.developer = this.user.roles.includes('developer')
  },
}
</script>
