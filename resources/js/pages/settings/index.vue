<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->


    <div class="row">
      <div class="col-md-3">
        <card :title="$t('Billing Settings')" class="settings-card">
          <ul class="nav flex-column nav-pills m-1">
            <li v-for="tab in tabs" :key="tab.route" class="nav-item">
              <router-link :to="{ name: tab.route }" class="nav-link" active-class="active" v-if="$can(tab.permission)">
                <i class="mr-2" :class="tab.icon"></i>
                {{ tab.name }}
              </router-link>
            </li>
          </ul>
        </card>
      </div>
      <div class="col-md-9">
        <transition name="fade" mode="out-in">
          <router-view />
        </transition>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  middleware: ['auth', 'check-permissions'],

  metaInfo() {
    return { title: this.$t('Update Profile') }
  },

  data: () => ({
    breadcrumbsCurrent: 'Billing Settings',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Billing Settings',
        url: '',
      },
    ]
  }),

  computed: {
    tabs() {
      return [
        {
          icon: "fas fa-user",
          name: this.$t('profile'),
          route: 'settings.profile',
          permission: 'update-profile',
        },
        {
          icon: "fas fa-money-bill",
          name: this.$t('Billing Settings'),
          route: 'settings.billing',
          permission: 'billing',
        },
        {
          icon: "fas fa-file-invoice",
          name: this.$t('Payments'),
          route: 'settings.billing.payments',
          permission: 'billing',
        },
        {
          icon: "fas fa-paper-plane",
          name: this.$t('Subscription Requests'),
          route: 'settings.billing.subscription-requests',
          permission: 'billing',
        }
      ]
    }
  }
}
</script>

<style>
.settings-card .card-header {
  border-bottom: 1px solid#ddd;

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


.settings-card .nav-pills .nav-link {
  display: flex;
  align-items: center;
}

.settings-card .nav-pills .nav-link svg {
  width: 12px;
  margin-right: 6px;
  height: 12px;
}

.settings-card .nav.flex-column:not(.nav-sidebar)>li {
  border-bottom: none !important;
}

.setings-header {
  padding: 13px 20px;
  background: #ddd;
  border-bottom: 0px;
  display: flex;
  align-items: center;
}

.setings-header h3 {
  margin-top: 0px;
}

.trial-block {
  display: flex;
  align-items: center;
  background: #6366f1;
  padding: 15px;
  border-radius: 5px;
  color: #fff;
}

.trial-block svg {
  width: 80px;
  margin-right: 10px;
  color: #c9cafa;
}

.trial-block div h4 {
  margin-bottom: 0px;
  text-transform: uppercase;
  font-weight: 600;
  font-size: 20px;
}

.trial-block div p {
  margin-bottom: 0px;
  font-size: 14px;
  font-weight: 500;
  text-transform: uppercase;
  margin-top: 4px;
  margin-bottom: -3px;
}

.trial-block div small {
  color: #edc1c7;
}
</style>
