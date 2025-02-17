<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-md-6 m-auto">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              {{ $t('Domain Request Create') }}
            </h3>
            <router-link :to="{ name: 'domain-requests.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" enctype="multipart/form-data" @submit.prevent="save" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="form-group">
                <label for="domain">{{ $t('Domain') }}
                  <span class="required">*</span></label>
                <input id="domain" v-model="form.requested_domain" type="url" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('requested_domain') }" name="domain"
                  placeholder="https://yourdomain.com" required />
                <has-error :form="form" field="requested_domain" />
              </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <v-button :loading="form.busy" class="btn btn-primary">
                <i class="fas fa-save" /> {{ $t('Save') }}
              </v-button>
              <button type="reset" class="btn btn-secondary float-right" @click="form.reset()">
                <i class="fas fa-power-off" /> {{ $t('Reset') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Form from 'vform'

export default {
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('Domain Request') }
  },
  data: () => ({
    breadcrumbsCurrent: 'Create',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Domain Requests',
        url: 'domain-requests.index',
      },
      {
        name: 'Create',
        url: '',
      },
    ],
    url: null,
    form: new Form({
      requested_domain: '',
    }),
  }),
  methods: {
    // save plan
    async save() {
      await this.form
        .post(window.location.origin + '/api/domain-requests')
        .then(({ data }) => {
          toast.fire({
            type: 'success',
            title: data.message,
          })
          this.$router.push({ name: 'domain-requests.index' })
        })
        .catch((e) => {
          if (e.response.status === 403) {
            this.$store.dispatch('operations/setSubscriptionLimitMessage', e.response.data.message)
          }

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
