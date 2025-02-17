<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">{{ $t('Create tenant') }}</h3>
            <router-link :to="{ name: 'tenants.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="save" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="company">{{
                  $t('Company')
                  }}</label>
                  <input id="company" v-model="form.company" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('company') }" name="company"
                    :placeholder="$t('Enter your company name')" />
                  <has-error :form="form" field="company" />
                </div>
                <div class="form-group col-md-6">
                  <label for="name">{{ $t('Name') }}
                    <span class="required">*</span></label>
                  <input id="name" v-model="form.name" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('name') }" name="name"
                    :placeholder="$t('Enter a name')" />
                  <has-error :form="form" field="name" />
                </div>
                <div class="form-group col-md-6">
                  <label for="domain">{{ $t('Domain') }}
                    <span class="required">*</span></label>
                  <input id="domain" v-model="form.domain" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('domain') }" name="domain"
                    :placeholder="$t('Enter your domain name')" />
                  <has-error :form="form" field="domain" />
                </div>
                <div class="form-group col-md-6">
                  <label for="email">{{ $t('Email') }}</label>
                  <input id="email" v-model="form.email" type="email" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('email') }" name="email"
                    :placeholder="$t('Enter your email address')" />
                  <has-error :form="form" field="email" />
                </div>
                <div class="form-group col-md-6">
                  <label for="password">{{ $t('Password') }}</label>
                  <input id="password" v-model="form.password" type="password" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('password') }" name="password"
                    :placeholder="$t('Enter your password')" />
                  <has-error :form="form" field="password" />
                </div>
                <div class="form-group col-md-6">
                  <label for="password_confirmation">{{ $t('Confirm your password') }}</label>
                  <input id="password_confirmation" v-model="form.password_confirmation" type="password"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('password_confirmation') }"
                    name="password_confirmation" :placeholder="$t('Confirm your password')" />
                  <has-error :form="form" field="password_confirmation" />
                </div>
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
  layout: 'central',
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('Create Tenant') }
  },
  data: () => ({
    breadcrumbsCurrent: 'Create Tenant',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Tenants',
        url: 'tenants.index',
      },
      {
        name: 'Create',
        url: '',
      },
    ],
    form: new Form({
      company: '',
      name: '',
      domain: '',
      email: '',
      password: '',
      status: 1,
    }),
    loading: true,
    url: null,
  }),
  methods: {
    // save
    async save() {
      await this.form
        .post(window.location.origin + '/api/tenants')
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('Successfully created'),
          })
          this.$router.push({ name: 'tenants.index' })
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
