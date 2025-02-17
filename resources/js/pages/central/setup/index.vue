<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="col-lg-12">
      <div class="row set-up-page">
        <div v-if="$can('general-settings')" class="col-xxl-3 col-lg-4 col-sm-12 col-md-6
        ">
          <div class="card card-large-icons">
            <div class="card-icon text-white bg-success">
              <i class="fas fa-cog" />
            </div>
            <div class="card-body">
              <h5>{{ $t('General Settings') }}</h5>
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
        <div v-if="$can('general-settings')" class="col-xxl-3 col-lg-4 col-sm-12 col-md-6
        ">
          <div class="card card-large-icons">
            <div class="card-icon text-white bg-success">
              <i class="fas fa-envelope" />
            </div>
            <div class="card-body">
              <h5>{{ $t('Mail Configurations') }}</h5>
              <p>
                {{ $t('Manage your mail configuration such as HOST, USERNAME, PASSWORD etc.') }}
              </p>
              <router-link :to="{ name: 'setup.mailConfiguration' }" href="#" class="card-cta">
                {{ $t('Change Setting') }}
                <i class="fas fa-long-arrow-alt-right" />
              </router-link>
            </div>
          </div>
        </div>
        <div v-if="$can('general-settings')" class="col-xxl-3 col-lg-4 col-sm-12 col-md-6
        ">
          <div class="card card-large-icons">
            <div class="card-icon text-white bg-success">
              <i class="fas fa-sms" />
            </div>
            <div class="card-body">
              <h5>{{ $t('SMS Configurations') }}</h5>
              <p>
                {{ $t('Manage your sms configuration such as TWILIO ACCOUNT SID, FROM etc.') }}
              </p>
              <router-link :to="{ name: 'setup.smsConfiguration' }" href="#" class="card-cta">
                {{ $t('Change Setting') }}
                <i class="fas fa-long-arrow-alt-right" />
              </router-link>
            </div>
          </div>
        </div>
        <div v-if="$can('payment-settings')" class="col-xxl-3 col-lg-4 col-sm-12 col-md-6">
          <div class="card card-large-icons">
            <div class="card-icon text-white bg-success">
              <i class="fas fa-money-check-alt" />

            </div>
            <div class="card-body">
              <h5>{{ $t('Payment Settings') }}</h5>
              <p>
                {{ $t('Manage payment settings. Add your stripe information.') }}
              </p>
              <router-link :to="{ name: 'advanced-settings' }" class="card-cta">
                {{ $t('Change Settings') }}
                <i class="fas fa-long-arrow-alt-right" />
              </router-link>
            </div>
          </div>
        </div>
        <div v-if="$can('user-role')" class="col-xxl-3 col-lg-4 col-sm-12 col-md-6">
          <div class="card card-large-icons">
            <div class="card-icon text-white bg-success">
              <i class="fas fa-user-lock" />
            </div>
            <div class="card-body">
              <h5>{{ $t('Role & Permissions') }}</h5>
              <p>
                {{ $t('Manage Roles & Permissions to assign them to employees.') }}
              </p>
              <router-link :to="{ name: 'roles.index' }" class="card-cta">
                {{ $t('Roles & Permissions') }}
                <i class="fas fa-long-arrow-alt-right" />
              </router-link>
            </div>
          </div>
        </div>
        <div v-if="$can('user-management')" class="col-xxl-3 col-lg-4 col-sm-12 col-md-6">
          <div class="card card-large-icons">
            <div class="card-icon text-white bg-success">
              <i class="fas fa-users-cog" />
            </div>
            <div class="card-body">
              <h5>{{ $t('User Management') }}</h5>
              <p>
                {{ $t('Create system user and assign individual role to users.') }}
              </p>
              <router-link :to="{ name: 'user.index' }" class="card-cta">
                {{ $t('Manage User') }}
                <i class="fas fa-long-arrow-alt-right" />
              </router-link>
            </div>
          </div>
        </div>
        <div v-if="$can('general-settings')" class="col-xxl-3 col-lg-4 col-sm-12 col-md-6
        ">
          <div class="card card-large-icons">
            <div class="card-icon text-white bg-success">
              <i class="fas fa-money-check-alt" />
            </div>
            <div class="card-body">
              <h5>{{ $t('Currency Settings') }}</h5>
              <p>
                {{ $t('Manage your currency') }}
              </p>
              <router-link :to="{ name: 'setup.centralCurrency.index' }" class="card-cta">
                {{ $t('Change Setting') }}
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
  layout: 'central',
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
