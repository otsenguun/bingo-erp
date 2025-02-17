<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->

    <div class="row">
      <div class="col-sm-12 col-md-4 col-lg-3 col-xl-2">
        <card :title="$t('Landing Page Settings')" class="settings-card">
          <ul class="nav flex-column nav-pills m-1">
            <li v-for="tab in tabs" :key="tab.route" class="nav-item">
              <router-link :to="{ name: tab.route }" class="nav-link thumb" active-class="active">
                <fa :icon="tab.icon" fixed-width />
                {{ tab.name }}
              </router-link>
            </li>
          </ul>
        </card>
      </div>
      <div class="col-sm-12 col-md-8 col-lg-9 col-xl-10">
        <transition name="fade" mode="out-in">
          <router-view />
        </transition>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  layout: 'central',
  middleware: 'auth',

  metaInfo() {
    return { title: this.$t('Landing Page Settings') }
  },

  data: () => ({
    breadcrumbsCurrent: 'Landing Page Settings',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Landing Page Settings',
        url: '',
      },
    ]
  }),

  computed: {
    tabs() {
      return [
        {
          icon: 'mask',
          name: this.$t('Hero'),
          route: 'settings.hero',
        },
        {
          icon: 'address-card',
          name: this.$t('About Us'),
          route: 'settings.about-us',
        },
        {
          icon: 'question',
          name: this.$t('Why Us'),
          route: 'settings.why-us',
        },
        {
          icon: 'briefcase',
          name: this.$t('Business Start'),
          route: 'settings.business-start',
        },
        {
          icon: 'asterisk',
          name: this.$t('Features'),
          route: 'settings.features',
        },
        {
          icon: 'bolt',
          name: this.$t('Explorers'),
          route: 'settings.explorers',
        },
        {
          icon: 'hourglass-start',
          name: this.$t('All features'),
          route: 'settings.all-features',
        },
        {
          icon: 'space-shuttle',
          name: this.$t('CTA Area'),
          route: 'settings.cta',
        },
        {
          icon: 'eye',
          name: this.$t('Software overview'),
          route: 'settings.software-overview',
        },
        {
          icon: 'money-bill',
          name: this.$t('Pricing plan'),
          route: 'settings.pricing-plan',
        },
        {
          icon: 'star',
          name: this.$t('Testimonial'),
          route: 'settings.testimonial',
        },
        {
          icon: 'newspaper',
          name: this.$t('Brands'),
          route: 'settings.brands',
        },
        {
          icon: 'envelope',
          name: this.$t('Newsletter'),
          route: 'settings.newsletter',
        },
        {
          icon: 'code',
          name: this.$t('Custom HTML'),
          route: 'settings.custom-html',
        },
      ]
    }
  }
}
</script>

<style>
.settings-card .card-header {
  border-bottom: 1px solid#ddd;
  box-shadow: none;
}

.settings-card .card-body {
  padding: 10px;
}

.settings-card .nav-pills .nav-link,
.settings-card .nav-tabs .nav-link {
  justify-content: flex-start;
}

.settings-card .nav-pills .nav-link.active,
.settings-card .nav-pills .show>.nav-link {
  color: #6366f1;
  background-color: #6366f159;
}

.settings-card {
  position: sticky;
  top: 30px;
}
</style>
