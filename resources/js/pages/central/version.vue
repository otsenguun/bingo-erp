<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-md-6 m-auto">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">{{ $t('Version') }}</h3>
            <router-link :to="{ name: 'home' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <div v-if="version" class="card-body">
            <h3>Current Version: {{ version.current }}</h3>
            <div v-if="version.is_update_available">
              <h6>New Update Found: {{ version.latest }}</h6>
              <ol>
                <li>Step 1 : Upload script.zip file (manually upload the zip file in admin/uploads folder via FTP or Cpanel.)</li>
                <li>Step 2 : After upload refresh this page. You can see Install Now button.</li>
                <li>Step 3 : Click on install button. Upgrade Successfully</li>
              </ol>
            </div>
            <div v-else>
              <h6>{{ $t('No update available') }}</h6>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
export default {
  layout: 'central',
  middleware: ['auth'],
  metaInfo() {
    return { title: this.$t('Version') }
  },
  created() {
    this.getVersion()
  },
  data: () => ({
    breadcrumbsCurrent: 'Version',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Version',
        url: '',
      },
    ],
    version: null,
    loading: true,
  }),
  methods: {
    async getVersion() {
      await axios
        .get('/api/version')
        .then(({ data }) => {
          console.log(data)
          this.version = data
        })
        .catch(() => {
          toast.fire({
            type: 'error',
            title: this.$t('Opps...something went wrong'),
          })
        })
    },
  },
}
</script>

<style lang="scss" scoped>

</style>
